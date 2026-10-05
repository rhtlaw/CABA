<?php
/**
 * APBA child theme bootstrap.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

define( 'APBA_VERSION', '1.0.0' );
define( 'APBA_DIR', get_stylesheet_directory() );
define( 'APBA_URI', get_stylesheet_directory_uri() );

require_once APBA_DIR . '/inc/setup.php';
require_once APBA_DIR . '/inc/customizer.php';
require_once APBA_DIR . '/inc/class-apba-nav-walker.php';
require_once APBA_DIR . '/inc/template-tags.php';
require_once APBA_DIR . '/inc/shortcodes.php';
require_once APBA_DIR . '/inc/forms.php';
require_once APBA_DIR . '/inc/seo.php';
require_once APBA_DIR . '/inc/performance.php';

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once APBA_DIR . '/inc/importer/class-apba-elementor-builder.php';
	require_once APBA_DIR . '/inc/importer/class-apba-importer.php';
	require_once APBA_DIR . '/inc/importer/admin-page.php';
}
