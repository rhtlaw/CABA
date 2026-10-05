<?php
/**
 * Site footer: brand + description, quick links (WordPress menu), contact
 * details and membership CTA (Customizer), copyright with dynamic year.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

$apba_address = apba_opt( 'apba_address' );
$apba_email   = apba_opt( 'apba_email' );
$apba_phone   = apba_opt( 'apba_phone' );
$apba_socials = array_filter(
	array(
		'linkedin' => apba_opt( 'apba_social_linkedin' ),
		'facebook' => apba_opt( 'apba_social_facebook' ),
		'x'        => apba_opt( 'apba_social_x' ),
		'youtube'  => apba_opt( 'apba_social_youtube' ),
	)
);
?>
</main>

<footer id="site-footer" class="apba-footer" role="contentinfo">
	<div class="apba-container apba-footer__grid">
		<div class="apba-footer__col apba-footer__col--brand">
			<?php apba_site_logo( 'footer' ); ?>
			<?php if ( apba_opt( 'apba_footer_about' ) ) : ?>
				<p class="apba-footer__about"><?php echo esc_html( apba_opt( 'apba_footer_about' ) ); ?></p>
			<?php endif; ?>
			<?php if ( $apba_socials ) : ?>
				<ul class="apba-social" aria-label="<?php esc_attr_e( 'Social media', 'apba' ); ?>">
					<?php foreach ( $apba_socials as $apba_network => $apba_url ) : ?>
						<li><a href="<?php echo esc_url( $apba_url ); ?>" rel="noopener" target="_blank"><?php apba_icon( $apba_network ); ?><span class="screen-reader-text"><?php echo esc_html( ucfirst( $apba_network ) ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="apba-footer__col" aria-labelledby="apba-footer-links-title">
			<h2 class="apba-footer__title" id="apba-footer-links-title"><?php esc_html_e( 'Quick Links', 'apba' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'apba-footer',
					'container'      => false,
					'menu_class'     => 'apba-footer__menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<div class="apba-footer__col">
			<h2 class="apba-footer__title"><?php esc_html_e( 'Contact Us', 'apba' ); ?></h2>
			<?php echo apba_contact_list(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>
		</div>

		<div class="apba-footer__col apba-footer__col--cta">
			<h2 class="apba-footer__title"><?php esc_html_e( 'Membership', 'apba' ); ?></h2>
			<?php if ( apba_opt( 'apba_footer_cta_text' ) ) : ?>
				<p><?php echo esc_html( apba_opt( 'apba_footer_cta_text' ) ); ?></p>
			<?php endif; ?>
			<?php if ( apba_opt( 'apba_cta_label' ) ) : ?>
				<a class="apba-btn apba-btn--accent" href="<?php echo esc_url( apba_link( apba_opt( 'apba_cta_url' ) ) ); ?>"><?php echo esc_html( apba_opt( 'apba_cta_label' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>

	<div class="apba-footer__bottom">
		<div class="apba-container apba-footer__bottom-inner">
			<p class="apba-footer__copy"><?php echo esc_html( str_replace( '{year}', wp_date( 'Y' ), apba_opt( 'apba_copyright' ) ) ); ?></p>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'apba-legal',
					'container'      => 'nav',
					'container_class' => 'apba-footer__legal',
					'container_aria_label' => __( 'Legal', 'apba' ),
					'menu_class'     => 'apba-footer__legal-menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
