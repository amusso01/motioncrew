import smoothscroll from "smoothscroll-polyfill";

export default {
	init() {
		// JavaScript to be fired on all pages

		// kick off the polyfill!
		smoothscroll.polyfill();
	},

	finalize() {
		// JavaScript to be fired on all pages, after page specific JS is fired
	},
};
