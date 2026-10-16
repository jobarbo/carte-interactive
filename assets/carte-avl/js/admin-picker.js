/**
 * Carte AVL — admin map picker.
 * Click or drag the pin; fills the hidden x / y inputs (0–100 %).
 * The postal code only suggests a starting position.
 */
(function ($) {
	'use strict';

	function init() {
		var $picker = $('.avl-picker');
		if (!$picker.length) {
			return;
		}

		var $x = $('[data-name="x"] input[type="number"]');
		var $y = $('[data-name="y"] input[type="number"]');
		var $postal = $('[data-name="code_postal"] input[type="text"]');
		if (!$x.length || !$y.length) {
			return;
		}

		var $map = $('<div class="avl-picker__map"></div>').css('background-image', 'url("' + (carteAvlPicker.image || $picker.data('image')) + '")');
		var $pin = $('<div class="avl-picker__pin" hidden></div>');
		var $status = $('<p class="avl-picker__status"></p>');
		var $btn = $('<button type="button" class="button">Placer depuis le code postal</button>');
		$map.append($pin);
		$picker.append($map, $('<p></p>').append($btn), $status);

		function say(msg, isError) {
			$status.text(msg || '').toggleClass('is-error', !!isError);
		}

		function clamp(v) {
			return Math.max(0, Math.min(100, v));
		}

		function set(x, y) {
			x = Math.round(clamp(x) * 100) / 100;
			y = Math.round(clamp(y) * 100) / 100;
			$x.val(x).trigger('change');
			$y.val(y).trigger('change');
			render();
		}

		function render() {
			var x = parseFloat($x.val());
			var y = parseFloat($y.val());
			if (isNaN(x) || isNaN(y)) {
				$pin.attr('hidden', true);
				say('Cliquez sur la carte pour placer le point.');
				return;
			}
			$pin.css({ left: x + '%', top: y + '%' }).removeAttr('hidden');
			say('Position : ' + x.toFixed(1) + ' % / ' + y.toFixed(1) + ' %. Cliquez ou glissez pour ajuster.');
		}

		function fromEvent(e) {
			var r = $map[0].getBoundingClientRect();
			set(((e.clientX - r.left) / r.width) * 100, ((e.clientY - r.top) / r.height) * 100);
		}

		var dragging = false;
		$map.on('pointerdown', function (e) {
			dragging = true;
			this.setPointerCapture(e.originalEvent.pointerId);
			fromEvent(e.originalEvent);
			e.preventDefault();
		});
		$map.on('pointermove', function (e) {
			if (dragging) {
				fromEvent(e.originalEvent);
			}
		});
		$map.on('pointerup pointercancel', function () {
			dragging = false;
		});

		function geocode() {
			var postal = $.trim($postal.val());
			if (!postal) {
				say('Saisissez un code postal.', true);
				return;
			}
			$btn.prop('disabled', true);
			say('Recherche…');
			$.post(carteAvlPicker.ajaxUrl, {
				action: 'carte_avl_geocode',
				nonce: carteAvlPicker.nonce,
				postal: postal
			})
				.done(function (res) {
					if (res && res.success) {
						$postal.val(res.data.postal);
						set(res.data.x, res.data.y);
					} else {
						say((res && res.data && res.data.message) || 'Erreur.', true);
					}
				})
				.fail(function () {
					say('Erreur réseau.', true);
				})
				.always(function () {
					$btn.prop('disabled', false);
				});
		}

		$btn.on('click', geocode);
		// Suggest a start only while the point has never been placed.
		$postal.on('change', function () {
			if ($.trim($postal.val()) && isNaN(parseFloat($x.val()))) {
				geocode();
			}
		});

		render();
	}

	if (window.acf) {
		window.acf.addAction('ready', init);
	} else {
		$(init);
	}
})(jQuery);
