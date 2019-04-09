var gulp = require( 'gulp' ),
	prompt = require( 'gulp-prompt' ),
	fs = require( 'fs' ),
	plugins = require( 'gulp-load-plugins' )(),
	config;

if ( fs.existsSync( './gulpconfig.json' ) ) {
	config = require( '../gulpconfig.json' );
} else {
	config = require( '../gulpconfig.example.json' );
	console.log( "Don't forget to create your own gulpconfig.json from gulpconfig.json.example" );
}

var theme_name = config.theme_name,
	main_branch = config.main_branch;

/**
 * Creates a prompt command which allows you to update the demos
 */
function updateDemo() {

	var run_exec = require( 'child_process' ).exec;

	return gulp.src( './' )
		.pipe( prompt.confirm( "This task will stash all your local changes without commiting them,\n Make sure you did all your commits and pushes to the main " + main_branch + " branch! \n Are you sure you want to continue?!? " ) )
		.pipe( prompt.prompt( {
			type: 'list',
			name: 'demo_update',
			message: 'Which demo would you like to update?',
			choices: ['cancel', 'test.demos.pixelgrade.com/' + theme_name, 'demos.pixelgrade.com/' + theme_name]
		}, function( res ) {

			if ( res.demo_update === 'cancel' ) {
				console.log( 'No hard feelings!' );
				return false;
			}

			console.log( 'This task may ask for a github user / password or a ssh passphrase' );

			if ( res.demo_update === 'test.demos.pixelgrade.com/' + theme_name ) {
				run_exec( 'git fetch; git checkout test; git pull origin ' + main_branch + '; git push origin test; git checkout ' + main_branch + ';', function( err, stdout, stderr ) {
					// console.log(stdout);
					// console.log(stderr);
				} );
				console.log( " ==== The master branch is up-to-date now. But is the CircleCi job to update the remote test.demo.pixelgrade.com" );
				return true;
			}


			if ( res.demo_update === 'demos.pixelgrade.com/' + theme_name ) {
				run_exec( 'git fetch; git checkout master; git pull origin test; git push origin master; git checkout ' + main_branch + ';', function( err, stdout, stderr ) {
					console.log( stdout );
					console.log( stderr );
				} );

				console.log( " ==== The master branch is up-to-date now. But is the CircleCi job to update the remote demo.pixelgrade.com" );
				return true;
			}
		} ) );
}
gulp.task( 'update-demo', updateDemo );
