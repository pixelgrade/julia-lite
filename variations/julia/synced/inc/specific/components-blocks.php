<?php
/**
 * Custom functions related to the Components Blocks system.
 *
 * Development notice: This file is synced from the variations directory! Do not edit in the `inc` directory!
 *
 * @package Julia
 * @since 2.0.1
 */

/**
 * Register new blog blocks, besides the ones provided by the blog component.
 *
 * @param string $component_slug The component's slug.
 * @param array $component_config The component entire component config.
 */
function julia_register_blog_blocks( $component_slug, $component_config ) {

Pixelgrade_BlocksManager()->registerBlock(
    'blog/single-portrait', array(
    'type' => 'loop', // We need this to be a loop so all who rely on "in_the_loop" have an easy life.
    'blocks' => array(
    'blog/entry-thumbnail',
    'sidebar' => array(
				'extend'   => 'blog/side',
				'blocks'   => array( 'blog/sidebar' ),
				'wrappers' => array(
					'side' => array(
						'extend_classes' => 'widget-area--post',
					),
				),
    ),
    'layout' => array( 
				'blocks'   => array(
					'blog/entry-content',
					'sidebar-below-post' => array(
						'blocks' => array(
							'blog/sidebar-below-post',
						),
						'wrappers' => array(
							array(
								'classes' => 'entry-aside u-content-width'
							),
						),
					),
					'blog/entry-footer',
				),
				'wrappers' => array(
					'main' => array(
						'classes' => 'single-main clearfix'
					),
				),
    ),
    ),
    ) 
);

Pixelgrade_BlocksManager()->registerBlock(
    'blog/single-landscape', array(
    'type' => 'loop', // We need this to be a loop so all who rely on "in_the_loop" have an easy life.
    'blocks' => array(
    'layout' => array(
				'extend' => 'blog/layout',
				'wrappers' => array(
					'layout' => array(
						'extend_classes' => 'o-layout--blog'
					),
				),
				'blocks' => array(
					'main' => array(
						'extend' => 'blog/main',
						'blocks' => array(
							'blog/entry-thumbnail',
							'blog/entry-content',
							'sidebar-below-post' => array(
								'blocks' => array(
									'blog/sidebar-below-post',
								),
								'wrappers' => array(
									array(
										'classes' => 'entry-aside u-content-width'
									),
								),
							),
							'blog/entry-footer',
						),
					),
					'side' => array(
						'extend' => 'blog/side',
						'blocks' => array( 'blog/sidebar' ),
						'wrappers' => array(
							'side' => array(
								'extend_classes' => 'widget-area--post',
							),
						),
					),
				),
    ),
    ),
    ) 
);

Pixelgrade_BlocksManager()->registerBlock(
    'blog/single', array(
    'extend' => 'blog/default',
    'type' => 'loop', // We need this to be a loop so all who rely on "in_the_loop" have an easy life.
    'blocks' => array(
    'header' => array(
				'extend'   => 'blog/container',
				'blocks'   => array( 'blog/entry-header-single' ),
				'wrappers' => array(
					array(
						'priority' => 100,
						'classes'  => 'u-header-background'
					),
				),
    ),
    'layout' => array(
				'extend' => 'blog/container',
				'blocks' => array(
					'image-landscape' => array(
						'extend' => 'blog/single-landscape',
						'checks' => array(
							'callback' => 'pixelgrade_has_landscape_thumbnail'
						),
					),
					'image-portrait'  => array(
						'extend' => 'blog/single-portrait',
						'checks' => array(
							'callback' => 'pixelgrade_has_portrait_thumbnail'
						),
					),
					'image-none'      => array(
						'extend' => 'blog/single-landscape',
						'checks' => array(
							'relation' => 'AND',
							array(
								'callback' => 'pixelgrade_has_landscape_thumbnail',
								'compare' => 'NOT',
							),
							array(
								'callback' => 'pixelgrade_has_portrait_thumbnail',
								'compare' => 'NOT',
							)
						),
					),
				),
    ),
    'blog/related-posts',
    ),
    ) 
);

Pixelgrade_BlocksManager()->registerBlock(
    'blog/page', array(
    'extend' => 'blog/default',
    'type' => 'loop', // We need this to be a loop so all who rely on "in_the_loop" have an easy life.
    'blocks' => array(
    'content' => array(
				'extend' => 'blog/container',
				'blocks' => array(
					'layout' => array(
						'extend' => 'blog/layout',
						'wrappers' => array(
							'layout' => array(
								'extend_classes' => 'c-page  o-layout--blog'
							),
						),
						'blocks' => array(
							'main' => array(
								'extend' => 'blog/main',
								'blocks' => array(
									'blog/entry-header-page',
									'blog/entry-content',
									'blog/entry-footer',
								),
							),
							'side' => array(
								'extend' => 'blog/side',
								'blocks' => array( 'blog/sidebar' ),
								'checks' => array(
									array(
										'callback' => '__return_true',
										'args'     => array(),
									),
								),
							),
						),
					),
				),
    ),
    )
    ) 
);
}
add_action( 'pixelgrade_blog_after_register_blocks', 'julia_register_blog_blocks', 10, 2 );
