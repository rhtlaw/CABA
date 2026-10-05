<?php
/**
 * Shortcodes used inside Elementor's free "Shortcode" widget. They replace the
 * Elementor Pro Posts/Loop widgets and dynamic tags.
 *
 *   [apba_posts category="news" count="3" columns="3" pagination="no"]
 *   [apba_breadcrumbs]
 *   [apba_contact_info show="address,email,phone"]
 *   [apba_year]
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * [apba_posts] — responsive card grid of WordPress posts.
 *
 * Attributes:
 *   category   Category slug(s), comma separated. Empty = all posts.
 *   count      Posts per page (default 6).
 *   columns    Desktop columns 1–4 (default 3; tablet 2, mobile 1).
 *   pagination "yes" to show numbered pagination (uses ?pg=N).
 *   excerpt    "no" to hide excerpts.
 *   exclude_current "yes" to skip the post being viewed.
 *   empty      Message when there are no posts.
 *   heading    Card title tag: h2, h3 (default) or h4.
 *
 * @param array $atts Attributes.
 * @return string
 */
function apba_posts_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'category'        => '',
			'count'           => 6,
			'columns'         => 3,
			'pagination'      => 'no',
			'excerpt'         => 'yes',
			'exclude_current' => 'no',
			'offset'          => 0,
			'empty'           => __( 'No posts have been published yet.', 'apba' ),
			'heading'         => 'h3',
		),
		$atts,
		'apba_posts'
	);

	$paged    = 'yes' === $atts['pagination'] ? max( 1, absint( isset( $_GET['pg'] ) ? wp_unslash( $_GET['pg'] ) : 1 ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification
	$per_page = max( 1, min( 48, absint( $atts['count'] ) ) );
	$columns  = max( 1, min( 4, absint( $atts['columns'] ) ) );

	$query_args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $per_page,
		'paged'               => $paged,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => 'yes' !== $atts['pagination'],
	);
	if ( absint( $atts['offset'] ) && 'yes' !== $atts['pagination'] ) {
		$query_args['offset'] = absint( $atts['offset'] );
	}
	if ( $atts['category'] ) {
		$query_args['category_name'] = sanitize_text_field( str_replace( ' ', '', $atts['category'] ) );
	}
	if ( 'yes' === $atts['exclude_current'] && is_singular() ) {
		$query_args['post__not_in'] = array( get_queried_object_id() );
	}

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '<p class="apba-empty">' . esc_html( $atts['empty'] ) . '</p>';
	}

	$out = '<div class="apba-grid apba-grid--cols-' . $columns . '">';
	foreach ( $query->posts as $post ) {
		$out .= apba_post_card(
			$post->ID,
			array(
				'excerpt' => 'no' !== $atts['excerpt'],
				'heading' => $atts['heading'],
			)
		);
	}
	$out .= '</div>';

	if ( 'yes' === $atts['pagination'] ) {
		$out .= apba_pagination(
			(int) $query->max_num_pages,
			$paged,
			str_replace( '999999999', '%#%', esc_url_raw( add_query_arg( 'pg', 999999999 ) ) )
		);
	}

	wp_reset_postdata();
	return $out;
}
add_shortcode( 'apba_posts', 'apba_posts_shortcode' );

/**
 * [apba_breadcrumbs]
 *
 * @return string
 */
function apba_breadcrumbs_shortcode() {
	return apba_breadcrumbs();
}
add_shortcode( 'apba_breadcrumbs', 'apba_breadcrumbs_shortcode' );

/**
 * [apba_contact_info show="address,email,phone"] — reads Customizer values so
 * contact details are maintained in one place.
 *
 * @param array $atts Attributes.
 * @return string
 */
function apba_contact_info_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'show' => 'address,email,phone' ), $atts, 'apba_contact_info' );
	$show = array_map( 'trim', explode( ',', $atts['show'] ) );
	return apba_contact_list( $show );
}
add_shortcode( 'apba_contact_info', 'apba_contact_info_shortcode' );

/**
 * [apba_year] — current year.
 *
 * @return string
 */
function apba_year_shortcode() {
	return esc_html( wp_date( 'Y' ) );
}
add_shortcode( 'apba_year', 'apba_year_shortcode' );
