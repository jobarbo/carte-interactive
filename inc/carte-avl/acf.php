<?php
/**
 * ACF field groups for Carte AVL (CPT points, cohort terms, options appearance).
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
 * Options page (appearance only).
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
		'icon_url'   => 'dashicons-admin-generic',
		'position'   => 58,
	));
});

/**
 * Register field groups.
 */
add_action('acf/init', function () {
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	// —— Fields on avl_point (2 tabs) ——
	acf_add_local_field_group(array(
		'key'    => 'group_avl_point',
		'title'  => 'Point carte',
		'fields' => array(
			array(
				'key'       => 'field_avl_tab_point',
				'label'     => 'Point / carte',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
				'endpoint'  => 0,
			),
			array(
				'key'           => 'field_avl_point_type',
				'label'         => 'Type',
				'name'          => 'type',
				'type'          => 'select',
				'choices'       => array(
					'auteur'    => 'Auteur·e (épingle)',
					'evenement' => 'Événement partenaire (drapeau)',
				),
				'default_value' => 'auteur',
				'required'      => 1,
				'wrapper'       => array('width' => '33', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_point_cohorte',
				'label'         => 'Cohorte',
				'name'          => 'cohorte',
				'type'          => 'taxonomy',
				'taxonomy'      => 'avl_cohorte',
				'field_type'    => 'select',
				'allow_null'    => 0,
				'add_term'      => 0,
				'save_terms'    => 1,
				'load_terms'    => 1,
				'return_format' => 'id',
				'multiple'      => 0,
				'required'      => 1,
				'wrapper'       => array('width' => '33', 'class' => '', 'id' => ''),
			),
			array(
				'key'          => 'field_avl_point_lien',
				'label'        => 'Lien « En savoir plus »',
				'name'         => 'lien',
				'type'         => 'url',
				'instructions' => 'Optionnel. Lien externe affiché dans le tooltip.',
				'required'     => 0,
				'wrapper'      => array('width' => '34', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_point_x',
				'label'         => 'Coordonnée X (%)',
				'name'          => 'x',
				'type'          => 'number',
				'instructions'  => '0 = gauche, 100 = droite.',
				'min'           => 0,
				'max'           => 100,
				'step'          => 0.1,
				'default_value' => 50,
				'required'      => 1,
				'wrapper'       => array('width' => '50', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_point_y',
				'label'         => 'Coordonnée Y (%)',
				'name'          => 'y',
				'type'          => 'number',
				'instructions'  => '0 = haut, 100 = bas.',
				'min'           => 0,
				'max'           => 100,
				'step'          => 0.1,
				'default_value' => 50,
				'required'      => 1,
				'wrapper'       => array('width' => '50', 'class' => '', 'id' => ''),
			),
			array(
				'key'       => 'field_avl_tab_contenu',
				'label'     => 'Contenu texte',
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
				'endpoint'  => 0,
			),
			// Initialize automatically; delay=1 leaves the editor waiting for a click.
			array(
				'key'          => 'field_avl_point_contenu',
				'label'        => 'Contenu du tooltip',
				'name'         => 'contenu',
				'type'         => 'wysiwyg',
				'tabs'         => 'all',
				'toolbar'      => 'full',
				'media_upload' => 1,
				'delay'        => 0,
				'instructions' => 'Texte, images et liens. Pour une vidéo : coller une URL YouTube ou Vimeo sur sa propre ligne.',
				'required'     => 0,
				'wrapper'      => array('width' => '', 'class' => '', 'id' => ''),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'avl_point',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
		'show_in_rest'          => 0,
	));

	// —— Fields on avl_cohorte term ——
	acf_add_local_field_group(array(
		'key'    => 'group_avl_cohorte',
		'title'  => 'Cohorte — affichage',
		'fields' => array(
			array(
				'key'           => 'field_avl_cohorte_couleur',
				'label'         => 'Couleur',
				'name'          => 'couleur',
				'type'          => 'color_picker',
				'default_value' => '#F5D76E',
				'required'      => 1,
				'wrapper'       => array('width' => '33', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_cohorte_ordre',
				'label'         => 'Ordre',
				'name'          => 'ordre',
				'type'          => 'number',
				'instructions'  => 'Ordre dans le filtre et la légende (plus petit = plus haut).',
				'default_value' => 10,
				'required'      => 1,
				'wrapper'       => array('width' => '33', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_cohorte_visible',
				'label'         => 'Visible',
				'name'          => 'visible',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
				'wrapper'       => array('width' => '34', 'class' => '', 'id' => ''),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'taxonomy',
					'operator' => '==',
					'value'    => 'avl_cohorte',
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

	// —— Options: appearance only ——
	acf_add_local_field_group(array(
		'key'    => 'group_carte_avl',
		'title'  => 'Carte Avez-vous lu — Apparence',
		'fields' => array(
			array(
				'key'           => 'field_avl_filtre_titre',
				'label'         => 'Titre du filtre',
				'name'          => 'avl_filtre_titre',
				'type'          => 'text',
				'default_value' => 'Éditions',
				'wrapper'       => array('width' => '33', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_filtre_toutes',
				'label'         => 'Libellé « tout afficher »',
				'name'          => 'avl_filtre_toutes',
				'type'          => 'text',
				'default_value' => 'Toutes les années',
				'wrapper'       => array('width' => '33', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_legende_partenaire',
				'label'         => 'Libellé légende partenaire',
				'name'          => 'avl_legende_partenaire',
				'type'          => 'text',
				'default_value' => 'Événement partenaire',
				'wrapper'       => array('width' => '34', 'class' => '', 'id' => ''),
			),
			array(
				'key'           => 'field_avl_icone_partenaire',
				'label'         => 'Icône événement partenaire',
				'name'          => 'avl_icone_partenaire',
				'type'          => 'image',
				'return_format' => 'url',
				'preview_size'  => 'thumbnail',
				'library'       => 'all',
				'mime_types'    => 'png,svg,jpg,jpeg,webp',
				'instructions'  => 'PNG ou SVG, fond transparent. Utilisée sur la carte et dans la légende. Si vide : drapeau SVG par défaut.',
			),
		),
		'location' => array(
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
