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

require_once CARTE_AVL_DIR . '/acf.php';
require_once CARTE_AVL_DIR . '/shortcode.php';

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
 * Edition → CSS color class mapping.
 *
 * @return array<string, string>
 */
function carte_avl_edition_colors() {
	return array(
		'2025' => '#F5D76E',
		'2026' => '#5BB8B8',
		'2027' => '#E89BB5',
		'2028' => '#C45C5C',
	);
}

/**
 * Edition labels for legend.
 *
 * @return array<string, string>
 */
function carte_avl_edition_labels() {
	return array(
		'2025' => '2025',
		'2026' => '2026',
		'2027' => '2027 — À venir',
		'2028' => '2028 — À venir',
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
