/**
 * Gulp tasks used to automate compilation of styles, concatenation of scripts
 * and the build process
 *
 * @version 1.0.0
 */
var gulp = require( 'gulp-help' )( require( 'gulp' ) ),
	plugins = require( 'gulp-load-plugins' )(),
	fs = require( 'fs' );

// load tasks from the gulp-tasks folder
require( 'require-dir' )( './gulp-tasks' );

if ( fs.existsSync( './gulpconfig.json' ) ) {
	config = require( './gulpconfig.json' );
} else {
	config = require( './gulpconfig.example.json' );
	console.log( "Don't forget to create your own gulpconfig.json from gulpconfig.json.example" );
}





// -----------------------------------------------------------------------------
// Do a clean compilation of all the styles and scripts files
// with the latest configurations
// -----------------------------------------------------------------------------

gulp.task( 'compile', 'Runs all compilation tasks in sequence', function( cb ) {
	plugins.sequence( 'styles', 'scripts', cb );
});





// -----------------------------------------------------------------------------
// Convenience task for development.
//
// This is the command you run to warm the site up for development. It will do
// a full build, open BrowserSync, and start listening for changes.
// -----------------------------------------------------------------------------

gulp.task( 'bs', 'Main development task:', ['compile', 'browser-sync', 'watch'] );
