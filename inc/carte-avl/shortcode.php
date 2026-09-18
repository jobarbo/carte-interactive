<?php
/**
 * Shortcode [carte_avl]
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

add_shortcode('carte_avl', function () {
	return carte_avl_render();
});
