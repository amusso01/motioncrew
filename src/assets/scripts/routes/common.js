import smoothscroll from "smoothscroll-polyfill";
// import part
import navResize from "./../part/navResize";

export default {
	init() {
		// JavaScript to be fired on all pages

		// kick off the polyfill!
		smoothscroll.polyfill();
		navResize();
	},

	finalize() {
		// JavaScript to be fired on all pages, after page specific JS is fired
	},
};
