<?php
/**
 * Contact Form 7 integration (free form plugin used instead of Elementor Pro Forms).
 *
 * - Loads CF7 CSS/JS only on pages that actually render a form.
 * - Turns off CF7's automatic <p>/<br> insertion so the theme's grid markup is kept.
 * - Adds a honeypot field and a minimum-fill-time check as built-in spam
 *   protection (no third-party service needed; reCAPTCHA/Turnstile can still be
 *   enabled under Contact → Integration).
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

// Keep CF7 off pages without a form.
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );

/**
 * Enqueue CF7 assets the moment a form shortcode is rendered.
 *
 * @param string $output Shortcode output.
 * @param string $tag    Shortcode tag.
 * @return string
 */
function apba_cf7_enqueue_on_render( $output, $tag ) {
	if ( in_array( $tag, array( 'contact-form-7', 'contact-form' ), true ) ) {
		if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
			wpcf7_enqueue_scripts();
		}
		if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
			wpcf7_enqueue_styles();
		}
	}
	return $output;
}
add_filter( 'do_shortcode_tag', 'apba_cf7_enqueue_on_render', 10, 2 );

add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * Signed timestamp for the time-trap.
 *
 * @param int $time Unix time.
 * @return string
 */
function apba_form_token( $time ) {
	return $time . '.' . substr( hash_hmac( 'sha256', (string) $time, wp_salt( 'nonce' ) ), 0, 16 );
}

/**
 * Hidden timestamp field.
 *
 * @param array $fields Hidden fields.
 * @return array
 */
function apba_cf7_hidden_fields( $fields ) {
	$fields['apba_ts'] = apba_form_token( time() );
	return $fields;
}
add_filter( 'wpcf7_form_hidden_fields', 'apba_cf7_hidden_fields' );

/**
 * Honeypot input (visually hidden, skipped by keyboard and screen readers).
 *
 * @param string $html Form inner HTML.
 * @return string
 */
function apba_cf7_honeypot( $html ) {
	$field = '<div class="apba-hp" aria-hidden="true"><label for="apba-website-%1$s">' . esc_html__( 'Leave this field empty', 'apba' ) . '</label><input type="text" name="apba_website" id="apba-website-%1$s" value="" tabindex="-1" autocomplete="off"></div>';
	return sprintf( $field, esc_attr( wp_unique_id() ) ) . $html;
}
add_filter( 'wpcf7_form_elements', 'apba_cf7_honeypot' );

/**
 * Flag submissions that fill the honeypot or arrive faster than a human could type.
 *
 * @param bool $spam Current spam state.
 * @return bool
 */
function apba_cf7_spam_check( $spam ) {
	if ( $spam ) {
		return $spam;
	}
	// phpcs:disable WordPress.Security.NonceVerification -- CF7 validates its own nonce/REST request.
	if ( ! empty( $_POST['apba_website'] ) ) {
		return true;
	}
	$token = isset( $_POST['apba_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['apba_ts'] ) ) : '';
	// phpcs:enable
	$parts = explode( '.', $token );
	if ( 2 !== count( $parts ) || ! hash_equals( apba_form_token( (int) $parts[0] ), $token ) ) {
		return true;
	}
	return ( time() - (int) $parts[0] ) < 3;
}
add_filter( 'wpcf7_spam', 'apba_cf7_spam_check', 9 );
