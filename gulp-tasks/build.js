/**
 * This file contains build tasks that will create a ready-to-install zip archive
 * without any development resources or dependencies
 *
 * @version 1.0.0
 */

var gulp = require( 'gulp-help' )( require( 'gulp' ) ),
    del = require( 'del' ),
    fs = require( 'fs' ),
    plugins = require( 'gulp-load-plugins' )(),
    rsync = require('gulp-rsync'),
    debug = require('gulp-debug'),
    argv = require('yargs').argv;




// -----------------------------------------------------------------------------
// Copy theme folder outside in a build folder, recreate styles before that
// -----------------------------------------------------------------------------

gulp.task( 'copy-folder', 'Copy theme production files to a build folder', function() {
    let variation = 'julia';

    if ( argv.variation !== undefined ) {
        variation = argv.variation;
    }

    var dir = process.cwd();
    return gulp.src( './*' )
    // .pipe(debug({title: 'Copy Folder:'}))
        .pipe( plugins.exec( 'rm -Rf ./../build; mkdir -p ./../build/' + variation + ';', {
            silent: true,
            continueOnError: true // default: false
        } ) )
        .pipe(rsync({
            root: dir,
            destination: '../build/' + variation + '/',
            // archive: true,
            progress: false,
            silent: false,
            compress: false,
            recursive: true,
            emptyDirectories: true,
            clean: true,
            exclude: ['node_modules']
        }));
} );

// -----------------------------------------------------------------------------
// Replace the components' text domain with the theme text domain
// -----------------------------------------------------------------------------

gulp.task( 'components-txtdomain-replace', ['copy-folder'], function() {
    let variation = 'julia';

    if ( argv.variation !== undefined ) {
        variation = argv.variation;
    }

    return gulp.src( '../build/' + variation + '/components/**/*.php' )
        .pipe( plugins.replace( /['|"]__components_txtd['|"]/g, '\'' + variation + '\'' ) )
        .pipe( gulp.dest( '../build/' + variation + '/components' ) );
} );

// -----------------------------------------------------------------------------
// Replace the themes' text domain with the actual text domain (think variations)
// -----------------------------------------------------------------------------

gulp.task( 'txtdomain-replace', ['components-txtdomain-replace'], function() {
    let variation = 'julia';

    if ( argv.variation !== undefined ) {
        variation = argv.variation;
    }

    return gulp.src( '../build/' + variation + '/**/*.php' )
        .pipe( plugins.replace( /['|"]__theme_txtd['|"]/g, '\'' + variation + '\'' ) )
        .pipe( gulp.dest( '../build/' + variation ) );
} );

// -----------------------------------------------------------------------------
// Move the current variation's PHP files in their proper place
// -----------------------------------------------------------------------------

gulp.task( 'move-variation-specific-files', ['txtdomain-replace'], function() {
	let variation = 'julia';

	if ( argv.variation !== undefined ) {
		variation = argv.variation;
	}

	return gulp.src( '../build/' + variation + '/variations/' + variation + '/synced/**/*' )
		.pipe( gulp.dest( '../build/' + variation ) );
} );


// -----------------------------------------------------------------------------
// Remove unneeded files and folders from the build folder
// -----------------------------------------------------------------------------

gulp.task( 'build', 'Remove unneeded files and folders from the build folder', ['move-variation-specific-files'], function() {
    let variation = 'julia';

    if ( argv.variation !== undefined ) {
        variation = argv.variation;
    }


    // Files that should not be present in build
    files_to_remove = [
        '**/codekit-config.json',
        'node_modules',
        'config.rb',
        'gulp-tasks',
        'gulpfile.js',
        'gulpconfig.json',
        'gulpconfig.example.json',
        'package.json',
        'package-lock.json',
        'pxg.json',
        'build',
        'css',
        '.idea',
        '.editorconfig',
        '**/.svn*',
        '**/*.css.map',
        '**/.sass*',
        '.sass*',
        '**/.git*',
        '*.sublime-project',
        '.DS_Store',
        '**/.DS_Store',
        '__MACOSX',
        '**/__MACOSX',
        'README.md',
        '**/README.md',
        '.csscomb',
        '.csscomb.json',
        '.codeclimate.yml',
        'tests',
        'circle.yml',
        '.circleci',
        '.labels',
        '.jscsrc',
        '.jshintignore',
        'browserslist',
		'.stylelintrc',
		'tsconfig.json',
		'tslint.json',
		'webpack.config.js',
		'.jscsrc',
		'.jshintignore',

		'assets/scss',
		'assets/js-old',
        'docs',
        'components/docs',
        'components/.bin',
        'components/.github',
        'components/tests',
        'components/.*',
        'components/composer*',
        'components/*.md',
        'components/functions.php',
        'components/phpcs*',
        'components/phpdoc*',
        'components/phpunit*',
        'components/style.css',
        'variations'

    ];

    files_to_remove.forEach( function( e, k ) {
        files_to_remove[k] = '../build/' + variation + '/' + e;
    } );

    return del.sync( files_to_remove, {force: true} );
} );





// -----------------------------------------------------------------------------
// Create the theme installer archive and delete the build folder
// -----------------------------------------------------------------------------

gulp.task( 'zip', 'Create the theme installer archive and delete the build folder', ['build'], function() {
    let variation = 'julia';

    if ( argv.variation !== undefined ) {
        variation = argv.variation;
    }

    var versionString = '';

    // get theme version from styles.css
    var contents = fs.readFileSync( "./style.css", "utf8" );

    // split it by lines
    var lines = contents.split( /[\r\n]/ );

    function checkIfVersionLine( value, index, ar ) {
        var myRegEx = /^[Vv]ersion:/;
        return myRegEx.test( value );
    }

    // apply the filter
    var versionLine = lines.filter( checkIfVersionLine );

    versionString = versionLine[0].replace( /^[Vv]ersion:/, '' ).trim();
    versionString = '-' + versionString.replace( /\./g, '-' );

    return gulp.src( './' )
        .pipe( plugins.exec( 'cd ./../; rm -rf ' + variation[0].toUpperCase() + variation.slice( 1 ) + '*.zip; cd ./build/; zip -r -X ./../' + variation[0].toUpperCase() + variation.slice( 1 ) + '-Installer' + versionString + '.zip ./; cd ./../; rm -rf build' ) );
} );
