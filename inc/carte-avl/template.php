<?php
/**
 * Front template: layered map + CPT pins + legends + tooltip.
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

$manifest       = carte_avl_get_manifest();
$points         = carte_avl_get_points();
$categories     = carte_avl_get_categories(true);
$edition_colors = carte_avl_edition_colors();
$ui             = carte_avl_get_ui_strings();
$partner_icon   = carte_avl_get_partner_icon_url();

$cw     = $manifest ? (int) $manifest['canvas']['width'] : 1920;
$ch     = $manifest ? (int) $manifest['canvas']['height'] : 1440;
$layers = $manifest && isset($manifest['layers']) ? $manifest['layers'] : array();
$max_z  = 0;
foreach ($layers as $layer) {
	$max_z = max($max_z, (int) ($layer['zIndex'] ?? 0));
}
$pins_z = $max_z + 10;

$carte_avl_render_partner_flag = static function ($width = 28, $height = 36) {
	?>
	<svg viewBox="0 0 24 32" width="<?php echo (int) $width; ?>" height="<?php echo (int) $height; ?>" focusable="false" aria-hidden="true">
		<path fill="#1a3a6b" d="M4 2v28M4 2h14l-3 5 3 5H4"/>
	</svg>
	<?php
};
?>
<div class="carte-avl" data-carte-avl>
	<div
		class="carte-avl__scene"
		style="aspect-ratio: <?php echo esc_attr($cw . ' / ' . $ch); ?>;"
	>
		<?php if (!empty($layers)) : ?>
			<div class="carte-avl__layers" aria-hidden="true">
				<?php foreach ($layers as $layer) :
					$name    = sanitize_html_class($layer['name'] ?? 'layer');
					$file    = isset($layer['file']) ? (string) $layer['file'] : '';
					$pos     = isset($layer['position']) && is_array($layer['position']) ? $layer['position'] : array();
					$z       = (int) ($layer['zIndex'] ?? 0);
					$opacity = isset($layer['opacity']) ? (float) $layer['opacity'] : 1;
					$blend   = isset($layer['blendMode']) ? (string) $layer['blendMode'] : 'normal';
					$clipped = !empty($layer['clipped']);
					if ($clipped || $file === '') {
						continue;
					}
					$src   = CARTE_AVL_ASSETS_URI . '/layers/' . rawurlencode($file);
					$style = sprintf(
						'--layer-center-left:%s%%;--layer-center-top:%s%%;--layer-width:%s%%;--layer-height:%s%%;--layer-opacity:%s;--layer-blend:%s;z-index:%d;',
						esc_attr((string) ($pos['centerLeft'] ?? 50)),
						esc_attr((string) ($pos['centerTop'] ?? 50)),
						esc_attr((string) ($pos['width'] ?? 10)),
						esc_attr((string) ($pos['height'] ?? 10)),
						esc_attr((string) $opacity),
						esc_attr($blend),
						$z
					);
					?>
					<img
						class="carte-avl__layer carte-avl__layer--<?php echo esc_attr($name); ?>"
						src="<?php echo esc_url($src); ?>"
						alt=""
						draggable="false"
						style="<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — values escaped above ?>"
					>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="carte-avl__missing">
				<p>Manifest des calques introuvable. Lancez l’export PSD.</p>
			</div>
		<?php endif; ?>

		<div class="carte-avl__editions" aria-label="<?php echo esc_attr($ui['filtre_titre']); ?>">
			<p class="carte-avl__editions-title"><?php echo esc_html($ui['filtre_titre']); ?></p>
			<ul class="carte-avl__editions-list">
				<?php foreach ($categories as $cat) : ?>
					<li>
						<button
							type="button"
							class="carte-avl__filter"
							data-edition="<?php echo esc_attr($cat['cle']); ?>"
							aria-pressed="false"
						>
							<span class="carte-avl__swatch" style="background:<?php echo esc_attr($cat['couleur']); ?>;"></span>
							<span><?php echo esc_html($cat['libelle']); ?></span>
						</button>
					</li>
				<?php endforeach; ?>
				<li>
					<button
						type="button"
						class="carte-avl__filter carte-avl__filter--all is-active"
						data-edition="all"
						aria-pressed="true"
					>
						<span class="carte-avl__swatch carte-avl__swatch--all"></span>
						<span><?php echo esc_html($ui['filtre_toutes']); ?></span>
					</button>
				</li>
			</ul>
		</div>

		<div class="carte-avl__pins" style="z-index:<?php echo (int) $pins_z; ?>;">
			<?php foreach ($points as $point) :
				$titre   = $point['titre'];
				$type    = $point['type'];
				$edition = $point['edition'];
				$x       = $point['x'];
				$y       = $point['y'];
				$contenu = $point['contenu'];
				$lien    = $point['lien'];
				$color   = $edition_colors[ $edition ] ?? '#F5D76E';
				$is_flag = ($type === 'evenement');
				$pin_id  = 'avl-pin-' . (int) $point['id'];
				?>
				<button
					type="button"
					class="carte-avl__pin <?php echo $is_flag ? 'carte-avl__pin--flag' : 'carte-avl__pin--auteur'; ?>"
					style="left:<?php echo esc_attr((string) $x); ?>%;top:<?php echo esc_attr((string) $y); ?>%;--pin-color:<?php echo esc_attr($color); ?>;"
					data-edition="<?php echo esc_attr($edition); ?>"
					data-pin-id="<?php echo esc_attr($pin_id); ?>"
					aria-expanded="false"
					aria-controls="<?php echo esc_attr($pin_id); ?>-tip"
				>
					<?php if ($is_flag) : ?>
						<span class="carte-avl__flag-icon" aria-hidden="true">
							<?php if ($partner_icon !== '') : ?>
								<img src="<?php echo esc_url($partner_icon); ?>" alt="" width="28" height="36" draggable="false">
							<?php else : ?>
								<?php $carte_avl_render_partner_flag(28, 36); ?>
							<?php endif; ?>
						</span>
					<?php else : ?>
						<span class="carte-avl__pin-icon" aria-hidden="true">
							<svg viewBox="0 0 24 32" width="28" height="36" focusable="false">
								<path fill="var(--pin-color)" stroke="#1a3a6b" stroke-width="1.2" d="M12 1C6.5 1 2 5.5 2 11c0 7.5 10 19 10 19s10-11.5 10-19C22 5.5 17.5 1 12 1z"/>
								<circle cx="12" cy="11" r="3.5" fill="#fff"/>
							</svg>
						</span>
					<?php endif; ?>
					<span class="screen-reader-text"><?php echo esc_html($titre); ?></span>
				</button>

				<div
					id="<?php echo esc_attr($pin_id); ?>-tip"
					class="carte-avl__tooltip"
					hidden
					role="dialog"
					aria-label="<?php echo esc_attr($titre); ?>"
					data-pin-id="<?php echo esc_attr($pin_id); ?>"
					style="left:<?php echo esc_attr((string) $x); ?>%;top:<?php echo esc_attr((string) max(0, $y - 8)); ?>%;"
				>
					<button type="button" class="carte-avl__tooltip-close" aria-label="Fermer">&times;</button>
					<?php if ($titre !== '') : ?>
						<h3 class="carte-avl__tooltip-title"><?php echo esc_html($titre); ?></h3>
					<?php endif; ?>
					<div class="carte-avl__tooltip-body">
						<?php
						if ($contenu !== '') {
							echo apply_filters('the_content', $contenu); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
					<?php if ($lien !== '' && filter_var($lien, FILTER_VALIDATE_URL)) : ?>
						<p class="carte-avl__tooltip-link-wrap">
							<a
								class="carte-avl__tooltip-link"
								href="<?php echo esc_url($lien); ?>"
								target="_blank"
								rel="noopener noreferrer"
							><?php esc_html_e('En savoir plus', 'bootscore'); ?></a>
						</p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="carte-avl__legend" aria-label="Légende">
		<ul class="carte-avl__legend-list">
			<?php foreach ($categories as $cat) : ?>
				<li>
					<span class="carte-avl__legend-pin" style="--pin-color:<?php echo esc_attr($cat['couleur']); ?>;" aria-hidden="true">
						<svg viewBox="0 0 24 32" width="18" height="24"><path fill="var(--pin-color)" stroke="#1a3a6b" stroke-width="1.2" d="M12 1C6.5 1 2 5.5 2 11c0 7.5 10 19 10 19s10-11.5 10-19C22 5.5 17.5 1 12 1z"/><circle cx="12" cy="11" r="3.5" fill="#fff"/></svg>
					</span>
					<span><?php echo esc_html($cat['libelle']); ?></span>
				</li>
			<?php endforeach; ?>
			<li>
				<span class="carte-avl__legend-flag" aria-hidden="true">
					<?php if ($partner_icon !== '') : ?>
						<img src="<?php echo esc_url($partner_icon); ?>" alt="" width="18" height="24" draggable="false">
					<?php else : ?>
						<?php $carte_avl_render_partner_flag(18, 24); ?>
					<?php endif; ?>
				</span>
				<span><?php echo esc_html($ui['legende_partenaire']); ?></span>
			</li>
		</ul>
	</div>
</div>
