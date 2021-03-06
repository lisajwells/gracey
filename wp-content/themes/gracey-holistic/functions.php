<?php
/**
 * Gracey Holistic.
 *
 */

// Start the engine.
include_once( get_template_directory() . '/lib/init.php' );

// Setup Theme.
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

// Helper functions.
include_once( get_stylesheet_directory() . '/lib/helper-functions.php' );

// Include customizer CSS.
include_once( get_stylesheet_directory() . '/lib/output.php' );

// Add image upload and color select to theme customizer.
require_once( get_stylesheet_directory() . '/lib/customize.php' );

// Add the required WooCommerce functions.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php' );

// Add the required WooCommerce custom CSS.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php' );

// Include notice to install Genesis Connect for WooCommerce.
include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php' );

// Set Localization (do not remove).
add_action( 'after_setup_theme', 'ghh_localization_setup' );
function ghh_localization_setup(){
	load_child_theme_textdomain( 'infinity-pro', get_stylesheet_directory() . '/languages' );
}

// Child theme (do not remove).
define( 'CHILD_THEME_NAME', 'Gracey Holistic' );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/infinity/' );
define( 'CHILD_THEME_VERSION', '1.1.2' );

// Enqueue scripts and styles.
add_action( 'wp_enqueue_scripts', 'ghh_enqueue_scripts_styles' );
function ghh_enqueue_scripts_styles() {

	wp_enqueue_style( 'ghh-fonts', '//fonts.googleapis.com/css?family=Cardo:700|Lato:400,700', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'ghh-ionicons', '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css', array(), CHILD_THEME_VERSION );

	wp_enqueue_script( 'ghh-match-height', get_stylesheet_directory_uri() . '/js/match-height.js', array( 'jquery' ), '0.5.2', true );
	wp_enqueue_script( 'ghh-global', get_stylesheet_directory_uri() . '/js/global.js', array( 'jquery', 'ghh-match-height' ), '1.0.0', true );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'infinity-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menus' . $suffix . '.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
	wp_localize_script(
		'infinity-responsive-menu',
		'genesis_responsive_menu',
		infinity_responsive_menu_settings()
	);

}

// Move child theme CSS to load after beaver builder CSS
/**
 * Remove Genesis child theme style sheet
 * @uses  genesis_meta  <genesis/lib/css/load-styles.php>
*/
remove_action( 'genesis_meta', 'genesis_load_stylesheet' );

/**
 * Enqueue Genesis child theme style sheet at higher priority
 * @uses wp_enqueue_scripts <http://codex.wordpress.org/Function_Reference/wp_enqueue_style>
 */
add_action( 'wp_enqueue_scripts', 'genesis_enqueue_main_stylesheet', 15 );

// Define our responsive menu settings.
function infinity_responsive_menu_settings() {

	$settings = array(
		'mainMenu'         => __( 'Menu', 'infinity-pro' ),
		'menuIconClass'    => 'ionicons-before ion-ios-drag',
		'subMenu'          => __( 'Submenu', 'infinity-pro' ),
		'subMenuIconClass' => 'ionicons-before ion-chevron-down',
		'menuClasses'      => array(
			'others' => array(
				'.nav-primary',
			),
		),
	);

	return $settings;

}

// Add HTML5 markup structure.
add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

// Add accessibility support.
add_theme_support( 'genesis-accessibility', array( '404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links' ) );

// Add viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );

// Add support for custom header.
add_theme_support( 'custom-header', array(
	'width'           => 400,
	'height'          => 130,
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'flex-height'     => true,
) );

// Add image sizes.
add_image_size( 'mini-thumbnail', 75, 75, TRUE );
add_image_size( 'team-member', 600, 600, TRUE );

// Add support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Remove header right widget area.
unregister_sidebar( 'header-right' );

// Remove secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Remove site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Remove output of primary navigation right extras.
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

// Remove navigation meta box.
add_action( 'genesis_theme_settings_metaboxes', 'infinity_remove_genesis_metaboxes' );
function infinity_remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );
}

// Remove skip link for primary navigation.
add_filter( 'genesis_skip_links_output', 'infinity_skip_links_output' );
function infinity_skip_links_output( $links ) {

	if ( isset( $links['genesis-nav-primary'] ) ) {
		unset( $links['genesis-nav-primary'] );
	}

	return $links;

}

// Rename primary and secondary navigation menus.
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Header Menu', 'infinity-pro' ), 'secondary' => __( 'Footer Menu', 'infinity-pro' ) ) );

// Reposition primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Reposition the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 5 );

// Add offscreen content if active.
add_action( 'genesis_before_header', 'infinity_offscreen_content_output' );
function infinity_offscreen_content_output() {

	$button = '<button class="offscreen-content-toggle"><i class="icon ion-ios-close-empty"></i> <span class="screen-reader-text">' . __( 'Hide Offscreen Content', 'infinity-pro' ) . '</span></button>';

	if ( is_active_sidebar( 'offscreen-content' ) ) {

		echo '<div class="offscreen-content-icon"><button class="offscreen-content-toggle"><i class="icon ion-ios-more"></i> <span class="screen-reader-text">' . __( 'Show Offscreen Content', 'infinity-pro' ) . '</span></button></div>';

	}

	genesis_widget_area( 'offscreen-content', array(
		'before' => '<div class="offscreen-content"><div class="offscreen-container"><div class="widget-area">' . $button . '<div class="wrap">',
		'after'  => '</div></div></div></div>',
	));

}

