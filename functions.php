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
		 * If you're building a theme based on '__theme_txtd', use a find and replace
		 * to change '__theme_txtd' to the name of your theme in all the template files.
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
		add_theme_support( 'custom-logo', apply_filters( 'julia_header_site_logo', array(
			'height'      => 600,
			'width'       => 1360,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array(
				'site-title',
				'site-description-text',
			)
		) ) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'comment-list',
			'gallery',
			'caption',
		) );

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

	/* Default Self-hosted Fonts should be loaded when Customify is off */
	if ( ! class_exists( 'PixCustomifyPlugin' ) ) {
		wp_enqueue_style( 'julia-fonts-charter', julia_charter_font_url() );
		$main_style_deps[] = 'julia-fonts-charter';

		wp_enqueue_style( 'julia-fonts-hkgrotesk', julia_hkgrotesk_font_url() );
		$main_style_deps[] = 'julia-fonts-hkgrotesk';
	}

	/* The main theme stylesheet */
	if ( ! is_rtl() ) {
		wp_enqueue_style( 'julia-style', get_stylesheet_uri(), $main_style_deps, $theme->get( 'Version' ) );
	}

	/* Scripts */

	//The main script
	wp_enqueue_script( 'julia-commons-scripts', get_theme_file_uri( '/assets/js/commons.js' ), array( 'jquery' ), $theme->get( 'Version' ), true );
	wp_enqueue_script( 'julia-scripts', get_theme_file_uri( '/assets/js/app.bundle.js' ), array( 'julia-commons-scripts' ), $theme->get( 'Version' ), true );

	$localization_array = array(
		'ajaxurl'      => admin_url( 'admin-ajax.php' ),
	);

	wp_localize_script( 'julia-main-scripts', 'juliaStrings', $localization_array );
}
add_action( 'wp_enqueue_scripts', 'julia_scripts' );

