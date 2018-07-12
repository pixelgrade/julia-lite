<?php
/**
 * Handles the definition of sidebars and the loading of various widgets
 *
 * @package Julia
 * @since 1.0.0
 */

/**
 * Register the widget areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function julia_widget_areas_init() {
	/**
	 * The main widget area
	 */
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', '__theme_txtd' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', '__theme_txtd' ),
		'before_widget' => '<section id="%1$s" class="widget widget--side %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title h3"><span>',
		'after_title'   => '</span></h2>',
	) );

	/**
	 * The below single post widget area
	 */
	register_sidebar( array(
		'name'          => esc_html__( 'Below Post', '__theme_txtd' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__( 'You can only use this area in the PRO version of the theme.', '__theme_txtd' ),
	) );

	/**
	 * The Archive Index Widget Areas
	 */

	// The Content Area
	register_sidebar( array(
		'name'          => esc_html__( 'Archive Index', '__theme_txtd' ),
		'id'            => 'archive-1',
		'description'   => esc_html__( 'Add widgets here.', '__theme_txtd' ),
		'before_widget' => '<section id="%1$s" class="widget widget--content %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title h3"><span>',
		'after_title'   => '</span></h2>',
	) );
}
add_action( 'widgets_init', 'julia_widget_areas_init', 10 );

/**
 * Register the front page widget areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function julia_widget_areas_init_front_page() {

	/**
	 * The Front Page Widget Areas
	 */

	// The Full Width Area #1
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page - Full Width Top', '__theme_txtd' ),
		'id'            => 'front-page-1',
		'description'   => esc_html__( 'Add widgets here.', '__theme_txtd' ),
		'before_widget' => '<section id="%1$s" class="widget widget--full %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title h3"><span>',
		'after_title'   => '</span></h2>',
	) );

	// The Content Area #1
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page - &#9484;  Content 1', '__theme_txtd' ),
		'id'            => 'front-page-2',
		'description'   => esc_html__( 'Add widgets here.', '__theme_txtd' ),
		'before_widget' => '<section id="%1$s" class="widget widget--content %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title h3"><span>',
		'after_title'   => '</span></h2>',
	) );

	// The Sidebar Area #1
	// @todo Rename this sidebar right here
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page - &#9492; Sidebar 1', '__theme_txtd' ),
		'id'            => 'front-page-3',
		'description'   => esc_html__( 'Add widgets here.', '__theme_txtd' ),
		'before_widget' => '<section id="%1$s" class="widget widget--side %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title h3"><span>',
		'after_title'   => '</span></h2>',
	) );

	// The Full Width Area #2
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page - Full Width Center', '__theme_txtd' ),
		'id'            => 'front-page-4',
		'description'   => esc_html__( 'You can only use this area in the PRO version of the theme.', '__theme_txtd' ),
	) );

	// The Content Area #2
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page - &#9484; Content 2', '__theme_txtd' ),
		'id'            => 'front-page-5',
		'description'   => esc_html__( 'You can only use this area in the PRO version of the theme.', '__theme_txtd' ),
	) );

	// The Sidebar Area #2
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page - &#9492; Sidebar 2', '__theme_txtd' ),
		'id'            => 'front-page-6',
		'description'   => esc_html__( 'You can only use this area in the PRO version of the theme.', '__theme_txtd' ),
	) );

	// The Full Width Area #3
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page - Full Width Bottom', '__theme_txtd' ),
		'id'            => 'front-page-7',
		'description'   => esc_html__( 'Add widgets here.', '__theme_txtd' ),
		'before_widget' => '<section id="%1$s" class="widget widget--full %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title h3"><span>',
		'after_title'   => '</span></h2>',
	) );

	// Footer - Featured Area
	register_sidebar( array(
		'name'          => esc_html__( 'Footer - Featured Area', '__theme_txtd' ),
		'id'            => 'footer-featured',
		'description'   => esc_html__( 'You can only use this area in the PRO version of the theme.', '__theme_txtd' ),
	) );
}
// We use a latter priority to make sure that all these sidebars appear grouped
add_action( 'widgets_init', 'julia_widget_areas_init_front_page', 30 );

/**
 * Register the our custom widgets for use in Appearance -> Widgets
 */
function julia_custom_widgets_init() {
    /**
     * Load and register the custom Featured Posts Widgets
     */

    // The Featured Posts - Grid Widget
    require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/featured-posts/class-FeaturedPosts-GridWidget.php' );
    register_widget( 'Pixelgrade_FeaturedPosts_GridWidget' );

    // The Featured Posts - List Widget
    require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/featured-posts/class-FeaturedPosts-ListWidget.php' );
    register_widget( 'Pixelgrade_FeaturedPosts_ListWidget' );

    // The Featured Posts - 5 Cards Layout Widget
    require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/featured-posts/class-FeaturedPosts-5CardsWidget.php' );
    register_widget( 'Pixelgrade_FeaturedPosts_5CardsWidget' );

    // The Featured Posts - 6 Cards Layout Widget
    require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/featured-posts/class-FeaturedPosts-6CardsWidget.php' );
    register_widget( 'Pixelgrade_FeaturedPosts_6CardsWidget' );

    // The Featured Posts - Slideshow Widget
    require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/featured-posts/class-FeaturedPosts-SlideshowWidget.php' );
    register_widget( 'Pixelgrade_FeaturedPosts_SlideshowWidget' );

    // The Featured Posts - Carousel Widget
    require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/featured-posts/class-FeaturedPosts-CarouselWidget.php' );
    register_widget( 'Pixelgrade_FeaturedPosts_CarouselWidget' );

    /**
     * Load other custom widgets
     */

    // The Categories Image Grid Widget
    require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/class-CategoriesImageGridWidget.php' );
    register_widget( 'Pixelgrade_CategoriesImageGridWidget' );

	// The Promo Box Widget
	require_once pixelgrade_get_parent_theme_file_path( 'inc/widgets/class-PromoBoxWidget.php' );
	register_widget( 'Pixelgrade_PromoBoxWidget' );
}
add_action( 'widgets_init', 'julia_custom_widgets_init', 10 );
