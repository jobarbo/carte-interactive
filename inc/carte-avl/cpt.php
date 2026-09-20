<?php
/**
 * CPT avl_point + taxonomy avl_cohorte.
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Register post type and taxonomy.
 */
add_action('init', function () {
	register_taxonomy(
		'avl_cohorte',
		'avl_point',
		array(
			'labels'            => array(
				'name'          => 'Cohortes',
				'singular_name' => 'Cohorte',
				'search_items'  => 'Rechercher des cohortes',
				'all_items'     => 'Toutes les cohortes',
				'edit_item'     => 'Modifier la cohorte',
				'update_item'   => 'Mettre à jour la cohorte',
				'add_new_item'  => 'Ajouter une cohorte',
				'new_item_name' => 'Nouvelle cohorte',
				'menu_name'     => 'Cohortes',
			),
			'public'            => true,
			'publicly_queryable'=> true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'rewrite'           => array('slug' => 'cohorte'),
		)
	);

	register_post_type(
		'avl_point',
		array(
			'labels'              => array(
				'name'               => 'Points carte',
				'singular_name'      => 'Point carte',
				'add_new'            => 'Ajouter',
				'add_new_item'       => 'Ajouter un point',
				'edit_item'          => 'Modifier le point',
				'new_item'           => 'Nouveau point',
				'view_item'          => 'Voir le point',
				'search_items'       => 'Rechercher des points',
				'not_found'          => 'Aucun point trouvé',
				'not_found_in_trash' => 'Aucun point dans la corbeille',
				'menu_name'          => 'Points carte',
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-location-alt',
			'menu_position'       => 57,
			// editor = required by ACF WYSIWYG TinyMCE; native box is hidden (see below).
			'supports'            => array('title', 'editor'),
			'rewrite'             => array('slug' => 'point-carte'),
			'taxonomies'          => array('avl_cohorte'),
		)
	);
});

/**
 * Force classic / no block editor for map points (ACF only).
 */
add_filter('use_block_editor_for_post_type', function ($use, $post_type) {
	if ($post_type === 'avl_point') {
		return false;
	}
	return $use;
}, 10, 2);

/**
 * Hide the native editor and keep the ACF content editor visible.
 */
add_action('admin_head', function () {
	$screen = function_exists('get_current_screen') ? get_current_screen() : null;
	if (!$screen || $screen->post_type !== 'avl_point') {
		return;
	}
	?>
	<style id="carte-avl-editor-visibility">
		#postdivrich {
			display: none !important;
		}
		/* Override editor-hiding CSS without changing ACF tab visibility. */
		#acf-group_avl_point [data-key="field_avl_point_contenu"] > .acf-input > .acf-editor-wrap {
			display: block !important;
		}
	</style>
	<?php
});

/**
 * Ensure editor/media assets for ACF WYSIWYG.
 */
add_action('acf/input/admin_enqueue_scripts', function () {
	$screen = function_exists('get_current_screen') ? get_current_screen() : null;
	if (!$screen || $screen->post_type !== 'avl_point') {
		return;
	}
	wp_enqueue_editor();
	wp_enqueue_media();
});
/**
 * Default cohortes to seed.
 *
 * @return array<int, array{slug:string,name:string,couleur:string,ordre:int,visible:int}>
 */
function carte_avl_default_cohortes() {
	return array(
		array(
			'slug'    => '2025',
			'name'    => '2025',
			'couleur' => '#F5D76E',
			'ordre'   => 10,
			'visible' => 1,
		),
		array(
			'slug'    => '2026',
			'name'    => '2026',
			'couleur' => '#5BB8B8',
			'ordre'   => 20,
			'visible' => 1,
		),
		array(
			'slug'    => '2027',
			'name'    => '2027 — À venir',
			'couleur' => '#E89BB5',
			'ordre'   => 30,
			'visible' => 1,
		),
		array(
			'slug'    => '2028',
			'name'    => '2028 — À venir',
			'couleur' => '#C45C5C',
			'ordre'   => 40,
			'visible' => 1,
		),
	);
}

/**
 * Ensure default cohort terms exist (idempotent).
 */
function carte_avl_seed_cohortes() {
	if (!taxonomy_exists('avl_cohorte')) {
		return;
	}

	foreach (carte_avl_default_cohortes() as $row) {
		$existing = get_term_by('slug', $row['slug'], 'avl_cohorte');
		if ($existing && !is_wp_error($existing)) {
			$term_id = (int) $existing->term_id;
		} else {
			$result = wp_insert_term(
				$row['name'],
				'avl_cohorte',
				array('slug' => $row['slug'])
			);
			if (is_wp_error($result)) {
				continue;
			}
			$term_id = (int) $result['term_id'];
		}

		if (function_exists('update_field')) {
			update_field('couleur', $row['couleur'], 'avl_cohorte_' . $term_id);
			update_field('ordre', $row['ordre'], 'avl_cohorte_' . $term_id);
			update_field('visible', $row['visible'], 'avl_cohorte_' . $term_id);
		} else {
			update_term_meta($term_id, 'couleur', $row['couleur']);
			update_term_meta($term_id, 'ordre', $row['ordre']);
			update_term_meta($term_id, 'visible', $row['visible']);
		}
	}
}

add_action('init', function () {
	carte_avl_seed_cohortes();
}, 20);

/**
 * Flush rewrite rules once after CPT registration.
 */
add_action('after_switch_theme', function () {
	flush_rewrite_rules();
});

add_action('init', function () {
	if (get_option('carte_avl_flush_rewrite_v1')) {
		return;
	}
	flush_rewrite_rules(false);
	update_option('carte_avl_flush_rewrite_v1', 1);
}, 99);

/**
 * Hide native taxonomy box (ACF taxonomy field is the UI).
 */
add_action('admin_menu', function () {
	remove_meta_box('tagsdiv-avl_cohorte', 'avl_point', 'side');
});
