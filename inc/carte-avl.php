<?php
/**
 * Carte Avez-vous lu — bootstrap.
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

define('CARTE_AVL_DIR', get_stylesheet_directory() . '/inc/carte-avl');
define('CARTE_AVL_ASSETS', get_stylesheet_directory() . '/assets/carte-avl');
define('CARTE_AVL_ASSETS_URI', get_stylesheet_directory_uri() . '/assets/carte-avl');

require_once CARTE_AVL_DIR . '/cpt.php';
require_once CARTE_AVL_DIR . '/acf.php';
require_once CARTE_AVL_DIR . '/shortcode.php';
require_once CARTE_AVL_DIR . '/migrate.php';

/**
 * Register Elementor widget when Elementor is loaded.
 */
add_action('elementor/widgets/register', function ($widgets_manager) {
	require_once CARTE_AVL_DIR . '/elementor-widget.php';
	$widgets_manager->register(new \Carte_AVL_Elementor_Widget());
});

/**
 * Enqueue assets only when the carte is rendered on the page.
 */
function carte_avl_enqueue_assets() {
	static $enqueued = false;
	if ($enqueued) {
		return;
	}
	$enqueued = true;

	$css = CARTE_AVL_ASSETS . '/css/carte-avl.css';
	$js  = CARTE_AVL_ASSETS . '/js/carte-avl.js';

	wp_enqueue_style(
		'carte-avl',
		CARTE_AVL_ASSETS_URI . '/css/carte-avl.css',
		array(),
		file_exists($css) ? (string) filemtime($css) : '1.0.0'
	);

	wp_enqueue_script(
		'carte-avl',
		CARTE_AVL_ASSETS_URI . '/js/carte-avl.js',
		array(),
		file_exists($js) ? (string) filemtime($js) : '1.0.0',
		true
	);
}

/**
 * Load manifest.json from theme assets.
 *
 * @return array{canvas: array{width:int,height:int}, layers: array}|null
 */
function carte_avl_get_manifest() {
	$path = CARTE_AVL_ASSETS . '/manifest.json';
	if (!file_exists($path)) {
		return null;
	}
	$data = json_decode((string) file_get_contents($path), true);
	if (!is_array($data) || empty($data['canvas']) || !isset($data['layers'])) {
		return null;
	}
	return $data;
}

/**
 * Read term meta via ACF or term_meta fallback.
 *
 * @param int    $term_id
 * @param string $key
 * @param mixed  $default
 * @return mixed
 */
function carte_avl_get_term_meta($term_id, $key, $default = null) {
	$term_id = (int) $term_id;
	if ($term_id <= 0) {
		return $default;
	}
	if (function_exists('get_field')) {
		$value = get_field($key, 'avl_cohorte_' . $term_id);
		if ($value !== null && $value !== false && $value !== '') {
			return $value;
		}
	}
	$meta = get_term_meta($term_id, $key, true);
	return ($meta !== '' && $meta !== false) ? $meta : $default;
}

/**
 * Cohortes for filter / legend (visible only by default).
 *
 * @param bool $visible_only
 * @return array<int, array{cle:string,libelle:string,couleur:string,ordre:int,visible:bool,term_id:int}>
 */
function carte_avl_get_categories($visible_only = true) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'avl_cohorte',
			'hide_empty' => false,
		)
	);

	if (is_wp_error($terms) || empty($terms)) {
		$fallback = array();
		foreach (carte_avl_default_cohortes() as $row) {
			$fallback[] = array(
				'cle'     => $row['slug'],
				'libelle' => $row['name'],
				'couleur' => $row['couleur'],
				'ordre'   => $row['ordre'],
				'visible' => true,
				'term_id' => 0,
			);
		}
		return $fallback;
	}

	$out = array();
	foreach ($terms as $term) {
		$visible = (bool) carte_avl_get_term_meta($term->term_id, 'visible', 1);
		if ($visible_only && !$visible) {
			continue;
		}
		$couleur = (string) carte_avl_get_term_meta($term->term_id, 'couleur', '#F5D76E');
		$ordre   = (int) carte_avl_get_term_meta($term->term_id, 'ordre', 100);
		$out[]   = array(
			'cle'     => $term->slug,
			'libelle' => $term->name,
			'couleur' => $couleur !== '' ? $couleur : '#F5D76E',
			'ordre'   => $ordre,
			'visible' => $visible,
			'term_id' => (int) $term->term_id,
		);
	}

	usort(
		$out,
		static function ($a, $b) {
			if ($a['ordre'] === $b['ordre']) {
				return strcmp($a['cle'], $b['cle']);
			}
			return $a['ordre'] <=> $b['ordre'];
		}
	);

	return $out;
}

