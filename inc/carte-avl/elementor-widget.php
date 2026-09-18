<?php
/**
 * Elementor widget: Carte Avez-vous lu
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

class Carte_AVL_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'carte-avl';
	}

	public function get_title() {
		return 'Carte Avez-vous lu';
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_categories() {
		return array('general');
	}

	public function get_keywords() {
		return array('carte', 'map', 'avl', 'rhizome', 'avez-vous-lu');
	}

	protected function register_controls() {
		$this->start_controls_section('section_content', array(
			'label' => 'Carte',
		));

		$this->add_control('info', array(
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'raw'  => '<p>Les points se gèrent dans <strong>Carte AVL</strong> (page d’options ACF). Ce widget affiche la carte illustrée et les marqueurs.</p>',
		));

		$this->end_controls_section();
	}

	protected function render() {
		echo carte_avl_render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	protected function content_template() {
		?>
		<div style="padding:2rem;background:#F4DDD2;border:2px dashed #1a3a6b;text-align:center;">
			<strong>Carte Avez-vous lu</strong><br>
			<small>Aperçu côté front uniquement — points via ACF Options</small>
		</div>
		<?php
	}
}
