<?php
/**
 * Not found.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

get_header();
apba_page_banner( __( 'Page not found', 'apba' ) );
?>
<section class="apba-section">
	<div class="apba-container apba-container--narrow apba-prose">
		<p><?php esc_html_e( 'The page you are looking for may have moved or no longer exists. Try a search or return to the home page.', 'apba' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="apba-btn apba-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'apba' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
