<?php
/**
 * Geocoding + projection for Carte AVL points (postal / lat-lng → x/y %).
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Normalize a Canadian postal code to "A1A 1A1".
 *
 * @param string $postal
 * @return string Empty if invalid.
 */
function carte_avl_normalize_postal($postal) {
	$raw = strtoupper(preg_replace('/\s+/', '', (string) $postal));
	if (!preg_match('/^[ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z]\d[ABCEGHJ-NPRSTV-Z]\d$/', $raw)) {
		return '';
	}
	return substr($raw, 0, 3) . ' ' . substr($raw, 3, 3);
}

/**
 * Whether a string is a valid Canadian postal code.
 *
 * @param string $postal
 * @return bool
 */
function carte_avl_is_valid_ca_postal($postal) {
	return carte_avl_normalize_postal($postal) !== '';
}

/**
 * Map illustration geographic bounds (Canada defaults).
 *
 * @return array{north:float,south:float,west:float,east:float}
 */
function carte_avl_get_map_bounds() {
	$defaults = array(
		'north' => 83.0,
		'south' => 41.7,
		'west'  => -141.0,
		'east'  => -52.6,
	);

	$north = function_exists('get_field') ? get_field('avl_bound_north', 'option') : null;
	$south = function_exists('get_field') ? get_field('avl_bound_south', 'option') : null;
	$west  = function_exists('get_field') ? get_field('avl_bound_west', 'option') : null;
	$east  = function_exists('get_field') ? get_field('avl_bound_east', 'option') : null;

	$bounds = array(
		'north' => is_numeric($north) ? (float) $north : $defaults['north'],
		'south' => is_numeric($south) ? (float) $south : $defaults['south'],
		'west'  => is_numeric($west) ? (float) $west : $defaults['west'],
		'east'  => is_numeric($east) ? (float) $east : $defaults['east'],
	);

	if ($bounds['north'] <= $bounds['south'] || $bounds['east'] <= $bounds['west']) {
		return $defaults;
	}

	return $bounds;
}

/**
 * Project lat/lng onto the illustrated map as percentages.
 *
 * @param float $lat
 * @param float $lng
 * @return array{x:float,y:float}|null
 */
function carte_avl_project_lat_lng($lat, $lng) {
	if (!is_numeric($lat) || !is_numeric($lng)) {
		return null;
	}
	$lat = (float) $lat;
	$lng = (float) $lng;
	if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
		return null;
	}

	$b     = carte_avl_get_map_bounds();
	$x_raw = ($lng - $b['west']) / ($b['east'] - $b['west']) * 100;
	$y_raw = ($b['north'] - $lat) / ($b['north'] - $b['south']) * 100;

	return array(
		'x' => max(0.0, min(100.0, round($x_raw, 2))),
		'y' => max(0.0, min(100.0, round($y_raw, 2))),
	);
}

/**
 * Geocode a Canadian postal code (cached 7 days).
 *
 * Uses Zippopotam FSA (first 3 characters) — Nominatim is unreliable for CA postals.
 *
 * @param string $postal Already normalized or raw.
 * @return array{lat:float,lng:float}|null
 */
function carte_avl_geocode_postal($postal) {
	$normalized = carte_avl_normalize_postal($postal);
	if ($normalized === '') {
		return null;
	}

	$cache_key = 'avl_geo_v2_' . md5($normalized);
	$cached    = get_transient($cache_key);
	if (is_array($cached) && isset($cached['lat'], $cached['lng'])) {
		return array(
			'lat' => (float) $cached['lat'],
			'lng' => (float) $cached['lng'],
		);
	}

	$fsa = substr(str_replace(' ', '', $normalized), 0, 3);
	$url = 'https://api.zippopotam.us/ca/' . rawurlencode($fsa);

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 12,
			'headers' => array(
				'Accept'     => 'application/json',
				'User-Agent' => 'RhizomeCarteAVL/' . (home_url() ?: 'wordpress') . ' (WordPress)',
			),
		)
	);

	if (is_wp_error($response)) {
		return null;
	}

	$code = (int) wp_remote_retrieve_response_code($response);
	$body = json_decode((string) wp_remote_retrieve_body($response), true);
	if (
		$code !== 200
		|| !is_array($body)
		|| empty($body['places'][0]['latitude'])
		|| empty($body['places'][0]['longitude'])
	) {
		return null;
	}

	$result = array(
		'lat' => (float) $body['places'][0]['latitude'],
		'lng' => (float) $body['places'][0]['longitude'],
	);

	set_transient($cache_key, $result, WEEK_IN_SECONDS);
	return $result;
}