// Reduce secondary navigation menu to one level depth.
add_filter( 'wp_nav_menu_args', 'infinity_secondary_menu_args' );
function infinity_secondary_menu_args( $args ) {

	if ( 'secondary' != $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;

	return $args;

}

// Modify size of the Gravatar in the author box.
add_filter( 'genesis_author_box_gravatar_size', 'infinity_author_box_gravatar' );
function infinity_author_box_gravatar( $size ) {
	return 100;
}

// Modify size of the Gravatar in the entry comments.
add_filter( 'genesis_comment_list_args', 'infinity_comments_gravatar' );
function infinity_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;

	return $args;

}

// Setup widget counts.
function infinity_count_widgets( $id ) {

	global $sidebars_widgets;

	if ( isset( $sidebars_widgets[ $id ] ) ) {
		return count( $sidebars_widgets[ $id ] );
	}

}

// Determine the widget area class.
function infinity_widget_area_class( $id ) {

	$count = infinity_count_widgets( $id );

	$class = '';

	if ( $count == 1 ) {
		$class .= ' widget-full';
	} elseif ( $count % 3 == 1 ) {
		$class .= ' widget-thirds';
	} elseif ( $count % 4 == 1 ) {
		$class .= ' widget-fourths';
	} elseif ( $count % 2 == 0 ) {
		$class .= ' widget-halves uneven';
	} else {
		$class .= ' widget-halves';
	}

	return $class;

}

// Add support for 3-column footer widgets.
add_theme_support( 'genesis-footer-widgets', 3 );

// Register widget areas.
genesis_register_sidebar( array(
	'id'          => 'front-page-1',
	'name'        => __( 'Front Page 1', 'infinity-pro' ),
	'description' => __( 'This is the front page 1 section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-2',
	'name'        => __( 'Front Page 2', 'infinity-pro' ),
	'description' => __( 'This is the front page 2 section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-3',
	'name'        => __( 'Front Page 3', 'infinity-pro' ),
	'description' => __( 'This is the front page 3 section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-4',
	'name'        => __( 'Front Page 4', 'infinity-pro' ),
	'description' => __( 'This is the front page 4 section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-5',
	'name'        => __( 'Front Page 5', 'infinity-pro' ),
	'description' => __( 'This is the front page 5 section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-6',
	'name'        => __( 'Front Page 6', 'infinity-pro' ),
	'description' => __( 'This is the front page 6 section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-7',
	'name'        => __( 'Front Page 7', 'infinity-pro' ),
	'description' => __( 'This is the front page 7 section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'lead-capture',
	'name'        => __( 'Lead Capture', 'infinity-pro' ),
	'description' => __( 'This is the lead capture section.', 'infinity-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'offscreen-content',
	'name'        => __( 'Offscreen Content', 'infinity-pro' ),
	'description' => __( 'This is the offscreen content section.', 'infinity-pro' ),
) );

// Add a custom body class
add_filter( 'body_class', function( $classes ) {
    return array_merge( $classes, array( 'ghh' ) );
} );

// Remove edit link from posts
add_filter( 'edit_post_link', '__return_false' );

// Allow shortcode in widgets
add_filter('widget_text', 'do_shortcode');

/** Force content-sidebar layout on single posts only*/
add_filter( 'genesis_pre_get_option_site_layout', 'ghh_content_sidebar_layout_single_posts' );
/**
* @author Brad Dalton
* @link http://wpsites.net/web-design/change-layout-genesis/
*/
function ghh_content_sidebar_layout_single_posts( $opt ) {
if ( is_single() || is_page( 'blog' ) ) {
    $opt = 'content-sidebar';
    return $opt;

    }
}

/** Change the footer text **/
add_filter( 'genesis_footer_creds_text', 'ghh_footer_copyright_text' );
/**
 * Change Genesis Footer Credit Copyright line
 * @link http://wpbeaches.com/changing-genesis-theme-copyright-line-footer-wordpress/
 */
function ghh_footer_copyright_text () {
	$copyright = '<div class="creditline"><p class="sitemap-link"><a href="'.get_bloginfo( 'url' ).'/sitemap">Sitemap A</a>/<a href="/page_tags-sitemap.xml">B</a> | Areas Served around <a href="/">Boston</a>, <a href="/acupuncture-massachusetts/">Massachusetts</a> | Offices in <a href="/acupuncture-brookline-ma/">Brookline</a> and <a href="/acupuncture-belmont-ma/">Belmont</a></p><p>Copyright &copy; ' . date('Y') . ' &middot; <a href="'.get_bloginfo( 'url' ).'">Gracey Holistic Health</a> &middot; All&nbsp;Rights&nbsp;Reserved.</div>';

	return $copyright;
}

// Translate the word Name on the BB Subscribe form module
function my_text_with_context_translations( $translated, $text, $context, $domain ) {
  if ( 'fl-builder' == $domain ) {
    if ( 'Name' == $text && 'First and last name.' == $context ) {
      $translated = 'First Name';
    }
  }
  return $translated;
}
add_filter( 'gettext_with_context', 'my_text_with_context_translations', 10, 4 );
