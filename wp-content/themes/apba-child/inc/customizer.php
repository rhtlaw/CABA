<?php
/**
 * Customizer: organisation details, header CTA and footer content.
 *
 * Everything the header and footer print comes from here or from WordPress
 * menus, so administrators never edit PHP for routine changes.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values, also used by the importer.
 *
 * @return array<string,mixed>
 */
function apba_customizer_defaults() {
	return array(
		'apba_org_name'          => 'ASEAN Plus Business Alliance (APBA)',
		'apba_org_short'         => 'APBA',
		'apba_logo_tagline'      => 'Formerly China-ASEAN Business Alliance (CABA)',
		'apba_address'           => "1 Paya Lebar Link #06-08\nPLQ 2 Paya Lebar Quarter\nSingapore 408533",
		'apba_email'             => 'secretariat@caba.org.sg',
		'apba_phone'             => '',
		'apba_cta_label'         => 'Register Your Interest',
		'apba_cta_url'           => '/register-your-interest/',
		'apba_topbar'            => true,
		'apba_sticky_header'     => true,
		'apba_footer_about'      => 'The ASEAN Plus Business Alliance (APBA), formerly known as China-ASEAN Business Alliance, was established in Singapore in 2013 and serves as a platform to promote international business ties and collaboration for the ASEAN region.',
		'apba_footer_cta_text'   => 'Members can take part in trade missions, conferences, exhibitions and networking events organised by APBA and its partners.',
		'apba_copyright'         => '© {year} ASEAN Plus Business Alliance (APBA). All rights reserved.',
		'apba_social_linkedin'   => '',
		'apba_social_facebook'   => '',
		'apba_social_x'          => '',
		'apba_social_youtube'    => '',
		'apba_default_card_image' => 0,
	);
}

/**
 * Read a theme option with its default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function apba_opt( $key ) {
	$defaults = apba_customizer_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Register Customizer panel.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function apba_customize_register( $wp_customize ) {
	$d = apba_customizer_defaults();

	$wp_customize->add_panel(
		'apba_panel',
		array(
			'title'       => __( 'APBA Site Options', 'apba' ),
			'description' => __( 'Organisation details, header and footer content. Colours and fonts are set in Elementor → Site Settings.', 'apba' ),
			'priority'    => 30,
		)
	);

	$sections = array(
		'apba_org'    => __( 'Organisation & Contact', 'apba' ),
		'apba_header' => __( 'Header', 'apba' ),
		'apba_footer' => __( 'Footer', 'apba' ),
		'apba_social' => __( 'Social Links', 'apba' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'apba_panel' ) );
	}

	$fields = array(
		// key, section, label, type, sanitize.
		array( 'apba_org_name', 'apba_org', __( 'Organisation name', 'apba' ), 'text', 'sanitize_text_field' ),
		array( 'apba_org_short', 'apba_org', __( 'Short name / acronym', 'apba' ), 'text', 'sanitize_text_field' ),
		array( 'apba_logo_tagline', 'apba_org', __( 'Text-logo tagline (shown only when no logo image is set)', 'apba' ), 'text', 'sanitize_text_field' ),
		array( 'apba_address', 'apba_org', __( 'Address (one line per row)', 'apba' ), 'textarea', 'sanitize_textarea_field' ),
		array( 'apba_email', 'apba_org', __( 'Public email', 'apba' ), 'email', 'sanitize_email' ),
		array( 'apba_phone', 'apba_org', __( 'Public phone (leave empty to hide)', 'apba' ), 'text', 'sanitize_text_field' ),
		array( 'apba_cta_label', 'apba_header', __( 'Header button label', 'apba' ), 'text', 'sanitize_text_field' ),
		array( 'apba_cta_url', 'apba_header', __( 'Header button link', 'apba' ), 'text', 'esc_url_raw' ),
		array( 'apba_topbar', 'apba_header', __( 'Show top contact bar', 'apba' ), 'checkbox', 'wp_validate_boolean' ),
		array( 'apba_sticky_header', 'apba_header', __( 'Sticky header', 'apba' ), 'checkbox', 'wp_validate_boolean' ),
		array( 'apba_footer_about', 'apba_footer', __( 'Footer description', 'apba' ), 'textarea', 'sanitize_textarea_field' ),
		array( 'apba_footer_cta_text', 'apba_footer', __( 'Footer membership text', 'apba' ), 'textarea', 'sanitize_textarea_field' ),
		array( 'apba_copyright', 'apba_footer', __( 'Copyright line ({year} is replaced with the current year)', 'apba' ), 'text', 'sanitize_text_field' ),
		array( 'apba_social_linkedin', 'apba_social', 'LinkedIn URL', 'url', 'esc_url_raw' ),
		array( 'apba_social_facebook', 'apba_social', 'Facebook URL', 'url', 'esc_url_raw' ),
		array( 'apba_social_x', 'apba_social', 'X (Twitter) URL', 'url', 'esc_url_raw' ),
		array( 'apba_social_youtube', 'apba_social', 'YouTube URL', 'url', 'esc_url_raw' ),
	);

	foreach ( $fields as $f ) {
		list( $key, $section, $label, $type, $sanitize ) = $f;
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => $type,
			)
		);
	}

	$wp_customize->add_setting(
		'apba_default_card_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'apba_default_card_image',
			array(
				'label'     => __( 'Fallback image for posts without a featured image', 'apba' ),
				'section'   => 'apba_org',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'apba_customize_register' );
