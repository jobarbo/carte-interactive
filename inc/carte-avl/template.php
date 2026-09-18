<?php
/**
 * Front template: layered map + ACF pins + legends + tooltip.
 *
 * @package Bootscore_Child
 */

if (!defined('ABSPATH')) {
	exit;
}

$manifest = carte_avl_get_manifest();
$points   = function_exists('get_field') ? get_field('avl_points', 'option') : null;
if (!is_array($points)) {
	$points = array();
}

$edition_colors = carte_avl_edition_colors();
$edition_labels = carte_avl_edition_labels();

$cw = $manifest ? (int) $manifest['canvas']['width'] : 1920;
$ch = $manifest ? (int) $manifest['canvas']['height'] : 1440;
$layers = $manifest && isset($manifest['layers']) ? $manifest['layers'] : array();
$max_z  = 0;
foreach ($layers as $layer) {
	$max_z = max($max_z, (int) ($layer['zIndex'] ?? 0));
}
$pins_z = $max_z + 10;
?>
<div class="carte-avl" data-carte-avl>
	<div
		class="carte-avl__scene"
		style="aspect-ratio: <?php echo esc_attr($cw . ' / ' . $ch); ?>;"
	>
		<?php if (!empty($layers)) : ?>
			<div class="carte-avl__layers" aria-hidden="true">
				<?php foreach ($layers as $layer) :
					$name     = sanitize_html_class($layer['name'] ?? 'layer');
					$file     = isset($layer['file']) ? (string) $layer['file'] : '';
					$pos      = isset($layer['position']) && is_array($layer['position']) ? $layer['position'] : array();
					$z        = (int) ($layer['zIndex'] ?? 0);
					$opacity  = isset($layer['opacity']) ? (float) $layer['opacity'] : 1;
					$blend    = isset($layer['blendMode']) ? (string) $layer['blendMode'] : 'normal';
					$clipped  = !empty($layer['clipped']);
					// Skip clipped layers for simplicity (plan).
					if ($clipped || $file === '') {
						continue;
					}
					$src = CARTE_AVL_ASSETS_URI . '/layers/' . rawurlencode($file);
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

		<div class="carte-avl__editions" aria-label="Filtrer par édition">
			<p class="carte-avl__editions-title">Éditions</p>
			<ul class="carte-avl__editions-list">
				<?php foreach ($edition_labels as $key => $label) :
					$color = $edition_colors[$key] ?? '#ccc';
					?>
					<li>
						<button
							type="button"
							class="carte-avl__filter"
							data-edition="<?php echo esc_attr($key); ?>"
							aria-pressed="false"
						>
							<span class="carte-avl__swatch" style="background:<?php echo esc_attr($color); ?>;"></span>
							<span><?php echo esc_html($label); ?></span>
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
						<span>Toutes les années</span>
					</button>
				</li>
			</ul>
		</div>

		<div class="carte-avl__pins" style="z-index:<?php echo (int) $pins_z; ?>;">
			<?php foreach ($points as $index => $point) :
				$titre   = isset($point['titre']) ? (string) $point['titre'] : '';
				$type    = isset($point['type']) ? (string) $point['type'] : 'auteur';
				$edition = isset($point['edition']) ? (string) $point['edition'] : '2025';
				$x       = isset($point['x']) ? (float) $point['x'] : 50;
				$y       = isset($point['y']) ? (float) $point['y'] : 50;
				$contenu = isset($point['contenu']) ? (string) $point['contenu'] : '';
				$lien    = isset($point['lien']) ? trim((string) $point['lien']) : '';
				$color   = $edition_colors[$edition] ?? '#F5D76E';
				$is_flag = ($type === 'evenement');
				$pin_id  = 'avl-pin-' . (int) $index;
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
							<svg viewBox="0 0 24 32" width="28" height="36" focusable="false">
								<path fill="#1a3a6b" d="M4 2v28M4 2h14l-3 5 3 5H4"/>
							</svg>
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
							echo apply_filters('the_content', $contenu); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — WP content filters / oEmbed
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
			<?php foreach ($edition_labels as $key => $label) :
				$color = $edition_colors[$key] ?? '#F5D76E';
				?>
				<li>
					<span class="carte-avl__legend-pin" style="--pin-color:<?php echo esc_attr($color); ?>;" aria-hidden="true">
						<svg viewBox="0 0 24 32" width="18" height="24"><path fill="var(--pin-color)" stroke="#1a3a6b" stroke-width="1.2" d="M12 1C6.5 1 2 5.5 2 11c0 7.5 10 19 10 19s10-11.5 10-19C22 5.5 17.5 1 12 1z"/><circle cx="12" cy="11" r="3.5" fill="#fff"/></svg>
					</span>
					<span><?php echo esc_html($label); ?></span>
				</li>
			<?php endforeach; ?>
			<li>
				<span class="carte-avl__legend-flag" aria-hidden="true">
					<svg viewBox="0 0 24 32" width="18" height="24"><path fill="#1a3a6b" d="M4 2v28M4 2h14l-3 5 3 5H4"/></svg>
				</span>
				<span>Événement partenaire</span>
			</li>
		</ul>
	</div>
</div>
