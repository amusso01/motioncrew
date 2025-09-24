/** @type {import('tailwindcss').Config} */
export default {
	content: [
		"./*.php",
		"./**/*.php",
		"./src/**/*.{js,jsx,ts,tsx}",
		"./template-parts/**/*.php",
		"./components/**/*.php",
		"./page-templates/**/*.php",

		// Add any other paths where you use Tailwind classes
	],
};
