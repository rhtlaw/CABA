<?php
/**
 * Pages not built with Elementor (e.g. edited in the block editor).
 * Pages built with Elementor use the "Elementor Full Width" template instead.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	apba_page_banner( get_the_title() );
	?>
	<section class="apba-section">
		<div class="apba-container apba-container--narrow apba-prose">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
