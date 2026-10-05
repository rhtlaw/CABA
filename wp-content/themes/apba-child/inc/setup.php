<?php
/**
 * Theme supports, menus, image sizes and asset loading.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme supports and menu locations.
 */
function apba_setup() {
	load_child_theme_textdomain( 'apba', APBA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'apba-primary' => __( 'Primary navigation (header)', 'apba' ),
			'apba-footer'  => __( 'Footer quick links', 'apba' ),
			'apba-legal'   => __( 'Footer legal links', 'apba' ),
		)
	);

	// 16:9 card image, matching the source site's news thumbnails.
	add_image_size( 'apba-card', 720, 405, true );
	add_image_size( 'apba-wide', 1280, 720, true );
}
add_action( 'after_setup_theme', 'apba_setup', 20 );

/**
 * Hello Elementor ships its own header/footer stylesheet; this theme renders its
 * own header and footer, so that stylesheet is not needed.
 */
add_filter( 'hello_elementor_header_footer', '__return_false' );

/**
 * Front-end assets. Only two small files are loaded globally.
 */
function apba_enqueue_assets() {
	wp_enqueue_style(
		'apba-main',
		APBA_URI . '/assets/css/main.css',
		array(),
		APBA_VERSION
	);

	wp_enqueue_script(
		'apba-navigation',
		APBA_URI . '/assets/js/navigation.js',
		array(),
		APBA_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
// Late priority so theme rules print after Elementor's kit CSS and win specificity ties.
add_action( 'wp_enqueue_scripts', 'apba_enqueue_assets', 9999 );

/**
 * Body classes used by the stylesheet.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function apba_body_classes( $classes ) {
	if ( get_theme_mod( 'apba_sticky_header', true ) ) {
		$classes[] = 'apba-has-sticky-header';
	}
	if ( is_singular() && 'builder' === get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) ) {
		$classes[] = 'apba-elementor-page';
	}
	return $classes;
}
add_filter( 'body_class', 'apba_body_classes' );

/**
 * Hello Elementor defaults this theme replaces: its menu locations, its
 * page-title/comment stylesheet, its 800px content width and its excerpt-based
 * meta description (inc/seo.php outputs a fuller one).
 */
add_filter( 'hello_elementor_register_menus', '__return_false' );
add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' );
add_filter( 'hello_elementor_description_meta_tag', '__return_false' );
add_filter(
	'hello_elementor_content_width',
	static function () {
		return 1200;
	}
);

/**
 * Excerpt length for cards.
 *
 * @return int
 */
function apba_excerpt_length() {
	return 26;
}
add_filter( 'excerpt_length', 'apba_excerpt_length', 20 );

/**
 * Plain ellipsis instead of "[...]".
 *
 * @return string
 */
function apba_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'apba_excerpt_more' );
