<?php
/**
 * One-shot migration: options avl_points → CPT avl_point.
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Read legacy options repeater rows without relying on ACF field registration.
 *
 * @return array<int, array<string, mixed>>
 */
function carte_avl_get_legacy_option_points() {
	$count = (int) get_option('options_avl_points', 0);
	if ($count <= 0) {
		if (function_exists('get_field')) {
			$rows = get_field('avl_points', 'option');
			return is_array($rows) ? $rows : array();
		}
		return array();
	}

	$rows = array();
	for ($i = 0; $i < $count; $i++) {
		$rows[] = array(
			'titre'    => (string) get_option("options_avl_points_{$i}_titre", ''),
			'type'     => (string) get_option("options_avl_points_{$i}_type", 'auteur'),
			'edition'  => (string) get_option("options_avl_points_{$i}_edition", '2025'),
			'x'        => get_option("options_avl_points_{$i}_x", 50),
			'y'        => get_option("options_avl_points_{$i}_y", 50),
			'contenu'  => (string) get_option("options_avl_points_{$i}_contenu", ''),
			'lien'     => (string) get_option("options_avl_points_{$i}_lien", ''),
		);
	}
	return $rows;
}

/**
 * Migrate repeater options to CPT posts.
 *
 * @param bool $force Re-run even if already marked done.
 * @return array{migrated:int,skipped:int,message:string}
 */
function carte_avl_migrate_points_from_options($force = false) {
	global $wpdb;

	if (!$force && get_option('carte_avl_points_migrated_v1')) {
		return array(
			'migrated' => 0,
			'skipped'  => 0,
			'message'  => 'Migration déjà effectuée.',
		);
	}

	carte_avl_seed_cohortes();

	$rows = carte_avl_get_legacy_option_points();
	if (empty($rows)) {
		update_option('carte_avl_points_migrated_v1', 1);
		return array(
			'migrated' => 0,
			'skipped'  => 0,
			'message'  => 'Aucun point options à migrer.',
		);
	}

	$migrated = 0;
	$skipped  = 0;

	foreach ($rows as $row) {
		$titre = isset($row['titre']) ? trim((string) $row['titre']) : '';
		if ($titre === '') {
			$skipped++;
			continue;
		}

		$existing_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_title = %s AND post_status != 'trash' LIMIT 1",
				'avl_point',
				$titre
			)
		);
		if ($existing_id) {
			$skipped++;
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'avl_point',
				'post_status'  => 'publish',
				'post_title'   => $titre,
				'post_content' => isset($row['contenu']) ? (string) $row['contenu'] : '',
			),
			true
		);

		if (is_wp_error($post_id) || !$post_id) {
			$skipped++;
			continue;
		}

		$type    = isset($row['type']) ? (string) $row['type'] : 'auteur';
		$edition = isset($row['edition']) ? (string) $row['edition'] : '2025';
		$x       = isset($row['x']) ? (float) $row['x'] : 50;
		$y       = isset($row['y']) ? (float) $row['y'] : 50;
		$lien    = isset($row['lien']) ? trim((string) $row['lien']) : '';

		if (function_exists('update_field')) {
			update_field('type', $type, $post_id);
			update_field('x', $x, $post_id);
			update_field('y', $y, $post_id);
			$contenu = isset($row['contenu']) ? (string) $row['contenu'] : '';
			if ($contenu !== '') {
				update_field('contenu', $contenu, $post_id);
			}
			if ($lien !== '') {
				update_field('lien', $lien, $post_id);
			}
		} else {
			update_post_meta($post_id, 'type', $type);
			update_post_meta($post_id, 'x', $x);
			update_post_meta($post_id, 'y', $y);
			$contenu = isset($row['contenu']) ? (string) $row['contenu'] : '';
			if ($contenu !== '') {
				update_post_meta($post_id, 'contenu', $contenu);
			}
			if ($lien !== '') {
				update_post_meta($post_id, 'lien', $lien);
			}
		}

		$term = get_term_by('slug', $edition, 'avl_cohorte');
		if ($term && !is_wp_error($term)) {
			wp_set_object_terms($post_id, array((int) $term->term_id), 'avl_cohorte', false);
			if (function_exists('update_field')) {
				update_field('cohorte', (int) $term->term_id, $post_id);
			}
		}

		$migrated++;
	}

	update_option('carte_avl_points_migrated_v1', 1);

	return array(
		'migrated' => $migrated,
		'skipped'  => $skipped,
		'message'  => sprintf('Migration terminée : %d créés, %d ignorés.', $migrated, $skipped),
	);
}

/**
 * Auto-run migration once on admin init.
 */
add_action('admin_init', function () {
	if (get_option('carte_avl_points_migrated_v1')) {
		return;
	}
	if (!current_user_can('edit_posts')) {
		return;
	}
	carte_avl_migrate_points_from_options();
});

/**
 * Also try on front once (Local / CLI loads).
 */
add_action('init', function () {
	if (get_option('carte_avl_points_migrated_v1')) {
		return;
	}
	// Defer until ACF + CPT ready.
}, 5);

add_action('acf/init', function () {
	if (get_option('carte_avl_points_migrated_v1')) {
		return;
	}
	carte_avl_migrate_points_from_options();
}, 30);
