<?php
/**
 * Single news item / article.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$apba_cat = apba_primary_category( get_the_ID() );
	?>
	<section class="apba-page-banner apba-page-banner--post">
		<div class="apba-container apba-container--narrow">
			<?php echo apba_breadcrumbs(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $apba_cat ) : ?>
				<p class="apba-eyebrow"><a href="<?php echo esc_url( apba_category_listing_url( $apba_cat ) ); ?>"><?php echo esc_html( $apba_cat->name ); ?></a></p>
			<?php endif; ?>
			<h1 class="apba-page-banner__title"><?php the_title(); ?></h1>
			<?php echo apba_post_meta( get_the_ID(), array( 'date', 'author' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'apba-article' ); ?>>
		<div class="apba-container apba-container--narrow">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="apba-article__media">
					<?php
					the_post_thumbnail(
						'apba-wide',
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'sizes'         => '(max-width: 860px) 100vw, 860px',
						)
					);
					?>
					<?php if ( get_the_post_thumbnail_caption() ) : ?>
						<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>

			<div class="apba-article__content apba-prose">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="apba-page-links" aria-label="' . esc_attr__( 'Post pages', 'apba' ) . '">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<?php
			$apba_tags = get_the_tag_list( '<ul class="apba-tags"><li>', '</li><li>', '</li></ul>' );
			if ( $apba_tags && ! is_wp_error( $apba_tags ) ) {
				echo $apba_tags; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>

			<?php if ( get_the_author_meta( 'description' ) ) : ?>
				<aside class="apba-author" aria-label="<?php esc_attr_e( 'About the author', 'apba' ); ?>">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 72, '', '', array( 'loading' => 'lazy' ) ); ?>
					<div>
						<p class="apba-author__name"><?php the_author(); ?></p>
						<p class="apba-author__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
					</div>
				</aside>
			<?php endif; ?>

			<?php
			the_post_navigation(
				array(
					'prev_text'          => '<span class="apba-postnav__label">' . esc_html__( 'Previous', 'apba' ) . '</span><span class="apba-postnav__title">%title</span>',
					'next_text'          => '<span class="apba-postnav__label">' . esc_html__( 'Next', 'apba' ) . '</span><span class="apba-postnav__title">%title</span>',
					'in_same_term'       => (bool) $apba_cat,
					'aria_label'         => __( 'More posts', 'apba' ),
					'class'              => 'apba-postnav',
				)
			);
			?>
		</div>
	</article>

	<?php
	$apba_related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'cat'                 => $apba_cat ? $apba_cat->term_id : 0,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $apba_related->have_posts() ) :
		?>
		<section class="apba-section apba-section--surface apba-related" aria-labelledby="apba-related-title">
			<div class="apba-container">
				<h2 id="apba-related-title" class="apba-section-title"><?php echo esc_html( $apba_cat ? sprintf( /* translators: %s: category name. */ __( 'Latest %s', 'apba' ), $apba_cat->name ) : __( 'Latest Posts', 'apba' ) ); ?></h2>
				<div class="apba-grid apba-grid--cols-3">
					<?php
					foreach ( $apba_related->posts as $apba_rel ) {
						echo apba_post_card( $apba_rel->ID ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
	wp_reset_postdata();
endwhile;

get_footer();