/**
 * Cle (slug) → couleur.
 *
 * @return array<string, string>
 */
function carte_avl_edition_colors() {
	$map = array();
	foreach (carte_avl_get_categories(false) as $cat) {
		$map[ $cat['cle'] ] = $cat['couleur'];
	}
	return $map;
}

/**
 * Cle (slug) → libelle.
 *
 * @return array<string, string>
 */
function carte_avl_edition_labels() {
	$map = array();
	foreach (carte_avl_get_categories(false) as $cat) {
		$map[ $cat['cle'] ] = $cat['libelle'];
	}
	return $map;
}

/**
 * Published map points from CPT.
 *
 * @return array<int, array{id:int,titre:string,type:string,edition:string,x:float,y:float,contenu:string,lien:string}>
 */
function carte_avl_get_points() {
	$query = new WP_Query(
		array(
			'post_type'      => 'avl_point',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	$points = array();
	foreach ($query->posts as $post) {
		$type    = function_exists('get_field') ? get_field('type', $post->ID) : get_post_meta($post->ID, 'type', true);
		$x       = function_exists('get_field') ? get_field('x', $post->ID) : get_post_meta($post->ID, 'x', true);
		$y       = function_exists('get_field') ? get_field('y', $post->ID) : get_post_meta($post->ID, 'y', true);
		$lien    = function_exists('get_field') ? get_field('lien', $post->ID) : get_post_meta($post->ID, 'lien', true);
		$contenu = function_exists('get_field') ? get_field('contenu', $post->ID) : get_post_meta($post->ID, 'contenu', true);
		if (!is_string($contenu) || $contenu === '') {
			$contenu = (string) $post->post_content;
		}

		$terms   = get_the_terms($post->ID, 'avl_cohorte');
		$edition = '2025';
		if (is_array($terms) && !empty($terms) && !is_wp_error($terms)) {
			$edition = $terms[0]->slug;
		}

		$points[] = array(
			'id'      => (int) $post->ID,
			'titre'   => get_the_title($post),
			'type'    => is_string($type) && $type !== '' ? $type : 'auteur',
			'edition' => $edition,
			'x'       => is_numeric($x) ? (float) $x : 50.0,
			'y'       => is_numeric($y) ? (float) $y : 50.0,
			'contenu' => is_string($contenu) ? $contenu : '',
			'lien'    => is_string($lien) ? trim($lien) : '',
		);
	}

	wp_reset_postdata();
	return $points;
}

/**
 * Partner event icon URL from options (empty = default SVG).
 *
 * @return string
 */
function carte_avl_get_partner_icon_url() {
	if (!function_exists('get_field')) {
		return '';
	}
	$url = get_field('avl_icone_partenaire', 'option');
	if (is_array($url) && isset($url['url'])) {
		$url = $url['url'];
	}
	$url = is_string($url) ? trim($url) : '';
	return $url !== '' ? $url : '';
}

/**
 * UI strings from options.
 *
 * @return array{filtre_titre:string,filtre_toutes:string,legende_partenaire:string}
 */
function carte_avl_get_ui_strings() {
	$filtre_titre  = function_exists('get_field') ? get_field('avl_filtre_titre', 'option') : null;
	$filtre_toutes = function_exists('get_field') ? get_field('avl_filtre_toutes', 'option') : null;
	$legende       = function_exists('get_field') ? get_field('avl_legende_partenaire', 'option') : null;

	return array(
		'filtre_titre'       => (is_string($filtre_titre) && $filtre_titre !== '') ? $filtre_titre : 'Éditions',
		'filtre_toutes'      => (is_string($filtre_toutes) && $filtre_toutes !== '') ? $filtre_toutes : 'Toutes les années',
		'legende_partenaire' => (is_string($legende) && $legende !== '') ? $legende : 'Événement partenaire',
	);
}

/**
 * Render the carte markup (shared by shortcode + Elementor widget).
 *
 * @return string
 */
function carte_avl_render() {
	carte_avl_enqueue_assets();
	ob_start();
	include CARTE_AVL_DIR . '/template.php';
	return (string) ob_get_clean();
}
