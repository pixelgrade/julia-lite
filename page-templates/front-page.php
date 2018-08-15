<?php
/**
 * Template Name: Front Page
 *
 * The template for displaying the widgetized front page.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Julia
 * @since 1.0.0
 */

//let the template parts know about our location
$location = pixelgrade_set_location( 'page front-page' );

// Here get the content width class
$content_width_class = 'u-content-width';

pixelgrade_get_header(); ?>

<?php
/**
 * pixelgrade_before_main_content hook.
 *
 * @hooked nothing() - 10 (outputs nothing)
 */
do_action( 'pixelgrade_before_main_content', $location );
?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main  u-content-bottom-spacing" role="main">

            <?php
            /**
             * pixelgrade_before_loop hook.
             *
             * @hooked nothing - 10 (outputs nothing)
             */
            do_action( 'pixelgrade_before_loop', $location );
            ?>

            <?php while ( have_posts() ) : the_post(); ?>

                <?php
                /**
                 * pixelgrade_before_loop_entry hook.
                 *
                 * @hooked julia_custom_page_css() - 10 (outputs the page's custom css)
                 */
                do_action( 'pixelgrade_before_loop_entry', $location );
                ?>

                <?php
                $header_class = '';
                if ( ! apply_filters( 'pixelgrade_display_entry_header', false, $location ) ) {
                    $header_class .= 'screen-reader-text';
                }
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="u-container-sides-spacing">
                        <div class="o-wrapper u-container-width">

                            <header class="entry-header <?php echo esc_attr( $header_class ); ?>">

                                <?php
                                /**
                                 * pixelgrade_before_entry_title hook.
                                 *
                                 * @hooked pixelgrade_the_hero() - 10 (outputs the hero markup)
                                 */
                                do_action( 'pixelgrade_before_entry_title', $location );
                                ?>

                                <?php the_title( '<h1 class="entry-title  c-page-header__title">', '</h1>' ); ?>

                                <?php
                                /**
                                 * pixelgrade_after_entry_title hook.
                                 *
                                 * @hooked nothing() - 10 (outputs nothing)
                                 */
                                do_action( 'pixelgrade_after_entry_title', $location );
                                ?>

                            </header><!-- .entry-header -->

                            <div class="o-layout">
                                <?php
                                // The Full Width Area #1
                                pixelgrade_get_sidebar( 'front-page-fullwidth-1' );
                                ?>

                                <?php
                                // The Content Area #1
                                pixelgrade_get_sidebar( 'front-page-content-1' );
                                // The Sidebar Area #1
                                pixelgrade_get_sidebar( 'front-page-sidebar-1' );
                                ?>

                                <?php
                                // The Full Width Area #2
                                pixelgrade_get_sidebar( 'front-page-fullwidth-2' );
                                ?>

                                <?php
                                // The Content Area #2
                                pixelgrade_get_sidebar( 'front-page-content-2' );
                                // The Sidebar Area #2
                                pixelgrade_get_sidebar( 'front-page-sidebar-2' );
                                ?>

                                <?php
                                // The Full Width Area #3
                                pixelgrade_get_sidebar( 'front-page-fullwidth-3' );
                                ?>
                            </div>

                        </div><!-- .o-wrapper .u-container-width -->
                    </div><!-- .u-container-sides-spacing -->
                </article><!-- #post-## -->

                <?php
                /**
                 * pixelgrade_after_loop_entry hook.
                 *
                 * @hooked nothing() - 10 (outputs nothing)
                 */
                do_action( 'pixelgrade_after_loop_entry', $location );
                ?>

            <?php endwhile; // End of the loop. ?>

            <?php
            /**
             * pixelgrade_after_loop hook.
             *
             * @hooked nothing() - 10 (outputs nothing)
             */
            do_action( 'pixelgrade_after_loop', $location );
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
/**
 * pixelgrade_after_main_content hook.
 *
 * @hooked nothing - 10 (outputs nothing)
 */
do_action( 'pixelgrade_after_main_content', $location );
?>

<?php
pixelgrade_get_footer();
