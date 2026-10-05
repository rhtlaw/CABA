<?php
/**
 * Appearance → APBA Site Setup screen and `wp apba setup` WP-CLI command.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the admin page.
 */
function apba_setup_menu() {
	add_theme_page(
		__( 'APBA Site Setup', 'apba' ),
		__( 'APBA Site Setup', 'apba' ),
		'manage_options',
		'apba-setup',
		'apba_setup_screen'
	);
}
add_action( 'admin_menu', 'apba_setup_menu' );

/**
 * Handle the form post.
 */
function apba_setup_handle() {
	if ( ! isset( $_POST['apba_setup_run'] ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'apba' ) );
	}
	check_admin_referer( 'apba_setup' );

	$importer = new APBA_Importer( ! empty( $_POST['apba_force'] ) );
	set_transient( 'apba_setup_log_' . get_current_user_id(), $importer->run(), 10 * MINUTE_IN_SECONDS );

	wp_safe_redirect( admin_url( 'themes.php?page=apba-setup&done=1' ) );
	exit;
}
add_action( 'admin_init', 'apba_setup_handle' );

/**
 * Render the screen.
 */
function apba_setup_screen() {
	$missing = APBA_Importer::missing_requirements();
	$log     = get_transient( 'apba_setup_log_' . get_current_user_id() );
	$map     = get_option( APBA_Importer::MAP_OPTION );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'APBA Site Setup', 'apba' ); ?></h1>
		<p><?php esc_html_e( 'Creates the APBA pages (built with free Elementor widgets), News/Articles categories and posts, Contact Form 7 forms, menus, and configures Elementor Site Settings (global colours, fonts, breakpoints). Existing published pages with the same slugs are kept.', 'apba' ); ?></p>

		<?php if ( $missing ) : ?>
			<div class="notice notice-warning inline"><p><strong><?php esc_html_e( 'Before running setup:', 'apba' ); ?></strong></p><ul style="list-style:disc;padding-left:20px">
				<?php foreach ( $missing as $m ) : ?>
					<li><?php echo esc_html( $m ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>

		<?php if ( $log && isset( $_GET['done'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-success inline"><p><strong><?php esc_html_e( 'Setup finished.', 'apba' ); ?></strong></p>
				<ul style="list-style:disc;padding-left:20px">
					<?php foreach ( (array) $log as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
				<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'View site', 'apba' ); ?></a></p>
			</div>
			<?php delete_transient( 'apba_setup_log_' . get_current_user_id() ); ?>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'apba_setup' ); ?>
			<p><label><input type="checkbox" name="apba_force" value="1"> <?php esc_html_e( 'Rebuild pages, posts, forms and menus previously created by this setup (overwrites edits made to them).', 'apba' ); ?></label></p>
			<p><button type="submit" name="apba_setup_run" value="1" class="button button-primary button-hero" <?php disabled( ! did_action( 'elementor/loaded' ) ); ?>><?php echo $map ? esc_html__( 'Run setup again', 'apba' ) : esc_html__( 'Run setup', 'apba' ); ?></button></p>
		</form>

		<h2><?php esc_html_e( 'After setup', 'apba' ); ?></h2>
		<ol>
			<li><?php esc_html_e( 'Upload the APBA logo under Appearance → Customize → Site Identity.', 'apba' ); ?></li>
			<li><?php esc_html_e( 'Check contact details and header button under Appearance → Customize → APBA Site Options.', 'apba' ); ?></li>
			<li><?php esc_html_e( 'Replace placeholder images in each page (Edit with Elementor) and in Media.', 'apba' ); ?></li>
			<li><?php esc_html_e( 'Migrate the full text of draft posts (Posts → Drafts) and publish them.', 'apba' ); ?></li>
			<li><?php esc_html_e( 'Confirm the form email recipient under Contact → Contact Forms → Mail.', 'apba' ); ?></li>
		</ol>
	</div>
	<?php
}

/**
 * Admin notice until setup has run.
 */
function apba_setup_notice() {
	if ( get_option( APBA_Importer::MAP_OPTION ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_apba-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'The APBA theme is active.', 'apba' ),
		esc_url( admin_url( 'themes.php?page=apba-setup' ) ),
		esc_html__( 'Run the one-click site setup →', 'apba' )
	);
}
add_action( 'admin_notices', 'apba_setup_notice' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Build the APBA site.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Rebuild content previously created by setup.
	 *
	 * ## EXAMPLES
	 *
	 *     wp apba setup --user=admin
	 *
	 * @param array $args       Positional.
	 * @param array $assoc_args Flags.
	 */
	$apba_cli = static function ( $args, $assoc_args ) {
		if ( ! get_current_user_id() ) {
			$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
			if ( $admins ) {
				wp_set_current_user( (int) $admins[0] );
			}
		}
		foreach ( APBA_Importer::missing_requirements() as $m ) {
			WP_CLI::warning( $m );
		}
		if ( ! did_action( 'elementor/loaded' ) ) {
			WP_CLI::error( 'Elementor is required.' );
		}
		( new APBA_Importer( ! empty( $assoc_args['force'] ) ) )->run();
		WP_CLI::success( 'APBA site setup complete.' );
	};
	WP_CLI::add_command( 'apba setup', $apba_cli );
}
