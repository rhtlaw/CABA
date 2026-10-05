<?php
/**
 * Site header: top contact bar, logo, primary menu (WordPress menu) and CTA.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

$apba_email = apba_opt( 'apba_email' );
$apba_phone = apba_opt( 'apba_phone' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'apba' ); ?></a>

<?php $apba_has_topbar = apba_opt( 'apba_topbar' ) && ( $apba_email || $apba_phone ); ?>
<header id="site-header" class="apba-header<?php echo $apba_has_topbar ? ' has-topbar' : ''; ?>" role="banner">
	<?php if ( $apba_has_topbar ) : ?>
		<div class="apba-topbar">
			<div class="apba-container apba-topbar__inner">
				<p class="apba-topbar__name"><?php echo esc_html( apba_opt( 'apba_org_name' ) ); ?></p>
				<ul class="apba-topbar__contact">
					<?php if ( $apba_email ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( $apba_email ) ); ?>"><?php apba_icon( 'mail' ); ?><span><?php echo esc_html( antispambot( $apba_email ) ); ?></span></a></li>
					<?php endif; ?>
					<?php if ( $apba_phone ) : ?>
						<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $apba_phone ) ); ?>"><?php apba_icon( 'phone' ); ?><span><?php echo esc_html( $apba_phone ); ?></span></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<div class="apba-header__main">
		<div class="apba-container apba-header__inner">
			<div class="apba-brand">
				<?php apba_site_logo(); ?>
			</div>

			<button type="button" class="apba-menu-toggle" aria-controls="apba-primary-nav" aria-expanded="false">
				<span class="apba-menu-toggle__bars" aria-hidden="true"><span></span><span></span><span></span></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'apba' ); ?></span>
			</button>

			<nav id="apba-primary-nav" class="apba-nav" aria-label="<?php esc_attr_e( 'Primary', 'apba' ); ?>">
				<?php
				if ( has_nav_menu( 'apba-primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'apba-primary',
							'container'      => false,
							'menu_class'     => 'apba-menu',
							'menu_id'        => 'apba-primary-menu',
							'depth'          => 3,
							'walker'         => new APBA_Nav_Walker(),
							'fallback_cb'    => false,
						)
					);
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					echo '<p class="apba-nav__empty"><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Assign a menu to “Primary navigation”', 'apba' ) . '</a></p>';
				}
				?>
				<?php if ( apba_opt( 'apba_cta_label' ) ) : ?>
					<a class="apba-btn apba-btn--accent apba-header__cta" href="<?php echo esc_url( apba_link( apba_opt( 'apba_cta_url' ) ) ); ?>"><?php echo esc_html( apba_opt( 'apba_cta_label' ) ); ?></a>
				<?php endif; ?>
			</nav>
		</div>
	</div>
</header>

<main id="content" class="apba-main" tabindex="-1">
