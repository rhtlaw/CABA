<?php
/**
 * Fallback template (posts index, search and anything without a more specific template).
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() && ! is_front_page() ) {
	$apba_title = single_post_title( '', false );
} elseif ( is_search() ) {
	/* translators: %s: search query. */
	$apba_title = sprintf( __( 'Search results for “%s”', 'apba' ), get_search_query() );
} else {
	$apba_title = wp_strip_all_tags( get_the_archive_title() );
}
$apba_desc = is_search() ? '' : wp_strip_all_tags( get_the_archive_description() );

apba_page_banner( $apba_title, $apba_desc );
?>
<section class="apba-section">
	<div class="apba-container">
		<?php if ( have_posts() ) : ?>
			<div class="apba-grid apba-grid--cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					echo apba_post_card( get_the_ID(), array( 'heading' => 'h2' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				endwhile;
				?>
			</div>
			<?php
			global $wp_query;
			echo apba_pagination( (int) $wp_query->max_num_pages, max( 1, (int) get_query_var( 'paged' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
		<?php else : ?>
			<p class="apba-empty"><?php esc_html_e( 'Nothing has been published here yet.', 'apba' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
