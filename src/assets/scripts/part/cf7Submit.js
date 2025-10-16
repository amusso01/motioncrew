export default function cf7Submit() {
	const el = document.querySelector(".wpcf7");
	if (!el) return;

	const btn = document.querySelector("#fdryCf7Submit");
	const cfBtn = el.querySelector(".wpcf7-submit");

	btn.addEventListener("click", (e) => {
		e.preventDefault();
		cfBtn.click();
	});
}
