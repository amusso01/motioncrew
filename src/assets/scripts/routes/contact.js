import cf7Submit from "./../part/cf7Submit";

export default {
	init() {
		// JavaScript to be fired on all pages
		cf7Submit();
	},
	finalize() {
		// JavaScript to be fired on all pages, after page specific JS is fired
	},
};
