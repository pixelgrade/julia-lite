<?php
/**
 * Julia Lite Theme About Page logic.
 *
 * @package Julia Lite
 */

function julia_lite_admin_setup() {
	/**
	 * Load the About page class
	 */
	// phpcs:ignore
	require_once 'ti-about-page/class-ti-about-page.php';

	/*
	* About page instance
	*/
	$config = array(
		// Menu name under Appearance.
		'menu_name'               => esc_html__( 'About Julia Lite', 'julia-lite' ),
		// Page title.
		'page_name'               => esc_html__( 'About Julia Lite', 'julia-lite' ),
		/* translators: Main welcome title */
		'welcome_title'         => sprintf( esc_html__( 'Welcome to %s! - Version ', 'julia-lite' ), 'Julia Lite' ),
		// Main welcome content
		'welcome_content'       => esc_html__( ' Julia Lite is a free magazine-style theme with clean type, smart layouts and a design flexibility that makes it perfect for publishers of all kinds.', 'julia-lite' ),
		/**
		 * Tabs array.
		 *
		 * The key needs to be ONLY consisted from letters and underscores. If we want to define outside the class a function to render the tab,
		 * the will be the name of the function which will be used to render the tab content.
		 */
		'tabs'                    => array(
			'getting_started'  => esc_html__( 'Getting Started', 'julia-lite' ),
			'recommended_actions' => esc_html__( 'Recommended Actions', 'julia-lite' ),
			'recommended_plugins' => esc_html__( 'Useful Plugins','julia-lite' ),
			'support'       => esc_html__( 'Support', 'julia-lite' ),
			'changelog'        => esc_html__( 'Changelog', 'julia-lite' ),
			'free_pro'         => esc_html__( 'Free VS PRO', 'julia-lite' ),
		),
		// Support content tab.
		'support_content'      => array(
			'first' => array (
				'title' => esc_html__( 'Contact Support','julia-lite' ),
				'icon' => 'dashicons dashicons-sos',
				'text' => __( 'We want to make sure you have the best experience using Julia Lite. If you <strong>do not have a paid upgrade</strong>, please post your question in our community forums.','julia-lite' ),
				'button_label' => esc_html__( 'Contact Support','julia-lite' ),
				'button_link' => esc_url( 'https://wordpress.org/support/theme/julia-lite' ),
				'is_button' => true,
				'is_new_tab' => true
			),
			'second' => array(
				'title' => esc_html__( 'Documentation','julia-lite' ),
				'icon' => 'dashicons dashicons-book-alt',
				'text' => esc_html__( 'Need more details? Please check our full documentation for detailed information on how to use Julia Lite.','julia-lite' ),
				'button_label' => esc_html__( 'Read The Documentation','julia-lite' ),
				'button_link' => 'https://pixelgrade.com/julia-lite-documentation/',
				'is_button' => false,
				'is_new_tab' => true
			)
		),
		// Getting started tab
		'getting_started' => array(
			'first' => array(
				'title' => esc_html__( 'Go to Customizer','julia-lite' ),
				'text' => esc_html__( 'Using the WordPress Customizer you can easily customize every aspect of the theme.','julia-lite' ),
				'button_label' => esc_html__( 'Go to Customizer','julia-lite' ),
				'button_link' => esc_url( admin_url( 'customize.php' ) ),
				'is_button' => true,
				'recommended_actions' => false,
				'is_new_tab' => true
			),
			'second' => array (
				'title' => esc_html__( 'Recommended actions','julia-lite' ),
				'text' => esc_html__( 'We have compiled a list of steps for you, to take make sure the experience you will have using one of our products is very easy to follow.','julia-lite' ),
				'button_label' => esc_html__( 'Recommended actions','julia-lite' ),
				'button_link' => esc_url( admin_url( 'themes.php?page=julia-lite-welcome&tab=recommended_actions' ) ),
				'button_ok_label' => esc_html__( 'You are good to go!','julia-lite' ),
				'is_button' => false,
				'recommended_actions' => true,
				'is_new_tab' => false
			),
			'third' => array(
				'title' => esc_html__( 'Read the documentation','julia-lite' ),
				'text' => esc_html__( 'Need more details? Please check our full documentation for detailed information on how to use Julia Lite.','julia-lite' ),
				'button_label' => esc_html__( 'Documentation','julia-lite' ),
				'button_link' => 'https://pixelgrade.com/julia-lite-documentation/',
				'is_button' => false,
				'recommended_actions' => false,
				'is_new_tab' => true
			)
		),
		// Free vs pro array.
		'free_pro'                => array(
			'free_theme_name'     => 'Julia Lite',
			'pro_theme_name'      => 'Julia PRO',
			'pro_theme_link'      => 'https://pixelgrade.com/themes/julia-lite/?utm_source=julia-lite-clients&utm_medium=about-page&utm_campaign=julia-lite#pro',
			/* translators: View link */
			'get_pro_theme_label' => sprintf( __( 'Get %s', 'julia-lite' ), 'Julia Pro' ),
			'features'            => array(
				array(
					'title'       => esc_html__( 'Daring Design for Devoted Readers', 'julia-lite' ),
					'description' => esc_html__( 'Julia\'s design helps you stand out from the crowd and create an experience that your readers will love and talk about. With a flexible home page you have the chance to easily showcase appealing content with ease.', 'julia-lite' ),
					'is_in_lite'  => 'true',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Mobile-Ready For All Devices', 'julia-lite' ),
					'description' => esc_html__( 'Julia makes room for your readers to enjoy your articles on the go, no matter the device their using. We shaped everything to look amazing to your audience.', 'julia-lite' ),
					'is_in_lite'  => 'true',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Widgetized Sidebars To Keep Attention', 'julia-lite' ),
					'description' => esc_html__( 'Julia comes with a widget-based flexible system which allows you to add your favorite widgets all over the Front Page and on the right side of your articles.', 'julia-lite' ),
					'is_in_lite'  => 'true',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'New Widgets for Extra Flexiblity', 'julia-lite' ),
					'description' => esc_html__( 'Julia gives you extra ways to showcase your food articles in great style. Besides the Featured and Grid Posts widgets, the PRO version comes with much more: Slideshow Posts, Carousel, Promo Box, and Categories Images.', 'julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Advanced Widgets Options', 'julia-lite' ),
					'description' => esc_html__( 'Julia\'s PRO version comes with more widget options to display and filter posts. For instance, you can have far more control on setting the source of the posts or how they are displayed, everything to push the content to the right people and promote it by the blink of an eye.', 'julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Flexible Home Page Design', 'julia-lite' ),
					'description' => esc_html__( 'Julia\'s PRO version has more Widgets Area available to enable you to place widgets on Footer or Below the Post at the end of your articles.', 'julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Powerful Recipe Index', 'julia-lite' ),
					'description' => esc_html__( 'Keep track of your recipes with our cus­tom made recipe indexing system. Filter by Category, Course, Season and much more.', 'julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Sticky Menu and Reading Progress Bar', 'julia-lite' ),
					'description' => esc_html__( 'Used to keep the menu at the top of your page while you scroll, making it more accessible on whatever page you are navigating.  On articles pages, we\'re pushing this concept even further and improve it with a Progress Bar, and at the end, it will switch to a link to the next article so you can keep momentum and maintain the attention of your readers.', 'julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Flexible Color Scheme', 'julia-lite' ),
					'description' => esc_html__( 'Match your unique style in an easy and smart way by using an intuitive interface that you can fine-tune it until it fully represents you and matches your particular blogging needs.','julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Advanced Typography Settings', 'julia-lite' ),
					'description' => esc_html__( 'Adjust your fonts by taking advantage of a playground with 600+ fonts varieties you can wisely choose from at any moment.','julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Premium Support and Assistance', 'julia-lite' ),
					'description' => esc_html__( 'We offer ongoing customer support to help you get things done in due time. This way, you save energy and time, and focus on what brings you happiness. We know our products inside-out and we can lend a hand to help you save resources of all kinds.','julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'Friendly Self-Service', 'julia-lite' ),
					'description' => esc_html__( 'We give you full access to an in-depth documentation to get the job done as quickly as possible. We don\'t stay in your way by offering you full access to our self-help tool directly from your Dashboard.', 'julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				),
				array(
					'title'       => esc_html__( 'No Credit Footer Link', 'julia-lite' ),
					'description' => esc_html__( 'You can easily remove the “Theme: Julia Lite by Pixelgrade” copyright from the footer area and make the theme yours from start to finish.', 'julia-lite' ),
					'is_in_lite'  => 'false',
					'is_in_pro'   => 'true',
				)
			),
		),
		// Plugins array.
		'recommended_plugins'        => array(
			'already_activated_message' => esc_html__( 'Already activated', 'julia-lite' ),
			'version_label' => esc_html__( 'Version: ', 'julia-lite' ),
			'install_label' => esc_html__( 'Install and Activate', 'julia-lite' ),
			'activate_label' => esc_html__( 'Activate', 'julia-lite' ),
			'deactivate_label' => esc_html__( 'Deactivate', 'julia-lite' ),
			'content'                   => array(
				array(
					'slug' => 'jetpack'
				),
				array(
					'slug' => 'wordpress-seo'
				),
//				array(
//					'slug' => 'gridable'
//				)
			),
		),
		// Required actions array.
		'recommended_actions'        => array(
			'install_label' => esc_html__( 'Install and Activate', 'julia-lite' ),
			'activate_label' => esc_html__( 'Activate', 'julia-lite' ),
			'deactivate_label' => esc_html__( 'Deactivate', 'julia-lite' ),
			'content'            => array(
				'jetpack' => array(
					'title'       => 'Jetpack',
					'description' => __( 'It is highly recommended that you install Jetpack so you can enable the <b>Portfolio</b> content type for adding and managing your projects. Plus, Jetpack provides a whole host of other useful things for you site.', 'julia-lite' ),
					'check'       => defined( 'JETPACK__VERSION' ),
					'plugin_slug' => 'jetpack',
					'id' => 'jetpack'
				),
			),
		),
	);
	Julia_Lite_About_Page::init( $config );
}
add_action( 'after_setup_theme', 'julia_lite_admin_setup' );
