<?php
/**
 * Lightweight SEO output. When an SEO plugin (Yoast, Rank Math, AIOSEO,
 * SEOPress, The SEO Framework) is active it takes over and this file only
 * keeps breadcrumbs JSON-LD off to avoid duplicates.
 *
 * Canonical URLs, robots.txt and the XML sitemap come from WordPress core.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a dedicated SEO plugin handling meta tags?
 *
 * @return bool
 */
function apba_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * Description for the current request.
 *
 * @return string
 */
function apba_meta_description() {
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post->post_excerpt ) {
			return wp_strip_all_tags( $post->post_excerpt );
		}
		if ( 'post' === $post->post_type ) {
			return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
		}
	}
	if ( is_category() && category_description() ) {
		return wp_strip_all_tags( category_description() );
	}
	return wp_strip_all_tags( apba_opt( 'apba_footer_about' ) );
}

/**
 * Meta description + Open Graph + Twitter card.
 */
function apba_meta_tags() {
	if ( apba_seo_plugin_active() ) {
		return;
	}
	$desc  = apba_meta_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'apba-wide' );
	} elseif ( has_site_icon() ) {
		$image = get_site_icon_url( 512 );
	}

	echo "\n";
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	if ( is_singular( 'post' ) ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
}
add_action( 'wp_head', 'apba_meta_tags', 2 );

/**
 * Schema.org JSON-LD: Organization (front page), Article (posts), BreadcrumbList.
 */
function apba_schema() {
	if ( apba_seo_plugin_active() ) {
		return;
	}
	$graph = array();
	$org   = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/#organization' ),
		'name'  => apba_opt( 'apba_org_name' ),
		'url'   => home_url( '/' ),
	);
	if ( apba_opt( 'apba_email' ) ) {
		$org['email'] = apba_opt( 'apba_email' );
	}
	if ( apba_opt( 'apba_phone' ) ) {
		$org['telephone'] = apba_opt( 'apba_phone' );
	}
	if ( apba_opt( 'apba_address' ) ) {
		$org['address'] = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => implode( ', ', array_filter( array_map( 'trim', explode( "\n", apba_opt( 'apba_address' ) ) ) ) ),
			'addressCountry' => 'SG',
		);
	}
	if ( has_custom_logo() ) {
		$org['logo'] = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
	}
	$same_as = array_values( array_filter( array( apba_opt( 'apba_social_linkedin' ), apba_opt( 'apba_social_facebook' ), apba_opt( 'apba_social_x' ), apba_opt( 'apba_social_youtube' ) ) ) );
	if ( $same_as ) {
		$org['sameAs'] = $same_as;
	}

	if ( is_front_page() ) {
		$graph[] = $org;
		$graph[] = array(
			'@type'     => 'WebSite',
			'@id'       => home_url( '/#website' ),
			'url'       => home_url( '/' ),
			'name'      => get_bloginfo( 'name' ),
			'publisher' => array( '@id' => home_url( '/#organization' ) ),
		);
	}

	if ( is_singular( 'post' ) ) {
		$article = array(
			'@type'            => 'Article',
			'headline'         => wp_strip_all_tags( get_the_title() ),
			'datePublished'    => get_the_date( 'c' ),
			'dateModified'     => get_the_modified_date( 'c' ),
			'mainEntityOfPage' => get_permalink(),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', (int) get_post_field( 'post_author' ) ),
			),
			'publisher'        => $org,
		);
		if ( has_post_thumbnail() ) {
			$article['image'] = get_the_post_thumbnail_url( null, 'apba-wide' );
		}
		$graph[] = $article;
	}

	$trail = apba_breadcrumb_trail();
	if ( count( $trail ) > 1 ) {
		$items = array();
		foreach ( $trail as $i => $crumb ) {
			$item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $crumb[0] ),
			);
			if ( $crumb[1] ) {
				$item['item'] = $crumb[1];
			}
			$items[] = $item;
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	if ( $graph ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'apba_schema', 20 );
