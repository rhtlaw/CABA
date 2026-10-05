<?php
/**
 * Template helpers shared by templates and shortcodes.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Turn a site-relative path ("/membership/") into an absolute URL.
 *
 * @param string $url URL or path.
 * @return string
 */
function apba_link( $url ) {
	if ( is_string( $url ) && 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
		return home_url( $url );
	}
	return $url;
}

/**
 * Print a small inline SVG icon (no icon font needed).
 *
 * @param string $name Icon name.
 */
function apba_icon( $name ) {
	$paths = array(
		'mail'     => '<path d="M3 5h18v14H3z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M3 6l9 7 9-7" fill="none" stroke="currentColor" stroke-width="1.8"/>',
		'phone'    => '<path d="M6.6 10.8a15 15 0 006.6 6.6l2.2-2.2a1 1 0 011-.25 11.4 11.4 0 003.6.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1z" fill="currentColor"/>',
		'pin'      => '<path d="M12 2a7 7 0 00-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z" fill="currentColor"/>',
		'calendar' => '<path d="M4 6h16v14H4zM4 10h16M8 3v4M16 3v4" fill="none" stroke="currentColor" stroke-width="1.8"/>',
		'user'     => '<circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6" fill="none" stroke="currentColor" stroke-width="1.8"/>',
		'folder'   => '<path d="M3 6h7l2 2h9v11H3z" fill="none" stroke="currentColor" stroke-width="1.8"/>',
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8"/>',
		'linkedin' => '<path d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05C20.6 8.65 21 11.2 21 14.5V21h-4v-5.8c0-1.4-.03-3.2-1.95-3.2-1.95 0-2.25 1.52-2.25 3.1V21H9z" fill="currentColor"/>',
		'facebook' => '<path d="M14 8h3V4h-3c-2.8 0-4.5 1.8-4.5 4.6V11H7v4h2.5v7h4v-7h3l.5-4h-3.5V8.8c0-.5.3-.8.5-.8z" fill="currentColor"/>',
		'x'        => '<path d="M17.5 3h3.2l-7 8 8.3 10h-6.5l-5-6.2L4.8 21H1.6l7.5-8.6L1.2 3h6.6l4.5 5.7zm-1.1 16.2h1.8L6.7 4.7H4.8z" fill="currentColor"/>',
		'youtube'  => '<path d="M22 8.2a3 3 0 00-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 002 8.2 31 31 0 001.6 12 31 31 0 002 15.8a3 3 0 002.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 002.1-2.1 31 31 0 00.4-3.8 31 31 0 00-.4-3.8zM10 15V9l5.2 3z" fill="currentColor"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return;
	}
	echo '<svg class="apba-icon apba-icon--' . esc_attr( $name ) . '" aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24">' . $paths[ $name ] . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup.
}

/**
 * Print the logo (Customizer → Site Identity) or a text logo fallback.
 *
 * @param string $context "header" or "footer".
 */
function apba_site_logo( $context = 'header' ) {
	$home = esc_url( home_url( '/' ) );
	if ( has_custom_logo() ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		echo '<a class="apba-logo apba-logo--image" href="' . $home . '" rel="home">';
		echo wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class'         => 'apba-logo__img',
				'alt'           => apba_opt( 'apba_org_name' ),
				'loading'       => 'header' === $context ? 'eager' : 'lazy',
				'fetchpriority' => 'header' === $context ? 'high' : 'auto',
			)
		);
		echo '</a>';
		return;
	}

	printf(
		'<a class="apba-logo apba-logo--text" href="%1$s" rel="home"><span class="apba-logo__mark" aria-hidden="true">%2$s</span><span class="apba-logo__text"><span class="apba-logo__name">%3$s</span>%4$s</span></a>',
		$home, // Escaped above.
		esc_html( apba_opt( 'apba_org_short' ) ),
		esc_html( preg_replace( '/\s*\([^)]*\)\s*$/', '', apba_opt( 'apba_org_name' ) ) ),
		apba_opt( 'apba_logo_tagline' ) ? '<span class="apba-logo__tagline">' . esc_html( apba_opt( 'apba_logo_tagline' ) ) . '</span>' : ''
	);
}

/**
 * Contact details list (address, email, phone) from the Customizer.
 *
 * @param string[] $show Which items to include.
 * @return string HTML.
 */
