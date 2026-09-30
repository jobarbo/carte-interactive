/**
 * Carte AVL — tooltip + year filter (no parallax / head tracking).
 */
(function () {
	"use strict";

	function initCarte(root) {
		if (!root || root.dataset.carteAvlReady) return;
		root.dataset.carteAvlReady = "1";

		const pins = Array.from(root.querySelectorAll(".carte-avl__pin"));
		const tips = Array.from(root.querySelectorAll(".carte-avl__tooltip"));
		const filters = Array.from(root.querySelectorAll(".carte-avl__filter"));

		function closeAll() {
			tips.forEach(function (tip) {
				tip.hidden = true;
			});
			pins.forEach(function (pin) {
				pin.setAttribute("aria-expanded", "false");
			});
			root.classList.remove("is-tooltip-open");
		}

		function placeTip(tip, pin) {
			const map = root.querySelector(".carte-avl__map") || root;
			const mobile = window.matchMedia("(max-width: 640px)").matches;
			tip.style.left = "";
			tip.style.top = "";
			tip.style.transform = "";
			tip.classList.remove("is-left", "is-right");
			if (mobile) return;

			const mapRect = map.getBoundingClientRect();
			const pinRect = pin.getBoundingClientRect();
			const gap = 12;
			const pad = 12;
			const w = tip.offsetWidth;
			const h = tip.offsetHeight;
			const pinCenterX = pinRect.left + pinRect.width / 2;
			const pinMidY = pinRect.top + pinRect.height / 2;
			const preferRight = pinCenterX < mapRect.left + mapRect.width / 2;

			const leftIfRight = pinRect.right + gap - mapRect.left;
			const leftIfLeft = pinRect.left - gap - w - mapRect.left;
			const roomRight = window.innerWidth - pad - (pinRect.right + gap + w);
			const roomLeft = pinRect.left - gap - w - pad;

			let side = preferRight ? "right" : "left";
			if (preferRight && roomRight < 0 && roomLeft > roomRight) {
				side = "left";
			} else if (!preferRight && roomLeft < 0 && roomRight > roomLeft) {
				side = "right";
			}

			let left = side === "right" ? leftIfRight : leftIfLeft;
			let top = pinMidY - h / 2 - mapRect.top;

			const minTop = pad - mapRect.top;
			const maxTop = window.innerHeight - pad - mapRect.top - h;
			if (maxTop >= minTop) {
				top = Math.min(Math.max(top, minTop), maxTop);
			}

			tip.classList.add(side === "right" ? "is-right" : "is-left");
			tip.style.left = left + "px";
			tip.style.top = top + "px";
			tip.style.transform = "none";
		}

		function openTip(pinId) {
			closeAll();
			const tip = root.querySelector(
				'.carte-avl__tooltip[data-pin-id="' + pinId + '"]'
			);
			const pin = root.querySelector(
				'.carte-avl__pin[data-pin-id="' + pinId + '"]'
			);
			if (!tip || !pin) return;
			tip.hidden = false;
			pin.setAttribute("aria-expanded", "true");
			root.classList.add("is-tooltip-open");
			placeTip(tip, pin);
			requestAnimationFrame(function () {
				placeTip(tip, pin);
			});
		}

		pins.forEach(function (pin) {
			pin.addEventListener("click", function (e) {
				e.stopPropagation();
				const id = pin.getAttribute("data-pin-id");
				const expanded = pin.getAttribute("aria-expanded") === "true";
				if (expanded) {
					closeAll();
				} else {
					openTip(id);
				}
			});
		});

		tips.forEach(function (tip) {
			const closeBtn = tip.querySelector(".carte-avl__tooltip-close");
			if (closeBtn) {
				closeBtn.addEventListener("click", function (e) {
					e.stopPropagation();
					closeAll();
				});
			}
			tip.addEventListener("click", function (e) {
				e.stopPropagation();
			});
		});

		document.addEventListener("click", function (e) {
			if (!root.contains(e.target)) {
				closeAll();
				return;
			}
			if (
				!e.target.closest(".carte-avl__pin") &&
				!e.target.closest(".carte-avl__tooltip")
			) {
				closeAll();
			}
		});

		document.addEventListener("keydown", function (e) {
			if (e.key === "Escape") closeAll();
		});

		function setFilter(edition) {
			filters.forEach(function (btn) {
				const active = btn.getAttribute("data-edition") === edition;
				btn.classList.toggle("is-active", active);
				btn.setAttribute("aria-pressed", active ? "true" : "false");
			});

			pins.forEach(function (pin) {
				const pinEd = pin.getAttribute("data-edition");
				const show = edition === "all" || pinEd === edition;
				pin.classList.toggle("is-filtered-out", !show);
			});

			closeAll();
		}

		filters.forEach(function (btn) {
			btn.addEventListener("click", function (e) {
				e.stopPropagation();
				setFilter(btn.getAttribute("data-edition") || "all");
			});
		});

		window.addEventListener("resize", function () {
			const openPin = root.querySelector('.carte-avl__pin[aria-expanded="true"]');
			if (!openPin) return;
			const tip = root.querySelector(
				'.carte-avl__tooltip[data-pin-id="' + openPin.getAttribute("data-pin-id") + '"]'
			);
			if (tip && !tip.hidden) placeTip(tip, openPin);
		});
	}

	function boot() {
		document.querySelectorAll("[data-carte-avl]").forEach(initCarte);
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", boot);
	} else {
		boot();
	}

	// Elementor editor / frontend hooks
	if (typeof window.jQuery !== "undefined") {
		window.jQuery(window).on("elementor/frontend/init", function () {
			boot();
		});
	}
})();
