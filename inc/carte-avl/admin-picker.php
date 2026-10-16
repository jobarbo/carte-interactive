<?php
/**
 * Admin map picker for avl_point: click / drag a pin on the illustrated map.
 *
 * x / y stay stored as plain 0–100 % post meta (read by the front end);
 * the picker only fills those two number inputs.
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * x / y: hidden (the picker drives them), no forced 50/50 default so we can
 * tell "never placed" from "placed in the centre".
 */
foreach (array('x', 'y') as $avl_axis) {
	// load_field (not prepare_field) so the change also applies to save-time validation.
	add_filter('acf/load_field/name=' . $avl_axis, function ($field) {
		if (!isset($field['key']) || strpos($field['key'], 'field_avl_point_') !== 0) {
			return $field;
		}
		$field['default_value']     = '';
		$field['required']          = 0;
		$field['step']              = 'any'; // picker writes 2 decimals; step 0.1 fails browser validation.
		$field['readonly']          = 0;
		$field['instructions']      = '';
		$field['wrapper']['width']  = '';
		$field['wrapper']['class'] .= ' avl-hidden-field';
		return $field;
	});
}

/**
 * Enqueue picker assets on the avl_point edit screen only.
 */
add_action('admin_enqueue_scripts', function ($hook) {
	if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
		return;
	}
	$screen = get_current_screen();
	if (!$screen || $screen->post_type !== 'avl_point') {
		return;
	}

	$ver = wp_get_theme()->get('Version');
	wp_enqueue_style('carte-avl-admin-picker', CARTE_AVL_ASSETS_URI . '/css/admin-picker.css', array(), $ver);
	wp_enqueue_script('carte-avl-admin-picker', CARTE_AVL_ASSETS_URI . '/js/admin-picker.js', array('jquery'), $ver, true);
	wp_localize_script('carte-avl-admin-picker', 'carteAvlPicker', array(
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce'   => wp_create_nonce('carte_avl_geocode'),
		'image'   => CARTE_AVL_ASSETS_URI . '/layers/map-sources-psd.webp',
	));
});

/**
 * AJAX: postal code → suggested x / y (does not save anything).
 */
add_action('wp_ajax_carte_avl_geocode', function () {
	check_ajax_referer('carte_avl_geocode', 'nonce');
	if (!current_user_can('edit_posts')) {
		wp_send_json_error(array('message' => 'Accès refusé.'), 403);
	}

	$postal = carte_avl_normalize_postal(isset($_POST['postal']) ? wp_unslash($_POST['postal']) : '');
	if ($postal === '') {
		wp_send_json_error(array('message' => 'Code postal invalide. Format attendu : A1A 1A1.'));
	}

	$coords = carte_avl_geocode_postal($postal);
	$xy     = $coords ? carte_avl_project_lat_lng($coords['lat'], $coords['lng']) : null;
	if ($xy === null) {
		wp_send_json_error(array('message' => 'Impossible de localiser ce code postal. Cliquez directement sur la carte.'));
	}

	wp_send_json_success(array('postal' => $postal, 'x' => $xy['x'], 'y' => $xy['y']));
});
