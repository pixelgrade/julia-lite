<?php
/**
 * Julia functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Julia
 * @since 1.0.0
 */

/**
 * =========================
 * A few (wise) words
 *
 * For consistency amongst our themes, we have put as much of the theme behaviour (both logical and stylistic)
 * in components (the `components` directory). This includes the "classic" theme files like `archive.php`, `single.php`,
 * `header.php`, or `sidebar.php`.
 * Do no worry. You can still have those files in a theme, or a child theme. It will automagically work!
 *
 * We prefer not to use those files if the theme design allows us to stick to the markup patterns common to our themes,
 * available in our components.
 * This will make for more solid themes, faster update cycles and faster development for new themes.
 *
 * Now, let the show begin!
 * Oh snap... it already began :)
 * =========================
 */

/*
 * =========================
 * Autoload the Pixelgrade Components FTW!
 * This must be the FIRST thing a theme does!
 * =========================
 */
require_once trailingslashit( get_template_directory() ) . 'components/components-autoload.php';
Pixelgrade_Components_Autoload();


if ( ! function_exists( 'julia_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function julia_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on 'julia-lite', use a find and replace
		 * to change 'julia-lite' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'julia-lite', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded title tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Add image sizes used by theme.
		 */
		// None right now. Only the ones that come from components.

		/*
		 * Add theme support for site logo
		 *
		 * First, it's the image size we want to use for the logo thumbnails
		 * Second, the 2 classes we want to use for the "Display Header Text" Customizer logic
		 */
add_theme_support(
    'custom-logo', apply_filters(
        'julia_header_site_logo', array(
        'height'      => 600,
        'width'       => 1360,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array(
        'site-title',
        'site-description-text',
        )
        ) 
    ) 
);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
add_theme_support(
    'html5', array(
    'comment-list',
    'gallery',
    'caption',
    ) 
);

		/*
		 * Remove themes' post formats support
		 */
		remove_theme_support( 'post-formats' );

		/*
		 * Add the editor style and fonts
		 */
add_editor_style(
    array(
				julia_google_fonts_url(),
				'editor-style.css',
    )
);

		/*
		 * Enable support for Visible Edit Shortcuts in the Customizer Preview
		 *
		 * @link https://make.wordpress.org/core/2016/11/10/visible-edit-shortcuts-in-the-customizer-preview/
		 */
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
}
add_action( 'after_setup_theme', 'julia_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function julia_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'julia_content_width', 720 );
}
add_action( 'after_setup_theme', 'julia_content_width', 0 );

function julia_custom_tiled_gallery_width() {
	$width = pixelgrade_option( 'main_content_container_width', 1300 );

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$width = pixelgrade_option( 'main_content_container_width', 1300 ) - 300 - 56;
	}

	return $width;
}
add_filter( 'tiled_gallery_content_width', 'julia_custom_tiled_gallery_width' );

/**
 * Enqueue scripts and styles.
 */
function julia_scripts() {
	$theme = wp_get_theme();
	$main_style_deps = array();

	/* Default Google Fonts */
	wp_enqueue_style( 'julia-google-fonts', julia_google_fonts_url() );

	/* The main theme stylesheet */
	if ( ! is_rtl() ) {
		wp_enqueue_style( 'julia-style', get_stylesheet_uri(), $main_style_deps, $theme->get( 'Version' ) );
	}

	/* Scripts */

	//The main script
	wp_enqueue_script( 'julia-commons-scripts', get_theme_file_uri( '/assets/js/commons.js' ), array( 'jquery' ), $theme->get( 'Version' ), true );
	wp_enqueue_script( 'julia-scripts', get_theme_file_uri( '/assets/js/app.bundle.js' ), array( 'julia-commons-scripts' ), $theme->get( 'Version' ), true );

	$localization_array = array(
		'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
	);

	wp_localize_script( 'julia-main-scripts', 'juliaStrings', $localization_array );
}
add_action( 'wp_enqueue_scripts', 'julia_scripts' );

function julia_load_wp_admin_style() {
	wp_register_style( 'julia_wp_admin_css', get_template_directory_uri() . '/admin.css', false, '1.0.0' );
	wp_enqueue_style( 'julia_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'julia_load_wp_admin_style' );

/**
 * Freemius Integration
 */
// Create a helper function for easy SDK access.
function jl_fs() {
    global $jl_fs;

    if ( ! isset( $jl_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $jl_fs = fs_dynamic_init( array(
            'id'                  => '2448',
            'slug'                => 'julia-lite',
            'type'                => 'theme',
            'public_key'          => 'pk_194d106c31e119afe90b2f2ce76ed',
            'is_premium'          => false,
            'has_addons'          => false,
            'has_paid_plans'      => false,
            'menu'                => array(
                'slug'           => 'julia-lite-welcome',
                'account'        => false,
                'contact'        => false,
                'support'        => false,
                'parent'         => array(
                    'slug' => 'themes.php',
                ),
            ),
        ) );
    }

    return $jl_fs;
}

// Init Freemius.
jl_fs();
// Signal that SDK was initiated.
do_action( 'jl_fs_loaded' );

/*
 * ==================================================
 * Load all the files directly in the `inc` directory
 * ==================================================
 */
pixelgrade_autoload_dir( 'inc' );

/**
 * Theme About page.
 */
require get_template_directory() . '/inc/admin/about-page.php';

require_once get_template_directory() . '/inc/integrations/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'julia_lite_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function julia_lite_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'      => 'WPForms Lite',
			'slug'      => 'wpforms-lite',
			'required'  => false,
		),
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 */
	$config = array(
		'id'           => 'julia-lite',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}

/**
 * Set the WPForms ShareASale ID.
 *
 * @param string $shareasale_id The the default ShareASale ID.
 *
 * @return string $shareasale_id
 */
function julia_lite_wpforms_shareasale_id( $shareasale_id ) {

	// If this WordPress installation already has an WPForms ShareASale ID
	// specified, use that.
	if ( ! empty( $shareasale_id ) ) {
		return $shareasale_id;
	}

	// Define the ShareASale ID to use.
	$shareasale_id = '1843354';

	// This WordPress installation doesn't have an ShareASale ID specified, so
	// set the default ID in the WordPress options and use that.
	update_option( 'wpforms_shareasale_id', $shareasale_id );

	// Return the ShareASale ID.
	return $shareasale_id;
}
add_filter( 'wpforms_shareasale_id', 'mythemename_wpforms_shareasale_id' );