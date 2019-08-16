/**
 * This file contains development tasks used to compile and concatenate
 * scripts and stylesheets
 *
 * @version 1.0.0
 */

var gulp = require( 'gulp' ),
	plugins = require('gulp-load-plugins')(),
	del = require('del'),
	bs = require('browser-sync'),
	argv = require('yargs').argv;

var u = plugins.util,
	c = plugins.util.colors,
	log = plugins.util.log

// -----------------------------------------------------------------------------
// Stylesheets
// -----------------------------------------------------------------------------

function logError (err, res) {
	log(c.red('Sass failed to compile'))
	log(c.red('> ') + err.file.split('/')[err.file.split('/').length - 1] + ' ' + c.underline('line ' + err.line) + ': ' + err.message)
}

function stylesMain() {
	let variation = 'julia'

	if (argv.variation !== undefined) {
		variation = argv.variation
	}

	return gulp.src('variations/' + variation + '/scss/*.scss')
		.pipe(plugins.sourcemaps.init())
		.pipe(plugins.sass().on('error', logError))
		.pipe(plugins.autoprefixer())
		.pipe(plugins.sourcemaps.write('.'))
		.pipe(plugins.replace(/^@charset \"UTF-8\";\n/gm, ''))
		.pipe(gulp.dest('.'))
}
stylesMain.description = 'Compiles main css files (ie. style.css editor-style.css)';
gulp.task('styles-main', stylesMain )

function stylesRtl() {
	return gulp.src('style.css')
		.pipe(plugins.rtlcss())
		.pipe(plugins.rename('style-rtl.css'))
		.pipe(gulp.dest('.'))
}
stylesRtl.description = 'Generate style-rtl.css file based on style.css';
gulp.task('styles-rtl', stylesRtl )

function stylesProcess() {
	return gulp.src('style.css')
		.pipe(plugins.sourcemaps.init({loadMaps: true}))
		// @todo some processing
		.pipe(plugins.sourcemaps.write('.'))
		.pipe(gulp.dest('.'))
}
gulp.task('styles-process', stylesProcess)

function stylesComponents() {
	return gulp.src(['components/**/*.scss', '!components/docs/**/*', '!components/.*/**/*'])
		.pipe(plugins.sass().on('error', logError))
		.pipe(plugins.autoprefixer())
		.pipe(plugins.rename(function (path) {
			path.dirname = path.dirname.replace('/scss', '')
			path.dirname += '/css'
		}))
		.pipe(gulp.dest('./components'))
}
stylesComponents.description = 'Compiles components Sass and uses autoprefixer';
gulp.task('styles-components', stylesComponents )

function stylesAdmin() {

	return gulp.src('inc/admin/scss/**/*.scss')
		.pipe(plugins.sourcemaps.init())
		.pipe(plugins.sass().on('error', logError))
		.pipe(plugins.autoprefixer())
		.pipe(plugins.replace(/^@charset \"UTF-8\";\n/gm, ''))
		.pipe(gulp.dest('./inc/admin/css'))
}
stylesAdmin.description = 'Compiles WordPress admin Sass and uses autoprefixer';
gulp.task('styles-admin', stylesAdmin )

function stylesPixassistNotice() {

	return gulp.src('inc/admin/pixelgrade-assistant-notice/*.scss')
		.pipe(plugins.sourcemaps.init())
		.pipe(plugins.sass().on('error', logError))
		.pipe(plugins.autoprefixer())
		.pipe(plugins.replace(/^@charset \"UTF-8\";\n/gm, ''))
		.pipe(gulp.dest('./inc/admin/pixelgrade-assistant-notice'))
}
stylesAdmin.description = 'Compiles Pixelgrade Assistant admin notice Sass and uses autoprefixer';
gulp.task('styles-pixassist-notice', stylesPixassistNotice )

function stylesSequence(cb) {
	gulp.series('typeline-config', 'typeline-phpconfig', 'styles-components', 'styles-main', 'styles-rtl', 'styles-pixassist-notice', 'styles-admin')(cb);
}
stylesSequence.description = 'Compile all styles';
gulp.task('styles', stylesSequence )

// -----------------------------------------------------------------------------
// Scripts
// -----------------------------------------------------------------------------

var jsFiles = [
	'./assets/js/vendor/*.js',
	'./assets/js/main/wrapper-start.js',
	'./assets/js/modules/*.js',
	'./assets/js/main/unsorted.js',
	'./assets/js/main/main.js',
	'./assets/js/main/wrapper-end.js'
]

function scripts() {
	return gulp.src(jsFiles, { allowEmpty: true })
		.pipe(plugins.concat('main.js'))
		.pipe(gulp.dest('./assets/js/'))
}
scripts.description = 'Concatenate all JS into main.js and wrap all code in a closure';
gulp.task('scripts', scripts )

// -----------------------------------------------------------------------------
// Variation specific/synced files
// -----------------------------------------------------------------------------

function syncVariationSpecificFiles() {
	let variation = 'julia'

	if (argv.variation !== undefined) {
		variation = argv.variation
	}

	del.sync(['./inc/specific'])

	return gulp.src('./variations/' + variation + '/synced/**/*')
		.pipe(gulp.dest('.'))
}
gulp.task('sync-variation-specific-files', syncVariationSpecificFiles )

// -----------------------------------------------------------------------------
// Watch tasks
//
// These tasks are run whenever a file is saved. Don't confuse the files being
// watched (gulp.watch blobs in this task) with the files actually operated on
// by the gulp.src blobs in each individual task.
//
// A few of the performance-related tasks are excluded because they can take a
// bit of time to run and don't need to happen on every file change. If you want
// to run those tasks more frequently, set up a new watch task here.
// -----------------------------------------------------------------------------

function watchStart() {
	let variation = 'julia'

	if (argv.variation !== undefined) {
		variation = argv.variation
	}

	// watch for Typeline config changes
	gulp.watch([
		'inc/integrations/typeline-config.json',
		'inc/integrations/typeline-config-editor.json'
	], gulp.series('typeline-config', 'typeline-phpconfig'))

	// watch for theme related CSS changes
	gulp.watch(['variations/' + variation + '/**/*.scss', 'assets/scss/**/*.scss'], gulp.series('styles-main'))

	gulp.watch('assets/scss/admin/*.scss', gulp.series('styles-admin'))

	// watch for components related CSS changes
	// exclude the docs directory since that is not a true component; also exclude . directories
	gulp.watch(['components/**/*.scss', '!components/docs/**/*', '!components/.*/**/*'], gulp.series('styles-components', 'styles-main'))

	// watch for JavaScript changes
	gulp.watch('assets/js/**/*.js', gulp.series('scripts'))
}
watchStart.description = 'Watch for changes to various files and process them';
gulp.task('watch-start', watchStart )

function watchSequence(cb) {
	return gulp.series( 'compile', 'watch-start' )(cb);
}
watchSequence.description = 'Compile and watch for changes to various JSON, SCSS and JS files and process them';
gulp.task('watch', watchSequence )

// -----------------------------------------------------------------------------
// Browser Sync using Proxy server
//
// Makes web development better by eliminating the need to refresh. Essential
// for CSS development and multi-device testing.
//
// This is how you'd connect to a local server that runs itself.
// Examples would be a PHP site such as Wordpress or a
// Drupal site, or a node.js site like Express.
//
// Usage: gulp browser-sync-proxy --port 8080
// -----------------------------------------------------------------------------

function browserSync() {
	bs({
		// Point this to your pre-existing server.
		proxy: config.baseurl + (
			u.env.port ? ':' + u.env.port : ''
		),
		files: ['*.php', 'style.css', 'assets/js/*.js'],
		// This tells BrowserSync to auto-open a tab once it boots.
		open: true
	}, function (err, bs) {
		if (err) {
			console.log(bs.options)
		}
	})
}
gulp.task('browser-sync', browserSync )
