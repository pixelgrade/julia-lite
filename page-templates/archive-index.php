<?php
/**
 * Template Name: Archive Index
 *
 * The template for displaying the widgetized archive index.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Julia
 * @since 1.0.0
 */

//let the template parts know about our location
$location = pixelgrade_set_location( 'page archive-index' );

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
        <main id="main" class="site-main" role="main">

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

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="u-container-sides-spacing">
                        <div class="o-wrapper u-container-width">
                            <div class="o-layout">
                                <div class="o-layout__main  widget-area  widget-area--archive-index">

                                    <?php
                                    // allow others to prevent this from displaying
                                    if ( apply_filters( 'pixelgrade_display_entry_header', true, $location ) ) {
                                        the_title( '<h2 class="h2  page-title">', '</h2>' );
                                    } else {
                                        the_title( '<h2 class="h2  screen-reader-text">', '</h2>' );
                                    } ?>

                                    <?php pixelgrade_get_sidebar( 'archive-content' ); ?>

                                </div><!-- .o-layout__main -->

	                            <div class="o-layout__side  widget-area  widget-area--side">
	                                <?php pixelgrade_get_sidebar(); ?>
	                            </div>

                            </div><!-- .widget-area -->
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
