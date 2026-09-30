/**
 * Carte AVL — tooltip, year filter and generative event routes (no parallax / head tracking).
 */
(function () {
	"use strict";

	/*
	 * Generative event routes, drawn client-side (so page caching can't freeze them) with a new
	 * Math.random shape on every load. The path always passes through each stop (event flag) in
	 * chronological order; stops come from data-stops, built by carte_avl_event_routes() in PHP.
	 */
	const LOOP_MIN_LEG = 360;
	const LOOP_SLOT = 450;
	const LOOP_CHANCE = 0.55;
	const LOOP_MAX = 3;

	/*
	 * Which legs get loops, as positions t (0–1) along each leg. Every leg long enough may get one
	 * loop per LOOP_SLOT px; at least one loop is guaranteed when any leg qualifies.
	 */
	function planLoops(legs, rand) {
		const plan = legs.map(function (leg) {
			const slots = leg.len >= LOOP_MIN_LEG ? Math.max(1, Math.min(2, Math.floor(leg.len / LOOP_SLOT))) : 0;
			const ts = [];
			for (let k = 0; k < slots; k++) {
				if (rand() < LOOP_CHANCE) ts.push((k + 0.3 + rand() * 0.4) / slots);
			}
			return ts;
		});

		let total = plan.reduce(function (n, ts) { return n + ts.length; }, 0);
		const eligible = legs.map(function (leg, i) { return leg.len >= LOOP_MIN_LEG ? i : -1; }).filter(function (i) { return i >= 0; });
		if (total === 0 && eligible.length) {
			plan[eligible[Math.floor(rand() * eligible.length)]].push(0.35 + rand() * 0.3);
			total = 1;
		}
		while (total > LOOP_MAX) {
			const withLoops = plan.filter(function (ts) { return ts.length; });
			withLoops[Math.floor(rand() * withLoops.length)].pop();
			total--;
		}
		return plan;
	}

	/*
	 * Densify the route into points to smooth. Each leg wanders along
	 * offset(t) = arc·sin(πt) + wave·sin(2πt), and loops are drawn *into* that curve as a prolate
	 * cycloid (the line runs forward, curls back over itself, then carries on), so after Catmull-Rom
	 * smoothing the loop flows out of the curve like a pen stroke instead of being pasted on.
	 */
	function routeWander(stops, rand, width, height) {
		const clampX = function (v) { return Math.min(Math.max(v, width * 0.02), width * 0.98); };
		const clampY = function (v) { return Math.min(Math.max(v, height * 0.02), height * 0.98); };

		const legs = stops.slice(0, -1).map(function (a, i) {
			const b = stops[i + 1];
			const dx = b[0] - a[0];
			const dy = b[1] - a[1];
			return { a: a, dx: dx, dy: dy, len: Math.hypot(dx, dy) };
		});
		const loops = planLoops(legs, rand);

		const out = [stops[0]];
		legs.forEach(function (leg, i) {
			const { a, dx, dy, len } = leg;

			// Very short legs only get the smoothing, a wave there would look like a scribble.
			if (len < 120) {
				out.push(stops[i + 1]);
				return;
			}

			const nx = -dy / len;
			const ny = dx / len;
			// Long legs always bow northward so routes stay over Canada rather than dipping into the US.
			const north = ny < 0 ? 1 : -1;
			const flip = rand() < 0.75 || len >= 500;
			const arc = Math.min(len * (0.07 + rand() * 0.09), 150) * (flip ? north : -north);
			const wave = Math.min(len * (0.03 + rand() * 0.05), 70) * (rand() < 0.5 ? -1 : 1);
			const base = function (t) {
				const off = arc * Math.sin(Math.PI * t) + wave * Math.sin(2 * Math.PI * t);
				return [a[0] + dx * t + nx * off, a[1] + dy * t + ny * off];
			};

			const samples = [];
			const count = Math.max(2, Math.min(12, Math.round(len / 130)));
			for (let j = 1; j <= count; j++) {
				const t = j / (count + 1);
				samples.push({ t: t, p: base(t) });
			}

			loops[i].forEach(function (t0) {
				const r = 38 + rand() * 20;          // loop radius along the line
				const ry = r * (0.7 + rand() * 0.3); // height: slightly squashed
				const lean = (rand() - 0.5) * 0.7;   // tilts the loop forward/backward
				const advance = r * (0.2 + rand() * 0.15); // < r, which is what makes the curve cross itself
				const h = (Math.PI * advance) / len;  // half the loop's span, in t units

				const tc = Math.min(Math.max(t0, h + (r * 3) / len), 1 - h - (r * 3) / len);
				const p0 = base(tc - 0.002);
				const p1 = base(tc + 0.002);
				const tl = Math.hypot(p1[0] - p0[0], p1[1] - p0[1]) || 1;
				const T = [(p1[0] - p0[0]) / tl, (p1[1] - p0[1]) / tl];
				let side = rand() < 0.5 ? -1 : 1;
				const top = base(tc);
				const tx = top[0] - T[1] * side * ry * 2;
				const ty = top[1] + T[0] * side * ry * 2;
				if (tx < width * 0.03 || tx > width * 0.97 || ty < height * 0.03 || ty > height * 0.97) side = -side;
				const N = [-T[1] * side, T[0] * side];

				// Drop regular samples the loop replaces (plus a little room so it can breathe).
				const room = h + 60 / len;
				for (let s = samples.length - 1; s >= 0; s--) {
					if (Math.abs(samples[s].t - tc) < room) samples.splice(s, 1);
				}

				const steps = 14;
				for (let k = 0; k <= steps; k++) {
					const th = (2 * Math.PI * k) / steps;
					const t = tc - h + (2 * h * k) / steps;
					const p = base(t);
					const along = r * Math.sin(th) + lean * ry * (1 - Math.cos(th));
					const across = ry * (1 - Math.cos(th));
					samples.push({ t: t, p: [p[0] + T[0] * along + N[0] * across, p[1] + T[1] * along + N[1] * across] });
				}

				// Swing: the line dips away from the loop around it, off-centre, so it sweeps into the
				// loop from one angle and leaves at another — like a pen flick rather than a stamped circle.
				const sag = ry * (0.5 + rand() * 0.6);
				const win = h + (160 + rand() * 120) / len;
				const mid = tc + (rand() - 0.5) * win * 0.9;
				samples.forEach(function (s) {
					const u = (s.t - mid) / win;
					if (Math.abs(u) >= 1) return;
					const k = (-sag * (1 + Math.cos(Math.PI * u))) / 2;
					s.p = [s.p[0] + N[0] * k, s.p[1] + N[1] * k];
				});
			});

			samples
				.sort(function (m, n) { return m.t - n.t; })
				.forEach(function (s) { out.push([clampX(s.p[0]), clampY(s.p[1])]); });
			out.push(stops[i + 1]);
		});
		return out;
	}

	function routeSmooth(pts) {
		const cap = function (tx, ty, max) {
			const len = Math.hypot(tx, ty);
			return len > max && len > 0 ? [(tx * max) / len, (ty * max) / len] : [tx, ty];
		};
		const n = pts.length;
		const segments = [];
		for (let i = 0; i < n - 1; i++) {
			const p0 = pts[Math.max(0, i - 1)];
			const p1 = pts[i];
			const p2 = pts[i + 1];
			const p3 = pts[Math.min(n - 1, i + 2)];
			const max = Math.hypot(p2[0] - p1[0], p2[1] - p1[1]) / 3;
			const t1 = cap((p2[0] - p0[0]) / 6, (p2[1] - p0[1]) / 6, max);
			const t2 = cap((p3[0] - p1[0]) / 6, (p3[1] - p1[1]) / 6, max);
			segments.push([p1, [p1[0] + t1[0], p1[1] + t1[1]], [p2[0] - t2[0], p2[1] - t2[1]], p2]);
		}
		return segments;
	}

	function routePath(stops, width, height) {
		if (stops.length < 2) return "";
		const rand = Math.random;
		const segments = routeSmooth(routeWander(stops, rand, width, height));

		const pt = function (p) {
			return Math.round(p[0] * 10) / 10 + " " + Math.round(p[1] * 10) / 10;
		};
		return segments.reduce(function (d, s) {
			return d + " C" + pt(s[1]) + " " + pt(s[2]) + " " + pt(s[3]);
		}, "M" + pt(segments[0][0]));
	}

	function drawRoutes(root) {
		root.querySelectorAll(".carte-avl__route[data-stops]").forEach(function (g) {
			const path = g.querySelector("path");
			let stops;
			try {
				stops = JSON.parse(g.getAttribute("data-stops"));
			} catch (e) {
				return;
			}
			const width = parseFloat(g.getAttribute("data-width")) || 1920;
			const height = parseFloat(g.getAttribute("data-height")) || 1440;
			const d = Array.isArray(stops) ? routePath(stops, width, height) : "";
			if (path && d) path.setAttribute("d", d);
		});
	}

	const SVG_NS = "http://www.w3.org/2000/svg";
	const DRAW_SPEED = 700; // canvas px per second
	const MIN_LEG_SECONDS = 0.7; // short legs still get a visible eased move
	const STOP_HOLD = 0.45; // pause on each event flag, in seconds
	let maskCount = 0;

	/*
	 * Draw-on reveal. The route's own stroke-dasharray makes the dots, so it can't also be animated
	 * with the dashoffset trick; instead a solid copy of the path, inside a mask, is drawn with
	 * pathLength=1 + dashoffset and progressively uncovers the dotted line.
	 */
	function prepareReveal(svg, g) {
		const path = g.querySelector("path");
		if (!path || !path.getAttribute("d")) return null;

		let defs = svg.querySelector("defs");
		if (!defs) {
			defs = document.createElementNS(SVG_NS, "defs");
			svg.insertBefore(defs, svg.firstChild);
		}
		const id = "carte-avl-route-mask-" + ++maskCount;
		const mask = document.createElementNS(SVG_NS, "mask");
		mask.setAttribute("id", id);
		mask.setAttribute("maskUnits", "userSpaceOnUse");
		const vb = svg.viewBox.baseVal;
		mask.setAttribute("x", vb.x);
		mask.setAttribute("y", vb.y);
		mask.setAttribute("width", vb.width);
		mask.setAttribute("height", vb.height);

		const reveal = document.createElementNS(SVG_NS, "path");
		reveal.setAttribute("d", path.getAttribute("d"));
		reveal.setAttribute("pathLength", "1");
		reveal.setAttribute("class", "carte-avl__route-reveal");
		mask.appendChild(reveal);
		defs.appendChild(mask);
		path.setAttribute("mask", "url(#" + id + ")");

		let stops = [];
		try {
			stops = JSON.parse(g.getAttribute("data-stops")) || [];
		} catch (e) {
			stops = [];
		}
		return { el: reveal, keyframes: revealKeyframes(path, stops) };
	}

	/*
	 * Where each stop falls along the path, as a fraction of its length. Walks forward from the
	 * previous stop, so a loop passing near a flag can't be mistaken for the flag itself.
	 */
	function stopFractions(path, stops) {
		const total = path.getTotalLength();
		const step = 2;
		const out = [0];
		let from = 0;
		for (let k = 1; k < stops.length; k++) {
			let best = total;
			let bestDist = Infinity;
			for (let l = from; l <= total; l += step) {
				const p = path.getPointAtLength(l);
				const dist = Math.hypot(p.x - stops[k][0], p.y - stops[k][1]);
				if (dist < bestDist) {
					bestDist = dist;
					best = l;
				}
				if (dist < 1.5) break;
			}
			from = best;
			out.push(k === stops.length - 1 ? 1 : best / total);
		}
		return { total: total, fractions: out };
	}

	const EASE_LEG = "cubic-bezier(0.45, 0.05, 0.3, 1)";

	/*
	 * One eased move per leg (decelerating into each event flag) followed by a short hold on the
	 * flag, as Web Animations keyframes on the mask's stroke-dashoffset (1 = hidden, 0 = drawn).
	 */
	function revealKeyframes(path, stops) {
		const { total, fractions } = stopFractions(path, stops);
		const legs = [];
		for (let k = 1; k < fractions.length; k++) {
			legs.push({
				to: fractions[k],
				move: Math.max(((fractions[k] - fractions[k - 1]) * total) / DRAW_SPEED, MIN_LEG_SECONDS),
				hold: k < fractions.length - 1 ? STOP_HOLD : 0,
			});
		}
		const duration = legs.reduce(function (s, leg) { return s + leg.move + leg.hold; }, 0);

		const frames = [{ offset: 0, strokeDashoffset: "1px", easing: EASE_LEG }];
		let time = 0;
		legs.forEach(function (leg) {
			time += leg.move;
			const value = 1 - leg.to + "px";
			frames.push({ offset: time / duration, strokeDashoffset: value, easing: leg.hold ? "linear" : EASE_LEG });
			if (leg.hold) {
				time += leg.hold;
				frames.push({ offset: time / duration, strokeDashoffset: value, easing: EASE_LEG });
			}
		});
		frames[frames.length - 1].offset = 1;
		return { frames: frames, duration: duration * 1000 };
	}

	function playReveal(reveal) {
		if (reveal.animation) reveal.animation.cancel();
		reveal.animation = reveal.el.animate(reveal.keyframes.frames, {
			duration: reveal.keyframes.duration,
			fill: "forwards",
		});
	}

	function setupReveal(root) {
		const svg = root.querySelector(".carte-avl__routes");
		if (!svg) return function () {};
		if (window.matchMedia("(prefers-reduced-motion: reduce)").matches || !("IntersectionObserver" in window)) {
			return function () {};
		}

		const reveals = new Map();
		svg.querySelectorAll(".carte-avl__route").forEach(function (g) {
			const reveal = prepareReveal(svg, g);
			if (reveal) reveals.set(g, reveal);
		});
		if (!reveals.size) return function () {};

		let seen = false;
		const io = new IntersectionObserver(
			function (entries) {
				if (!entries.some(function (e) { return e.isIntersecting; })) return;
				seen = true;
				io.disconnect();
				reveals.forEach(playReveal);
			},
			{ threshold: 0.35 }
		);
		io.observe(root.querySelector(".carte-avl__map") || svg);

		// Replay a route when the year filter brings it back into view.
		return function (shownRoutes) {
			if (!seen) return;
			shownRoutes.forEach(function (g) {
				const reveal = reveals.get(g);
				if (reveal) playReveal(reveal);
			});
		};
	}

	function initCarte(root) {
		if (!root || root.dataset.carteAvlReady) return;
		root.dataset.carteAvlReady = "1";

		const pins = Array.from(root.querySelectorAll(".carte-avl__pin"));
		const tips = Array.from(root.querySelectorAll(".carte-avl__tooltip"));
		const filters = Array.from(root.querySelectorAll(".carte-avl__filter"));
		const routes = Array.from(root.querySelectorAll(".carte-avl__route"));

		drawRoutes(root);
		const replayRoutes = setupReveal(root);

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

			pins.concat(routes).forEach(function (el) {
				const elEd = el.getAttribute("data-edition");
				const show = edition === "all" || elEd === edition;
				el.classList.toggle("is-filtered-out", !show);
			});

			replayRoutes(
				routes.filter(function (g) {
					return !g.classList.contains("is-filtered-out");
				})
			);

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
