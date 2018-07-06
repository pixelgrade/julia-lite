const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const WebpackRTLPlugin = require("webpack-rtl-plugin");
const WebpackNotifierPlugin = require('webpack-notifier');
const ProgressBarPlugin = require('progress-bar-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const chalk = require('chalk');

// In case we want to differentiate between PROD and DEV environments at least we have a const ready
const isProduction = process.env.NODE_ENV === 'production';

/**
 * Extract text plugin is required to convert scss import in App.ts to external stylesheet
 * @type {ExtractTextPlugin}
 */
const extractPlugin = new ExtractTextPlugin({
	filename: '../../[name].css',
	allChunks: true
});
module.exports = function (env, argv) {

	const variation = env.variation || 'julia';
	const variationOptions = require('./variations/' + variation + '/webpack.defaults');

	return {
		/**
		 * Tell Webpack we want sourcemaps in our developer tools
		 * Note this ternary is a hack, because there prod will not build with sourcemaps
		 * https://github.com/webpack-contrib/sass-loader/issues/351
		 */
		devtool: "source-map",
		/**
		 * This is the entry point for our application src/App.ts everything including an import to our scss goes through here
		 */
		context: path.resolve(__dirname, './variations'),
		entry: {
			app: variationOptions.entry,
			'editor-style': variationOptions.editorStyle,
			admin: variationOptions.admin,
			style: variationOptions.style
		},
		/**
		 * This is where our bundled stuff is saved and the public path is what we link to in our script tags
		 */
		output: {
			path: path.resolve(__dirname, './assets/js'),
			filename: '[name].bundle.js',
			// Set this to whatever the relative asset path will be on your server
			publicPath: '/'
		},
		/**
		 * Sets the options for webpack-dev-server uses hot reloading by default
		 */
		devServer: {
			contentBase: path.resolve(__dirname, './variations'),
			// Access localhost through any device via YOUR_IPV4_ADDRESS:PORT ex. 192.168.1.9:8080
			host: '0.0.0.0',
			disableHostCheck: true,
			compress: false,
			port: 4300,
			// Controls terminal output for build process
			stats: {
				chunks: false
			}
		},
		/**
		 * This will warn us if any of our compiled assets are over 250kb (default value)
		 */
		performance: {hints: isProduction ? "warning" : false},
		/**
		 * Resolver helps webpack find module code that needs to be included for every bundle
		 */
		resolve: {
			/**
			 * Specify which extensions we want to look at for module bundling
			 */
			extensions: ['.ts', '.tsx', '.js', '.scss']
		},
		module: {
			/**
			 * Include our typescript and sass loaders
			 */
			rules: [
				/**
				 * Loader for TSHint
				 */
				{
					test: /\.ts$/,
					enforce: 'pre',
					loader: 'tslint-loader',
					options: {
						rules: {
							configuration: require('./tslint.json')
						}
					}
				},
				/**
				 * Typescript loader, excludes node_modules in case any dependencies use ts
				 */
				{
					test: /\.ts$/,
					exclude: [/node_modules/],
					use: [
						{
							loader: "babel-loader",
							options: {
								sourceMaps: true,
								"presets": [
									["es2015", {
										"modules": false
									}],
									"es2016"
								]
							}
						},
						{loader: "ts-loader"},
					]
				},
				/**
				 * Style loaders extractPlugin takes bundled css and creates an external stylesheet
				 */
				{
					test: /\.scss$/,
					use: extractPlugin.extract({
						/**
						 * Creates a <style></style> block with CSS in head if all other loaders fail
						 */
						fallback: "style-loader",
						use: [
							/**
							 * Bundles our CSS with sourcemaps and minify
							 */
							{
								loader: "css-loader", options: {
								importLoaders: 1,
								sourceMap: true,
								minimize: true,
								url: false
							}
							},
							/**
							 * Applies autoprefixer to our CSS as well as any other PostCSS plugins
							 */
							{
								loader: "postcss-loader", options: {
								sourceMap: true,
								plugins: (loader) => [
									require('postcss-import')({root: loader.resourcePath}),
									/**
									 * Allows for future CSS to be used today, probably best not used with Sass
									 */
									//require('postcss-cssnext')(),

									// Automatically add vendor prefixes to compiled SCSS
									require('autoprefixer')({browsers: "last 2 versions"}),
								]
							}
							},
							/**
							 * Compiles our SCSS code with sourcmaps
							 */
							{loader: "sass-loader", options: {sourceMap: true}},
						],
					}),
				},
			]
		},
		/**
		 * Include webpack plugins
		 */
		plugins: [
			new BrowserSyncPlugin({
				proxy: variationOptions.proxy,
				files: [
					'**/*.php',
					'**/*.css',
					'assets/js/**/*.js',
				],
				reloadDelay: 0,
				ui: false,
				notify: false,
				reloadOnRestart: true,
				open: false,
				browser: 'google chrome'
			}, {
				reload: true,
			}),
			new webpack.optimize.CommonsChunkPlugin({
				name: 'commons',
				filename: 'commons.js',
				/**
				 * Automatically detect libraries in node_modules for bundling in common.js
				 * @param module
				 * @returns {boolean}
				 */
				minChunks: (module) => {
					return module.context && module.context.indexOf('node_modules') !== -1;
				}
			}),
			/**
			 * Prevents generation of imported modules matching require expressions
			 */

			// Ex. We want everything in moment, but want to only exclude locales
			new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),

			/**
			 * Shows a desktop notification when builds are complete
			 */
			new WebpackNotifierPlugin({
				title: 'Webpack',
				alwaysNotify: true
			}),
			/**
			 * Creates a fancy progress bar in the terminal and uses chalk to make the terminal colors all like um pretty and stuff
			 */
			new ProgressBarPlugin({
				format: '  build [:bar] ' + chalk.blue.bold(':percent') + ' (:elapsed seconds)',
				clear: false
			}),
			/**
			 * Extracts bundled compiled scss and creates an external stylesheet to link to in our index.html
			 */
			extractPlugin
		]
	};
};