function apba_contact_list( $show = array( 'address', 'email', 'phone' ) ) {
	$items   = array();
	$address = apba_opt( 'apba_address' );
	$email   = apba_opt( 'apba_email' );
	$phone   = apba_opt( 'apba_phone' );

	ob_start();
	if ( in_array( 'address', $show, true ) && $address ) {
		echo '<li class="apba-contact__item">';
		apba_icon( 'pin' );
		echo '<address>' . nl2br( esc_html( $address ) ) . '</address></li>';
	}
	if ( in_array( 'email', $show, true ) && $email ) {
		echo '<li class="apba-contact__item">';
		apba_icon( 'mail' );
		echo '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a></li>';
	}
	if ( in_array( 'phone', $show, true ) && $phone ) {
		echo '<li class="apba-contact__item">';
		apba_icon( 'phone' );
		echo '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a></li>';
	}
	$items = ob_get_clean();

	return $items ? '<ul class="apba-contact">' . $items . '</ul>' : '';
}

/**
 * Breadcrumb trail as an array of [label, url|null].
 *
 * @return array<int,array{0:string,1:?string}>
 */
function apba_breadcrumb_trail() {
	$trail = array( array( __( 'Home', 'apba' ), home_url( '/' ) ) );

	if ( is_front_page() ) {
		return array();
	}

	if ( is_singular( 'post' ) ) {
		$cat = apba_primary_category( get_queried_object_id() );
		if ( $cat ) {
			$listing = apba_category_listing_url( $cat );
			$trail[] = array( $cat->name, $listing );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$trail[] = array( single_term_title( '', false ), null );
	} elseif ( is_search() ) {
		/* translators: %s: search query. */
		$trail[] = array( sprintf( __( 'Search: %s', 'apba' ), get_search_query() ), null );
	} elseif ( is_404() ) {
		$trail[] = array( __( 'Page not found', 'apba' ), null );
	} elseif ( is_archive() ) {
		$trail[] = array( wp_strip_all_tags( get_the_archive_title() ), null );
	} elseif ( is_home() ) {
		$trail[] = array( single_post_title( '', false ), null );
	}

	return $trail;
}

/**
 * Breadcrumbs HTML.
 *
 * @return string
 */
function apba_breadcrumbs() {
	$trail = apba_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return '';
	}
	$out  = '<nav class="apba-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'apba' ) . '"><ol>';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		if ( $i === $last || null === $crumb[1] ) {
			$out .= '<li><span aria-current="' . ( $i === $last ? 'page' : 'false' ) . '">' . esc_html( $crumb[0] ) . '</span></li>';
		} else {
			$out .= '<li><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></li>';
		}
	}
	return $out . '</ol></nav>';
}

/**
 * First (Yoast/Rank Math primary if set) category of a post.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function apba_primary_category( $post_id ) {
	$primary = (int) get_post_meta( $post_id, '_yoast_wpseo_primary_category', true );
	if ( ! $primary ) {
		$primary = (int) get_post_meta( $post_id, 'rank_math_primary_category', true );
	}
	if ( $primary ) {
		$term = get_term( $primary, 'category' );
		if ( $term instanceof WP_Term ) {
			return $term;
		}
	}
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0] : null;
}

/**
 * Prefer the Elementor listing page with the category's slug (/news/, /articles/)
 * over the default category archive, matching the site's navigation.
 *
 * @param WP_Term $cat Category.
 * @return string
 */
function apba_category_listing_url( $cat ) {
	$page = get_page_by_path( $cat->slug );
	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return get_category_link( $cat );
}

/**
 * Card image for a post with fallbacks (featured → Customizer default → bundled placeholder).
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return string HTML.
 */
function apba_card_image( $post_id, $size = 'apba-card' ) {
	$alt = get_the_title( $post_id );
	if ( has_post_thumbnail( $post_id ) ) {
		$thumb_alt = get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true );
		return get_the_post_thumbnail(
			$post_id,
			$size,
			array(
				'loading' => 'lazy',
				'alt'     => $thumb_alt ? $thumb_alt : $alt,
				'sizes'   => '(max-width: 767px) 100vw, (max-width: 991px) 50vw, 400px',
			)
		);
	}
	$fallback = (int) apba_opt( 'apba_default_card_image' );
	if ( $fallback ) {
		return wp_get_attachment_image( $fallback, $size, false, array( 'loading' => 'lazy', 'alt' => '' ) );
	}
	return '<img src="' . esc_url( APBA_URI . '/assets/images/post-placeholder.jpg' ) . '" width="1200" height="675" alt="" loading="lazy" decoding="async">';
}

