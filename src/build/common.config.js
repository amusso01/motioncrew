const webpack = require("webpack");
const path = require("path");

module.exports = {
	entry: {
		main: [
			path.resolve(__dirname, "../assets/scripts/main.js"),
			path.resolve(__dirname, "../assets/styles/_main.scss"),
		],
		tailwind: path.resolve(__dirname, "../assets/styles/tailwind.js"),
	},
	performance: {
		hints: false,
	},
	stats: {
		hash: false,
		version: false,
		timings: false,
		children: false,
		chunks: false,
		modules: false,
		source: false,
		publicPath: false,
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				use: "babel-loader",
				exclude: /node_modules/,
			},
		],
	},
	plugins: [
		// new webpack.ProvidePlugin({//If you use jquery.
		// 	$: 'jquery',
		// 	jQuery: 'jquery'
		// })
	],
}; //Config end
