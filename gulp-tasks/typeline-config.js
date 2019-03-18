/**
 * This file contains tasks used to generate typeline configuration files to be used in sync
 * in php and scss code from .json source files
 *
 * @version 1.0.0
 */

var gulp = require( 'gulp-help' )( require( 'gulp' ) ),
	plugins = require( 'gulp-load-plugins' )();





// -----------------------------------------------------------------------------
// This task generates a SCSS file containing a map variable that has
// the same keys and values as the typeline-config.json
// -----------------------------------------------------------------------------

gulp.task( 'typeline-config', 'Create SCSS typeline config from json', function() {
	return gulp.src( 'inc/integrations/typeline-config.json' )
	           .pipe( plugins.jsonToSassMap( {
		           source: 'inc/integrations/typeline-config.json',
		           output: 'assets/scss/_typeline-config.scss'
	           } ) )
	           .pipe( plugins.jsonToSassMap( {
		           source: 'inc/integrations/typeline-config-editor.json',
		           output: 'assets/scss/_typeline-config-editor.scss'
	           } ) );
} );





// -----------------------------------------------------------------------------
// This is a helper function used to generate a similar variable in a php file
// This is taken from gulp-phpconfig, but I had to modify it because it didn't add '' around the json :(
// -----------------------------------------------------------------------------
var through = require( 'through2' );
var path = require( 'path' );
var phpConfig = function( opts ) {
	opts = opts || {};
	function generate( file, enc, cb ) {
		if ( file.isStream() ) {
			return cb( new Error( 'gulp-phpconfig: Streaming is not supported' ) );
		}
		try {
			var str = file.contents.toString( enc || 'utf8' ),
				json = JSON.parse( str ),
				filename = path.basename( file.history[0] ),
				filepath = path.dirname( file.history[0] ),
				php = [opts.openTag || '<?php'];

			for ( prop in json ) {
				php.push( (
					opts.define
				) ? 'define("' + prop + '", ' + JSON.stringify( json[prop] ) + ');' : '$' + prop.replace( /-/g, "_" ) + ' = \'' + JSON.stringify( json[prop] ) + '\';' );
			}

			if ( typeof opts.closeTag !== 'undefined' ) {
				php.push( opts.closeTag );
			} else {
				php.push( '?>' );
			}

			file.contents = new Buffer( php.join( '\r\n' ) );

			if ( typeof opts === 'string' ) {
				filename = opts;
			} else if ( opts && opts.filename && typeof opts.filename === 'string' ) {
				filename = opts.filename;
			}
			file.history[0] = filepath + '/' + filename;
			cb( null, file );
		} catch ( e ) {
			return cb( e );
		}
	}

	return through.obj( generate );
};





// -----------------------------------------------------------------------------
// The task that actually generates the php file that contains
// the same configuration as typeline-config.json
// -----------------------------------------------------------------------------

gulp.task( 'typeline-phpconfig', 'Create PHP typeline config from json', function() {

	return gulp.src( 'inc/integrations/typeline-config.json' )
	           .pipe( phpConfig( 'typeline-config.php' ) )
	           .pipe( gulp.dest( 'inc/integrations/' ) );
} );