function julia_load_wp_admin_style() {
	wp_register_style( 'julia_wp_admin_css', get_template_directory_uri() . '/admin.css', false, '1.0.0' );
	wp_enqueue_style( 'julia_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'julia_load_wp_admin_style' );

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

add_theme_support( 'starter-content', array(
	// Julia Lite
	'attachments' => array(
		'image-first-post' => array(
			'post_title' => _x( 'Mystery Dinner at Fire and Ice attachment', 'julia-lite'),
			'file' => 'assets/images/berries-blackberries-breakfast.jpg',
		),
		'image-second-post' => array(
			'post_title' => _x( 'Mystery Dinner at Thicket attachment', 'julia-lite'),
			'file' => 'assets/images/blackberries-bowl-delicious.jpg',
		),
		'image-third-post' => array(
			'post_title' => _x( 'Mystery Dinner and My Winter Workshop attachment', 'julia-lite'),
			'file' => 'assets/images/bowl-breakfast-cereal-bowl.jpg',
		),
		'image-fourth-post' => array(
			'post_title' => _x( 'Mystery Dinner at Noble Root attachment', 'julia-lite'),
			'file' => 'assets/images/bowl-coffee-cooking.jpg',
		),
		'image-fifth-post' => array(
			'post_title' => _x( 'Salted Almond Chocolate Cake with Violets attachment', 'julia-lite'),
			'file' => 'assets/images/cooking-cuisine-dish.jpg',
		),
	),
	'posts' => array(
		'homepage' => array(
			'post_type' => 'page',
			'post_title' => _x( 'Home', 'Julia Lite starter content' ),
			'template' => '/page-templates/front-page.php'
		),
		'blog',
		'mystery-dinner-fire-ice-post' => array(
			'post_type' => 'post',
			'post_title' => 'Mystery Dinner at Fire and Ice',
			'thumbnail' => '{{image-first-post}}',
			'post_content' => '<p class="intro">To have a thorough understanding of their goodness one must not only read about them but taste them. They are the staple diet in many foreign countries and in the Armour brand the <em>native flavoring</em> has been done with remarkable faithfulness—so much so that large quantities are shipped from this country every week to the countries where they originated.</p>
<span class="dropcap">T</span>he frequent experience of the cook living in the country or suburbs these days to receive unexpected visits from friends who are touring in automobiles, and she finds she must have something attractive, dainty and nourishing ready at a moment\'s notice to supplement the cup of tea or coffee so welcome after a hot, dusty trip.',
		),
		'mistery-dinner-at-thicket' => array(
			'post_tyep' => 'post',
			'post_title' => 'Mystery Dinner at Thicket',
			'thumbnail' => '{{image-second-post}}',
			'post_content' => '<p class="intro">If you are fortunate enough to possess a wide porch or a stretch of lawn do not forget your less fortunate friends, and give an occasional informal party there while the weather is still fine. Food always tastes so much better in the fresh air and when friends are present it makes the affair nothing more than a kind of glorified picnic. There are few more pleasant ways of entertaining than by giving a porch party.</p>
<span class="dropcap">P</span>repare as much as possible early in the day. If you have sandwiches wrap them in a damp napkin; if cold drinks are wanted have them well chilled, your glasses and straws handy, have your silver and china ready at hand so that when your guests arrive you may devote your time and attention to them. The following menus are not hard to prepare and the dishes will be found most palatable and suited to every purse: <a href="#">Veribest Canned Fruits</a>, the standby of the cook who combines economy of time with excellence of quality, are used in many of them.

<strong>There is a wide range of these meats delicious and many ways of using them.</strong> Every pantry should have at least one shelf devoted to them so that the housewife need never be at a loss for the basis of a good meal. In so many cases of convalescence where the appetite is flagging and the digestion weak, ham and bacon are prescribed, both for their tonic and nutritive value.

Buying a whole ham at a time is the best and most economical way of buying ham, as experience will prove. It can be boiled or baked whole and sliced for whatever purpose intended. When baked ham is broiled for breakfast it requires to be cooked just long enough to get hot all the way through.'
		),
		'mystery-dinner-and-my-winter-workshop' => array(
			'post_type' => 'post',
			'post_title' => 'Mystery Dinner and My Winter Workshop',
			'thumbnail' => '{{image-third-post}}',
			'post_content' => '<p class="intro">We are drawing nearer and nearer to an appreciation of the power which Cookery wields in the preservation of health, but this awakening as to its value has been too tardy, indeed, it has been from a slumber of centuries. Not that good Cookery has not been practised from time immemorial, but its recognition from a scientific point of view is almost within our own day; and even at the present time, dietetics, or that department of medicine which relates to food and diet, is only gradually assuming a position which is destined ultimately to become second to none.</p>
<span class="dropcap">T</span>he frequent experience of the cook living in the country or suburbs these days to receive unexpected visits from friends who are touring in automobiles, and she finds she must have something attractive, dainty and nourishing ready at a moment\'s notice to supplement the cup of tea or coffee so welcome after a hot, dusty trip.

<strong>It is a wise plan to keep a variety of Summer Sausage on hand, as in a very few minutes delicious sandwiches may be prepared with this, these sandwiches having the charm of novelty.</strong> It is impossible to deal in a short article with the many varieties of Summer Sausage, but there are three or four which can be touched upon. To have a thorough understanding of their goodness one must not only read about them but taste them.'
		),
		'mystery-dinner-at-noble-root' => array(
			'post_type' => 'post',
			'post_title' => 'Mystery Dinner at Noble Root',
			'thumbnail' => '{{image-fourth-post}}',
			'post_content' => '<p class="intro">If you are fortunate enough to possess a wide porch or a stretch of lawn do not forget your less fortunate friends, and give an occasional informal party there while the weather is still fine. <em>Food always tastes so much better in the fresh air</em> and when friends are present it makes the affair nothing more than a kind of glorified picnic. There are few more pleasant ways of entertaining than by giving a porch party.</p>
<span class="dropcap">P</span>repare as much as possible early in the day. If you have sandwiches wrap them in a damp napkin; if cold drinks are wanted have them well chilled, your glasses and straws handy, have your silver and china ready at hand so that when your guests arrive you may devote your time and attention to them. <em>The following menus are not hard to prepare and the dishes will be found most palatable and suited to every purse</em>: Veribest Canned Meats, the standby of the housewife who combines economy of time with excellence of quality, are used in many of them.

<strong>There is a wide range of these meats delicious and many ways of using them.</strong> Every pantry should have at least one shelf devoted to them so that the housewife need never be at a loss for the basis of a good meal. In so many cases of convalescence where the appetite is flagging and the digestion weak, ham and bacon are prescribed, both for their tonic and nutritive value.'
		),
		'salted-almond-chocolate-cake-with-violets' => array(
			'post_type' => 'post',
			'post_title' => 'Salted Almond Chocolate Cake with Violets',
			'thumbnail' => '{{image-fifth-post}}',
			'post_content' => '<p class="intro">Tea, with which we are all so familiar, is in reality a number of dried rolled leaves of the tea plant, <em>Camellia Thea</em>, cultivated chiefly in China and the contiguous countries. It is used excessively throughout Australasia—for has it not been shown that our four million people use more of this beverage than the millions who inhabit Continental Europe, if Russia be excepted?</p>
<span class="dropcap">H</span>ow to make pure food, better food and to economize on the cost of same is just now taxing the attention and ingenuity of domestic science teachers and food experts generally. The average cook is intensely interested in the result of these findings, and must keep in touch with them to keep up with the times and run her home in an <em>intelligent and economical as well as healthful routine</em>.

<strong>It is impossible to deal in a short article with the many varieties of Summer Sausce, but there are three or four which can be touched upon.</strong> To have a thorough understanding of their goodness one must not only read about them but taste them. They are the staple diet in many foreign countries and in the <a href="#">Armour brand</a> the native flavoring has been done with remarkable faithfulness—so much so that large quantities are shipped from this country every week to the countries where they originated.'
		),
		'salted-caramel-apricot-pots-de-creme' => array(
			'post_type' => 'post',
			'post_title' => 'Salted Caramel & Apricot Pots De Creme',
			'thumbnail' => '{{image-first-post}}',
			'post_content' => '<p class="intro">There is inspiration in the art that enters into the production of a French dinner, in the perfect balance of every item from hors <em>d\'oeuvre to café noir</em>, in the ways with seasoning that work miracles with left-overs and preserve the daily routine of three meals a day from the deadly monotony of the <em>American régime</em>, in the garnishings that glorify the most insignificant concoctions into objects of appetising beauty and in the sauces that elevate indifferent dishes into the realm of creations and enable a French cook to turn out a dinner fit for capricious young gods from what an American cook wastes in preparing one.</p>
<span class="dropcap">H</span>ow to make pure food, better food and to economize on the cost of same is just now taxing the attention and ingenuity of domestic <a href="#">science teachers</a> and food experts generally. The average housewife is intensely interested in the result of these findings, and must keep in touch with them to keep up with the times and run her home in an <em>intelligent and economical as well as healthful routine.</em>

<strong>To have a thorough understanding of their goodness one must not only read about them but taste them.</strong> They are the staple diet in many foreign countries and in the Armour brand the native flavoring has been done with remarkable faithfulness—so much so that large quantities are shipped from this country every week to the countries where they originated.'
		),
		'about' => array(
			'post_type' => 'page',
			'post_title' => _x( 'Style guide', 'Julia Lite starter content' ),
			'post_content' => _x( '<p class="intro">You can add an introductory larger size text to your articles by simply wrapping a paragraph in a p tag with the CSS class of “intro”. Put simply, larger text will usually be read before smaller text.</p>
We paid a lot of attention to getting the basics of our typography right in the new WordPress Blog theme. The purpose of this page is to help determine what default settings are with CSS and to make sure that all possible elements are included. For example we looked at headings. Lovely headings.
<h2>Heading Two Formatting</h2>
<strong><span class="dropcap">D</span>ropcap</strong> can be added by wrapping the first letter of the first word in a span tag with the CSS class of “dropcap”. Instead of using the body text font, we use the display font from our titles. This also ties the two elements together if the display font works well with the body text.openers.

We\'ve considered the needs of cooks that want to start their recipe journal, so we are styled the recipe format in such way that it beautifully displays the preparation steps.

<h3>Heading Three</h3>
You can also use a purely decorative font. There are thousands of decorative typefaces, and most of them aren’t appropriate for use in a book’s body text.
<blockquote>Blockquotes are a great way to display and format quotations. Insert beautiful quotes using<em><strong> the “quote” button</strong></em> from the visual editor. To add an author just wrap its name in a <em><strong>cite</strong></em> tag.</blockquote>
Tables are useful for lay­outs where text needs to be po­si­tioned side-by-side or float­ing at spe­cif­ic lo­ca­tions on the page. If mak­ing these is frus­trat­ing with the usu­al lay­out tools, try us­ing a table.

&nbsp;
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td><strong>Type</strong></td>
<td><strong>Font</strong></td>
<td><strong>Description</strong></td>
</tr>
<tr>
<td><strong>Humanist</strong></td>
<td>Sabon</td>
<td>Closely connected to calligraphy</td>
</tr>
<tr>
<td><strong>Transitional</strong></td>
<td>Baskerville</td>
<td>More abstract and less organic</td>
</tr>
<tr>
<td><strong>Modern</strong></td>
<td>Bodoni</td>
<td>Note the thin, straight serifs</td>
</tr>
<tr>
<td><strong>Slab Serif</strong></td>
<td>Clarendon</td>
<td>Egyptian typefaces have heavy serifs</td>
</tr>
</tbody>
</table>

To <span class="highlight">highlight</span> a text, you simply need to wrap it into a &lt;span&gt; with the class “highlight”. This can be done in the Text editor view.
<h4>Heading four</h4>

To split the text in a <strong>two columns layout</strong> you can use our <a href="https://wordpress.org/plugins/gridable/">Gridable</a> plugin. Having multiple columns allows for a very versatile ad grid, and, traditionally, newspapers were in the business of selling ads.babies in slings around front.There is a general rule that one line of unjustified text should have around 9-12 words. For justified text these numbers are around 10-15 words. Since some words are longer and some shorter this is not a perfect measurement.

<h2>Image Styles</h2>
Welcome to <strong>image alignment!</strong> The best way to demonstrate the ebb and flow of the various image positioning options is to nestle them snuggly among an ocean of words. Grab a paddle and let’s get started.

[caption id="attachment_33" align="aligncenter" width="2500"]<img class="wp-image-33 size-full" src="https://cdn.demos.pixelgrade.com/wp-content/uploads/sites/27/2017/03/Pumpkin-Pancakes-by-Eva-Kosmas-Flores-8.jpg" alt="" width="2500" height="1667" /> The image above happens to be a centered full-width example.[/caption]

The rest of this paragraph is filler for the sake of seeing the text wrap around a <strong>right aligned image</strong>.

As you can see there should be some space above, below, and to the left of the image. The text should not be creeping on the image. Creeping is just not right. Images need breathing room too. Let them speak like you words.<img class="alignright wp-image-43 size-large" src="https://cdn.demos.pixelgrade.com/wp-content/uploads/sites/27/2017/03/Pumpkin-Pancakes-by-Eva-Kosmas-Flores-683x1024.jpg" alt="" width="640" height="960" /><span style="font-size: 1em; line-height: 1.5;">
As you can see there should be some space above, below, and to the left of the image. The text should not be creeping on the image. Creeping is just not right. Images need breathing room too. Let them speak like you words.</span>

Let them do their jobs without any hassle from the text. In about one more sentence here, we’ll see that the text moves from the right of the image down below the image in seamless transition.

Don’t let anyone else tell you differently. In just a bit here, you should see the text start to wrap below the left aligned image and settle in nicely. There should still be plenty of room and everything should be sitting pretty. Yeah… Just like that. It never felt so good to be right.

Let them do their jobs without any hassle from the text. In about one more sentence here, we’ll see that the text moves from the right of the image down below the image in seamless transition.

And now we’re going to shift things to the left align. Again, there should be  plenty of room above, below, and to the right of the image. Just look at him there… Hey guy! Way to rock that left side. I don’t care what the right aligned image says, you look great.
<img class="alignleft wp-image-39 size-large" src="https://cdn.demos.pixelgrade.com/wp-content/uploads/sites/27/2017/03/Pumpkin-Pancakes-by-Eva-Kosmas-Flores-13-1024x683.jpg" alt="" width="640" height="427" />Don’t let anyone else tell you differently. In just a bit here, you should see the text start to wrap below the left aligned image and settle in nicely. There should still be plenty of room and everything should be sitting pretty. Yeah… Just like that. It never felt so good to be right.

And that’s a wrap, yo! You survived the tumultuous waters of alignment. In just a bit here, you should see the text start to wrap below the right aligned image and settle in nicely. There should still be plenty of room and everything should be sitting pretty.', 'Julia Lite starter content' ),
		),
	),
	'options' => array(
		'show_on_front' => 'page',
		'page_on_front' => '{{homepage}}',
		'page_for_posts' => '{{blog}}',
	),
	'nav_menus' => array(
		'primary-right' => array(
			'name' => __( 'Main Menu', 'julia-lite' ),
			'items' => array(
				'link_home',
				'page_blog',
				'about' => array(
					'type' => 'post_type',
					'object' => 'page',
					'object_id' => '{{about}}',
				),
			),
		),
	),
	'widgets' => array(
		'front-page-1' => array(
			'featured-posts-widget' => array(
				'featured-posts-5cards',
				array(
					'title' => 'Featured Posts',
					'source' => 'recent',
					'prevent_duplicate_posts' => false,
					'image_ratio' => 'portrait',
					'primary_meta' => 'category',
					'secondary_meta' => 'none',
				),
			),
		),
		'front-page-2' => array(
			'widget-featuredposts' => array(
				'featured-posts-grid',
				array(
					'title' => 'Posts Grid',
					'source' => 'recent',
					'number' => 6,
					'prevent_duplicate_posts' => false,
					'columns' => '3',
					'image_ratio' => 'portrait',
					'primary_meta' => 'category',
					'secondary_meta' => 'date',
					'view_more_label' => 'View More',
				),
			),
		),
	),
) );

function post_status_customizer_change( $query ) {
	if ( ( 1 == get_option( 'fresh_site' ) ) && is_customize_preview() )
		$query->set('post_status', 'auto-draft');
}

add_action('pre_get_posts','post_status_customizer_change');