<?php
/**
 * Small, safe front-end performance tweaks.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

// Emoji detection script and styles are not needed for this site.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Header clutter.
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );

/**
 * Block-library CSS is only needed on posts written with the block editor.
 */
function apba_trim_block_styles() {
	if ( is_singular() && 'builder' === get_post_meta( get_queried_object_id(), '_elementor_edit_mode', true ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'apba_trim_block_styles', 100 );

/**
 * Comments are not used on this site's posts; avoid loading comment-reply.js.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_dequeue_script( 'comment-reply' );
	},
	100
);

/**
 * Prefer WebP for generated sub-sizes when the server supports it (WP 5.8+).
 *
 * @param array $formats Output formats map.
 * @return array
 */
function apba_webp_subsizes( $formats ) {
	if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		$formats['image/jpeg'] = 'image/webp';
	}
	return $formats;
}
add_filter( 'image_editor_output_format', 'apba_webp_subsizes' );
