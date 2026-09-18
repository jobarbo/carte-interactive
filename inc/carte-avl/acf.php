<?php
/**
 * ACF options page + field group for Carte AVL.
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Save/load ACF JSON in the child theme.
 */
add_filter('acf/settings/save_json', function ($path) {
	return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
});

/**
 * Options page.
 */
add_action('acf/init', function () {
	if (!function_exists('acf_add_options_page')) {
		return;
	}

	acf_add_options_page(array(
		'page_title' => 'Carte Avez-vous lu',
		'menu_title' => 'Carte AVL',
		'menu_slug'  => 'carte-avl',
		'capability' => 'edit_posts',
		'redirect'   => false,
		'icon_url'   => 'dashicons-location-alt',
		'position'   => 58,
	));
});

/**
 * Register field group in PHP (also mirrored in acf-json for sync).
 */
add_action('acf/init', function () {
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_local_field_group(array(
		'key'                   => 'group_carte_avl',
		'title'                 => 'Carte Avez-vous lu — Points',
		'fields'                => array(
			array(
				'key'          => 'field_avl_points',
				'label'        => 'Points géographiques',
				'name'         => 'avl_points',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Ajouter un point',
				'instructions' => 'Une personne = un point (ville de résidence). Les événements partenaires utilisent le type drapeau. Couleur = cohorte / année. Environ 3–4 mises à jour par année via ce panneau. Dans le contenu : texte, images, et URL YouTube/Vimeo pour les vidéos.',
				'sub_fields'   => array(
					array(
						'key'      => 'field_avl_titre',
						'label'    => 'Titre',
						'name'     => 'titre',
						'type'     => 'text',
						'required' => 1,
					),
					array(
						'key'           => 'field_avl_type',
						'label'         => 'Type',
						'name'          => 'type',
						'type'          => 'select',
						'choices'       => array(
							'auteur'    => 'Auteur·e (épingle)',
							'evenement' => 'Événement partenaire (drapeau)',
						),
						'default_value' => 'auteur',
						'required'      => 1,
					),
					array(
						'key'           => 'field_avl_edition',
						'label'         => 'Édition / année',
						'name'          => 'edition',
						'type'          => 'select',
						'choices'       => array(
							'2025' => '2025',
							'2026' => '2026',
							'2027' => '2027 — À venir',
							'2028' => '2028 — À venir',
						),
						'default_value' => '2025',
						'required'      => 1,
					),
					array(
						'key'           => 'field_avl_x',
						'label'         => 'Coordonnée X (%)',
						'name'          => 'x',
						'type'          => 'number',
						'instructions'  => 'Position horizontale en % du cadre (0 = gauche, 100 = droite).',
						'min'           => 0,
						'max'           => 100,
						'step'          => 0.1,
						'default_value' => 50,
						'required'      => 1,
					),
					array(
						'key'           => 'field_avl_y',
						'label'         => 'Coordonnée Y (%)',
						'name'          => 'y',
						'type'          => 'number',
						'instructions'  => 'Position verticale en % du cadre (0 = haut, 100 = bas).',
						'min'           => 0,
						'max'           => 100,
						'step'          => 0.1,
						'default_value' => 50,
						'required'      => 1,
					),
					array(
						'key'          => 'field_avl_contenu',
						'label'        => 'Contenu du tooltip',
						'name'         => 'contenu',
						'type'         => 'wysiwyg',
						'tabs'         => 'all',
						'toolbar'      => 'full',
						'media_upload' => 1,
						'instructions' => 'Texte, images et liens. Pour une vidéo : coller une URL YouTube ou Vimeo sur sa propre ligne.',
					),
					array(
						'key'          => 'field_avl_lien',
						'label'        => 'Lien vers une page dédiée',
						'name'         => 'lien',
						'type'         => 'url',
						'instructions' => 'Optionnel. Affiche un bouton « En savoir plus » dans le tooltip.',
						'required'     => 0,
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'carte-avl',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
		'show_in_rest'          => 0,
	));
});