/**
 * Queue an admin notice for the current user (shown once).
 *
 * @param string $message
 * @param string $type success|error|warning|info
 */
function carte_avl_queue_admin_notice($message, $type = 'error') {
	$user_id = get_current_user_id();
	if ($user_id <= 0) {
		return;
	}
	set_transient(
		'carte_avl_admin_notice_' . $user_id,
		array(
			'message' => (string) $message,
			'type'    => (string) $type,
		),
		MINUTE_IN_SECONDS * 5
	);
}

/**
 * Display queued geocode / projection notices.
 */
add_action('admin_notices', function () {
	$user_id = get_current_user_id();
	if ($user_id <= 0) {
		return;
	}
	$key  = 'carte_avl_admin_notice_' . $user_id;
	$data = get_transient($key);
	if (!is_array($data) || empty($data['message'])) {
		return;
	}
	delete_transient($key);
	$type = isset($data['type']) ? sanitize_key($data['type']) : 'error';
	if (!in_array($type, array('success', 'error', 'warning', 'info'), true)) {
		$type = 'error';
	}
	printf(
		'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
		esc_attr($type),
		esc_html($data['message'])
	);
});

/**
 * After ACF saves an avl_point: if it has never been placed, use the postal code
 * for a first position (else the centre). A position already set is never overwritten.
 *
 * Priority 20 so ACF field values are already stored.
 *
 * @param int|string $post_id
 */
add_action('acf/save_post', function ($post_id) {
	if (!is_numeric($post_id)) {
		return;
	}
	$post_id = (int) $post_id;
	if ($post_id <= 0 || get_post_type($post_id) !== 'avl_point') {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// A position already set (picker or earlier save) wins: never overwrite a manual adjustment.
	if (is_numeric(get_post_meta($post_id, 'x', true)) && is_numeric(get_post_meta($post_id, 'y', true))) {
		return;
	}

	$save_xy = static function ($x, $y) use ($post_id) {
		if (function_exists('update_field')) {
			update_field('field_avl_point_x', $x, $post_id);
			update_field('field_avl_point_y', $y, $post_id);
		} else {
			update_post_meta($post_id, 'x', $x);
			update_post_meta($post_id, 'y', $y);
		}
	};

	$postal_raw = function_exists('get_field') ? get_field('code_postal', $post_id) : get_post_meta($post_id, 'code_postal', true);
	$postal_raw = is_string($postal_raw) ? trim($postal_raw) : '';

	$xy = null;
	if ($postal_raw !== '') {
		$normalized = carte_avl_normalize_postal($postal_raw);
		if ($normalized === '') {
			carte_avl_queue_admin_notice(
				'Code postal invalide (format attendu : A1A 1A1). Le point a été placé au centre : déplacez-le sur la carte.',
				'error'
			);
		} else {
			if (function_exists('update_field')) {
				update_field('field_avl_point_code_postal', $normalized, $post_id);
			} else {
				update_post_meta($post_id, 'code_postal', $normalized);
			}
			$coords = carte_avl_geocode_postal($normalized);
			$xy     = $coords ? carte_avl_project_lat_lng($coords['lat'], $coords['lng']) : null;
			if ($xy === null) {
				carte_avl_queue_admin_notice(
					'Impossible de localiser le code postal « ' . $normalized . ' ». Le point a été placé au centre : déplacez-le sur la carte.',
					'error'
				);
			}
		}
	}

	if ($xy === null) {
		$save_xy(50, 50);
		return;
	}

	$save_xy($xy['x'], $xy['y']);
	carte_avl_queue_admin_notice(
		sprintf('Position placée depuis le code postal : %.2f %% / %.2f %%. Ajustez-la sur la carte au besoin.', $xy['x'], $xy['y']),
		'success'
	);
}, 20);
