export default function navResize() {
	const el = document.querySelector("#navResize"); // ← change selector
	if (!el) return;

	const cls = "is-scrolled";
	const addAt = 95; // initial add threshold
	const upDelta = 40; // remove after scrolling up this much from peak
	const downDelta = 15; // re-add after moving down this much from the lowest point since removal

	let state = "hidden"; // 'shown' | 'hidden'
	let everShown = false; // becomes true after first add
	let peakY = 0; // highest Y while shown
	let valleyY = window.pageYOffset || document.documentElement.scrollTop; // lowest Y while hidden
	let lastY = valleyY;
	let ticking = false;

	function show(y) {
		el.classList.add(cls);
		state = "shown";
		everShown = true;
		peakY = y;
	}

	function hide(y) {
		el.classList.remove(cls);
		state = "hidden";
		valleyY = y; // start a new valley from this point
	}

	function update() {
		const y = window.pageYOffset || document.documentElement.scrollTop;

		if (state === "shown") {
			if (y > peakY) peakY = y; // update peak while going down
			if (y <= peakY - upDelta) hide(y); // remove after enough upward scroll
		} else {
			if (y < valleyY) valleyY = y; // track lowest point since removal
			if (y > lastY) {
				// scrolling down
				if (!everShown) {
					if (y >= addAt) show(y); // first time: must pass 95px
				} else if (y >= valleyY + downDelta) {
					show(y); // after removal: any downward move by downDelta
				}
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
	else state = "hidden";
}
