<?php
/**
 * Contact Form 7 form definitions (editable later under Contact → Contact Forms).
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Countries for the "Country" select: ASEAN members first, then other markets.
 *
 * @return string[]
 */
function apba_form_countries() {
	return array(
		'Singapore', 'Brunei Darussalam', 'Cambodia', 'Indonesia', 'Lao PDR', 'Malaysia', 'Myanmar', 'Philippines', 'Thailand', 'Timor-Leste', 'Viet Nam',
		'Australia', 'Canada', 'China', 'France', 'Germany', 'Hong Kong SAR', 'India', 'Japan', 'Netherlands', 'New Zealand', 'Republic of Korea', 'Switzerland', 'Taiwan', 'United Arab Emirates', 'United Kingdom', 'United States',
		'Other',
	);
}

/**
 * Quote options for a CF7 select tag.
 *
 * @param string[] $options Options.
 * @return string
 */
function apba_form_options( $options ) {
	return '"' . implode( '" "', array_map( static function ( $o ) {
		return str_replace( '"', '', $o );
	}, $options ) ) . '"';
}

/**
 * Form definitions keyed by importer key.
 *
 * @return array<string,array>
 */
function apba_import_forms() {
	$privacy = '{{url:privacy-policy}}';
	$consent = '<div class="apba-field apba-field--full">[acceptance privacy-consent] I agree to the collection and use of my personal data in accordance with the <a href="' . $privacy . '" target="_blank" rel="noopener">Privacy Policy</a>. [/acceptance]</div>';

	$register = '<div class="apba-form-grid">
<div class="apba-field"><label for="ri-name">Name <span class="apba-req" aria-hidden="true">*</span></label>[text* your-name id:ri-name autocomplete:name]</div>
<div class="apba-field"><label for="ri-email">Email <span class="apba-req" aria-hidden="true">*</span></label>[email* your-email id:ri-email autocomplete:email]</div>
<div class="apba-field"><label for="ri-phone">Phone Number</label>[tel your-phone id:ri-phone autocomplete:tel]</div>
<div class="apba-field"><label for="ri-company">Company Name</label>[text your-company id:ri-company autocomplete:organization]</div>
<div class="apba-field"><label for="ri-designation">Designation</label>[text your-designation id:ri-designation autocomplete:organization-title]</div>
<div class="apba-field"><label for="ri-type">Type of Membership <span class="apba-req" aria-hidden="true">*</span></label>[select* membership-type id:ri-type first_as_label "Please select" "Corporate Membership" "Individual Membership"]</div>
<div class="apba-field apba-field--full"><label for="ri-country">Country <span class="apba-req" aria-hidden="true">*</span></label>[select* your-country id:ri-country first_as_label "Please select" ' . apba_form_options( apba_form_countries() ) . ']</div>
<div class="apba-field apba-field--full"><label for="ri-details">Details</label>[textarea your-details id:ri-details x5]</div>
' . $consent . '
<div class="apba-field apba-field--full apba-form-actions">[submit "Submit"]</div>
</div>';

	$contact = '<div class="apba-form-grid">
<div class="apba-field"><label for="cu-first">First Name <span class="apba-req" aria-hidden="true">*</span></label>[text* first-name id:cu-first autocomplete:given-name]</div>
<div class="apba-field"><label for="cu-last">Last Name</label>[text last-name id:cu-last autocomplete:family-name]</div>
<div class="apba-field"><label for="cu-email">Email <span class="apba-req" aria-hidden="true">*</span></label>[email* your-email id:cu-email autocomplete:email]</div>
<div class="apba-field"><label for="cu-phone">Phone</label>[tel your-phone id:cu-phone autocomplete:tel]</div>
<div class="apba-field"><label for="cu-company">Company Name</label>[text your-company id:cu-company autocomplete:organization]</div>
<div class="apba-field"><label for="cu-designation">Designation</label>[text your-designation id:cu-designation autocomplete:organization-title]</div>
<div class="apba-field apba-field--full"><label for="cu-subject">Subject</label>[text your-subject id:cu-subject]</div>
<div class="apba-field apba-field--full"><label for="cu-message">Message <span class="apba-req" aria-hidden="true">*</span></label>[textarea* your-message id:cu-message x6]</div>
' . $consent . '
<div class="apba-field apba-field--full apba-form-actions">[submit "Submit"]</div>
</div>';

	$messages = array(
		'mail_sent_ok'     => 'Thank you. Your submission has been received and the APBA Secretariat will be in touch soon.',
		'mail_sent_ng'     => 'Sorry, your submission could not be sent. Please try again later or email us directly.',
		'validation_error' => 'Some fields need your attention. Please check the highlighted fields and try again.',
		'spam'             => 'Your submission could not be sent. Please try again later or email us directly.',
		'accept_terms'     => 'Please confirm that you agree to the Privacy Policy before submitting.',
		'invalid_required' => 'This field is required.',
		'invalid_email'    => 'Please enter a valid email address.',
		'invalid_tel'      => 'Please enter a valid phone number.',
	);

	return array(
		'register' => array(
			'title'    => 'Register Your Interest',
			'form'     => $register,
			'subject'  => '[APBA] Membership interest: [your-name] ([membership-type])',
			'body'     => "Name: [your-name]\nEmail: [your-email]\nPhone: [your-phone]\nCompany: [your-company]\nDesignation: [your-designation]\nType of membership: [membership-type]\nCountry: [your-country]\n\nDetails:\n[your-details]\n\nPrivacy consent: [privacy-consent]\n\n-- \nSent from [_site_title] ([_url])",
			'reply_to' => '[your-email]',
			'messages' => $messages,
		),
		'contact'  => array(
			'title'    => 'Contact Us',
			'form'     => $contact,
			'subject'  => '[APBA] Enquiry: [your-subject]',
			'body'     => "Name: [first-name] [last-name]\nEmail: [your-email]\nPhone: [your-phone]\nCompany: [your-company]\nDesignation: [your-designation]\nSubject: [your-subject]\n\nMessage:\n[your-message]\n\nPrivacy consent: [privacy-consent]\n\n-- \nSent from [_site_title] ([_url])",
			'reply_to' => '[your-email]',
			'messages' => $messages,
		),
	);
}
