export default function navResize() {
	const el = document.querySelector("#navResize");
	if (!el) return;

	const cls = "is-scrolled";
	const addAt = 145; // initial add threshold
	const upDelta = 90; // remove after scrolling up this much from the peak
	const downDelta = 20; // re-add after moving down this much (if you haven't gone back ≤ addAt)

	let state = "hidden"; // 'shown' | 'hidden'
	let everShown = false;
	let peakY = 0; // highest scrollY while shown
	let valleyY = window.pageYOffset || document.documentElement.scrollTop; // lowest while hidden
	let lastY = valleyY;
	let ticking = false;

	function show(y) {
		if (!el.classList.contains(cls)) el.classList.add(cls);
		state = "shown";
		everShown = true;
		peakY = y;
	}

	function hide(y) {
		if (el.classList.contains(cls)) el.classList.remove(cls);
		state = "hidden";
		valleyY = y; // start new valley at current point
	}

	function update() {
		const y = window.pageYOffset || document.documentElement.scrollTop;

		if (state === "shown") {
			if (y > peakY) peakY = y; // track new deepest point
			if (y <= peakY - upDelta) hide(y); // remove after enough upward scroll
		} else {
			if (y < valleyY) valleyY = y; // track lowest point since removal

			if (y > lastY) {
				// only react on downward motion
				// If we've gone back at/above the initial threshold area,
				// use addAt as the reference; else use valley + downDelta.
				const threshold =
					!everShown || valleyY <= addAt ? addAt : valleyY + downDelta;

				if (y >= threshold) show(y);
			}
		}

		lastY = y;
		ticking = false;
	}

	window.addEventListener(
		"scroll",
		() => {
			if (!ticking) {
				requestAnimationFrame(update);
				ticking = true;
			}
		},
		{ passive: true }
	);

	// Set correct state on load/refresh
	const y0 = window.pageYOffset || document.documentElement.scrollTop;
	if (y0 >= addAt) show(y0);
}