/**
 * Post metadata line (date, category, author).
 *
 * @param int      $post_id Post ID.
 * @param string[] $parts   Parts to show.
 * @return string HTML.
 */
function apba_post_meta( $post_id, $parts = array( 'date', 'category' ) ) {
	ob_start();
	echo '<ul class="apba-meta">';
	if ( in_array( 'date', $parts, true ) ) {
		echo '<li class="apba-meta__date">';
		apba_icon( 'calendar' );
		echo '<time datetime="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">' . esc_html( get_the_date( '', $post_id ) ) . '</time></li>';
	}
	if ( in_array( 'category', $parts, true ) ) {
		$cat = apba_primary_category( $post_id );
		if ( $cat ) {
			echo '<li class="apba-meta__cat">';
			apba_icon( 'folder' );
			echo '<a href="' . esc_url( apba_category_listing_url( $cat ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
		}
	}
	if ( in_array( 'author', $parts, true ) ) {
		$author_id = (int) get_post_field( 'post_author', $post_id );
		echo '<li class="apba-meta__author">';
		apba_icon( 'user' );
		echo '<span>' . esc_html( get_the_author_meta( 'display_name', $author_id ) ) . '</span></li>';
	}
	echo '</ul>';
	return ob_get_clean();
}

/**
 * Render a news/article card.
 *
 * @param int   $post_id Post ID.
 * @param array $args    Options: excerpt (bool), category (bool), heading (h2|h3).
 * @return string HTML.
 */
function apba_post_card( $post_id, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'excerpt'  => true,
			'category' => true,
			'heading'  => 'h3',
		)
	);
	$heading = in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading'] : 'h3';
	$url     = get_permalink( $post_id );
	$title   = get_the_title( $post_id );

	ob_start();
	?>
	<article class="apba-card">
		<a class="apba-card__media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo apba_card_image( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
		<div class="apba-card__body">
			<?php echo apba_post_meta( $post_id, $args['category'] ? array( 'date', 'category' ) : array( 'date' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<<?php echo esc_html( $heading ); ?> class="apba-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_html( $heading ); ?>>
			<?php if ( $args['excerpt'] ) : ?>
				<p class="apba-card__excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $post_id ) ) ); ?></p>
			<?php endif; ?>
			<a class="apba-card__more" href="<?php echo esc_url( $url ); ?>">
				<?php esc_html_e( 'Read More', 'apba' ); ?><span class="screen-reader-text"><?php echo ': ' . esc_html( $title ); ?></span>
				<?php apba_icon( 'arrow' ); ?>
			</a>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Page banner used by PHP templates (single post, archives, 404).
 * Elementor pages build the same banner from widgets so it stays editable.
 *
 * @param string $title    Title.
 * @param string $subtitle Optional subtitle HTML-escaped text.
 */
function apba_page_banner( $title, $subtitle = '' ) {
	?>
	<section class="apba-page-banner">
		<div class="apba-container">
			<?php echo apba_breadcrumbs(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<h1 class="apba-page-banner__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="apba-page-banner__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Numbered pagination markup.
 *
 * @param int $total   Total pages.
 * @param int $current Current page.
 * @param string $base Pagination base for paginate_links().
 * @return string
 */
function apba_pagination( $total, $current, $base = '' ) {
	if ( $total < 2 ) {
		return '';
	}
	$args = array(
		'total'     => $total,
		'current'   => max( 1, $current ),
		'mid_size'  => 1,
		'prev_text' => '<span aria-hidden="true">&lsaquo;</span><span class="screen-reader-text">' . esc_html__( 'Previous page', 'apba' ) . '</span>',
		'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'apba' ) . '</span><span aria-hidden="true">&rsaquo;</span>',
		'type'      => 'list',
	);
	if ( $base ) {
		$args['base']   = $base;
		$args['format'] = '';
	}
	$links = paginate_links( $args );
	return $links ? '<nav class="apba-pagination" aria-label="' . esc_attr__( 'Pagination', 'apba' ) . '">' . $links . '</nav>' : '';
}
