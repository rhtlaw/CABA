<?php
/**
 * Page definitions for the importer, built from free Elementor widgets.
 *
 * Copy is taken from the published caba.org.sg website (see docs/CONTENT-SOURCES.md).
 * Tokens resolved by the importer before saving:
 *   {{url:slug}}   → permalink of the page/post with that slug
 *   {{form:key}}   → Contact Form 7 shortcode
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * All pages: slug => [ title, builder callback, meta description ].
 *
 * @return array<string,array>
 */
function apba_import_pages() {
	return array(
		'home'                  => array( 'Home', 'apba_page_home', 'The ASEAN Plus Business Alliance (APBA), formerly China-ASEAN Business Alliance, promotes international business ties and collaboration for the ASEAN region.' ),
		'about-us'              => array( 'About Us', 'apba_page_about', 'About the ASEAN Plus Business Alliance (APBA), established in Singapore in 2013 as the China-ASEAN Business Alliance.' ),
		'vision-and-mission'    => array( 'Vision & Mission', 'apba_page_vision', 'The vision and mission of the ASEAN Plus Business Alliance (APBA).' ),
		'leadership'            => array( 'Leadership', 'apba_page_leadership', 'Leadership of the ASEAN Plus Business Alliance (APBA).' ),
		'secretariat'           => array( 'Secretariat', 'apba_page_secretariat', 'Contact the APBA Secretariat for membership, programme and general enquiries.' ),
		'membership'            => array( 'Membership', 'apba_page_membership', 'APBA membership benefits at a glance: advocacy, trade missions, networking, management and consultancy services.' ),
		'register-your-interest' => array( 'Register Your Interest', 'apba_page_register', 'Register your interest in becoming a member of the ASEAN Plus Business Alliance (APBA).' ),
		'news'                  => array( 'News', 'apba_page_news', 'Latest news from the ASEAN Plus Business Alliance (APBA).' ),
		'articles'              => array( 'Articles', 'apba_page_articles', 'Articles and thought leadership on ASEAN business, trade and economic issues.' ),
		'contact-us'            => array( 'Contact Us', 'apba_page_contact', 'Contact the ASEAN Plus Business Alliance (APBA) Secretariat in Singapore.' ),
		'privacy-policy'        => array( 'Privacy Policy', 'apba_page_privacy', 'How the ASEAN Plus Business Alliance (APBA) collects, uses and protects personal data.' ),
	);
}

/* -------------------------------------------------------------------------
 * Shared copy (published wording)
 * ---------------------------------------------------------------------- */

/**
 * The three pillars with their published descriptions.
 *
 * @return array<int,array{0:string,1:string,2:string}> icon, title, text.
 */
function apba_copy_pillars() {
	return array(
		array( 'fas fa-bullhorn', 'Advocacy', 'APBA engages with government leaders and policymakers of various countries through roundtables and policy briefings, and offers views and recommendations on behalf of its members on trade and economic issues and policies affecting them, especially the ASEAN–China Free Trade Area as well as other schemes under the ASEAN-China dialogue relations and the ASEAN Economic Community.' ),
		array( 'fas fa-chalkboard-teacher', 'Capability Building', 'APBA acts as a capability builder for the business community and its members, especially on current trending business issues, focusing on Digital Transformation, Financing, and Smart and Sustainable Cities.' ),
		array( 'fas fa-lightbulb', 'Thought Leadership', 'APBA has positioned itself as a strategic advocate for the region and as a thought leader, providing insights into important regional issues and promoting business issues such as infrastructure financing, sustainability and digital capabilities.' ),
	);
}

/**
 * Organisation introduction paragraphs.
 *
 * @return string HTML.
 */
function apba_copy_intro() {
	return '<p>The ASEAN Plus Business Alliance (APBA), formerly known as China-ASEAN Business Alliance, was established in Singapore in 2013 and serves as a platform to promote international business ties and collaboration for the ASEAN region.</p>'
		. '<p>Beyond business networking and collaboration opportunities, APBA also provides for business co-operation between ASEAN and other countries within the ASEAN Economic Community (AEC).</p>';
}

/* -------------------------------------------------------------------------
 * Pages
 * ---------------------------------------------------------------------- */

/**
 * Home.
 *
 * @return array
 */
function apba_page_home() {
	$hero = APBA_El::con(
		array(
			'html_tag'                      => 'section',
			'css_classes'                   => 'apba-hero',
			'min_height'                    => APBA_El::size( 640 ),
			'min_height_tablet'             => APBA_El::size( 540 ),
			'min_height_mobile'             => APBA_El::size( 480 ),
			'flex_justify_content'          => 'center',
			'flex_align_items'              => 'flex-start',
			'flex_gap'                      => APBA_El::gap( 16 ),
			'padding'                       => APBA_El::dim( 96, 24 ),
			'padding_mobile'                => APBA_El::dim( 64, 16 ),
			'background_background'         => 'classic',
			'background_image'              => APBA_El::img( 'hero' ),
			'background_position'           => 'center center',
			'background_size'               => 'cover',
			'background_overlay_background' => 'gradient',
			'background_overlay_color'      => '#071D3B',
			'background_overlay_color_stop' => APBA_El::size( 35, '%' ),
			'background_overlay_color_b'    => '#0B2D5B',
			'background_overlay_color_b_stop' => APBA_El::size( 100, '%' ),
			'background_overlay_gradient_angle' => APBA_El::size( 90, 'deg' ),
			'background_overlay_opacity'    => APBA_El::size( 0.78 ),
		),
		array(
			APBA_El::heading(
				'ASEAN Plus Business Alliance (APBA)',
				'p',
				array(
					'_css_classes' => 'apba-eyebrow',
					'title_color'  => '#FFFFFF',
				)
			),
			APBA_El::heading(
				'China-ASEAN Business Alliance',
				'h1',
				array(
					'_css_classes'                => 'apba-hero__title',
					'title_color'                 => '#FFFFFF',
					'typography_typography'       => 'custom',
					'typography_font_size'        => APBA_El::size( 52 ),
					'typography_font_size_tablet' => APBA_El::size( 40 ),
					'typography_font_size_mobile' => APBA_El::size( 30 ),
					'typography_font_weight'      => '800',
					'typography_line_height'      => APBA_El::size( 1.15, 'em' ),
					'_element_width'              => 'initial',
					'_element_custom_width'       => APBA_El::size( 780 ),
					'_element_custom_width_mobile' => APBA_El::size( 100, '%' ),
				)
			),
			APBA_El::heading(
				'Building Partnerships, Opening Markets',
				'p',
				array(
					'title_color'                 => '#FFFFFF',
					'typography_typography'       => 'custom',
					'typography_font_size'        => APBA_El::size( 26 ),
					'typography_font_size_mobile' => APBA_El::size( 20 ),
					'typography_font_weight'      => '500',
				)
			),
			APBA_El::heading(
				'Advocacy. Capability Building. Thought Leadership.',
				'p',
				array(
					'_css_classes'                => 'apba-hero__tagline',
					'title_color'                 => '#FFFFFFD9',
					'typography_typography'       => 'custom',
					'typography_font_size'        => APBA_El::size( 16 ),
					'typography_font_weight'      => '600',
					'typography_letter_spacing'   => APBA_El::size( 1 ),
					'typography_text_transform'   => 'uppercase',
				)
			),
			APBA_El::con(
				array(
					'content_width'  => 'full',
					'flex_direction' => 'row',
					'flex_wrap'      => 'wrap',
					'flex_gap'       => APBA_El::gap( 12, 16 ),
					'padding'        => APBA_El::dim( 16, 0, 0, 0 ),
				),
				array(
					APBA_El::button( 'Register Your Interest', '{{url:register-your-interest}}', 'accent', array( 'size' => 'lg' ) ),
					APBA_El::button( 'About Us', '{{url:about-us}}', 'outline-light', array( 'size' => 'lg' ) ),
				)
			),
		)
	);

	$intro = APBA_El::section(
		array(
			APBA_El::two_col(
				array_merge(
					APBA_El::section_heading( 'About Us', 'Building Partnerships, Opening Markets' ),
					array(
						APBA_El::text( apba_copy_intro() ),
						APBA_El::button( 'Learn More', '{{url:about-us}}', 'primary' ),
					)
				),
				array(
					APBA_El::image(
						'about',
						array(
							'_css_classes' => 'apba-framed',
							'image_size'   => 'large',
						)
					),
				),
				55
			),
		)
	);

	$pillar_cards = array();
	foreach ( apba_copy_pillars() as $p ) {
		$pillar_cards[] = array( APBA_El::icon_box( $p[0], $p[1], $p[2] ) );
	}
	$pillars = APBA_El::section(
		array_merge(
			APBA_El::section_heading( 'What We Stand For', 'Advocacy. Capability Building. Thought Leadership.', '', 'center' ),
			array( APBA_El::card_row( $pillar_cards, 3 ) )
		),
		array( 'flex_gap' => APBA_El::gap( 32 ) ),
		'surface'
	);

	$summit = APBA_El::section(
		array(
			APBA_El::two_col(
				array(
					APBA_El::image(
						'summit',
						array(
							'_css_classes' => 'apba-feature-media',
							'image_size'   => 'large',
							'link_to'      => 'custom',
							'link'         => array( 'url' => '{{url:onerht-asean-summit-2026-positioning-asean-for-what-comes-next}}' ),
						)
					),
				),
				array_merge(
					APBA_El::section_heading( 'Featured Event', 'ONERHT ASEAN Summit 2026' ),
					array(
						APBA_El::text( '<p>On 20 August 2026, the 10th ONERHT ASEAN Summit brought together policymakers, diplomats, regulators, investors and industry leaders to explore how ASEAN can strengthen its strategic autonomy, resilience and global relevance. A defining milestone was the official launch of the ASEAN Plus Business Alliance (APBA).</p>' ),
						APBA_El::checklist(
							array(
								'Theme: Positioning ASEAN in a US–China Contest Era',
								'Date: 20 August 2026',
								'Venue: Suntec Singapore Convention & Exhibition Centre',
							)
						),
						APBA_El::button( 'Read More', '{{url:onerht-asean-summit-2026-positioning-asean-for-what-comes-next}}', 'accent' ),
					)
				),
				50
			),
		)
	);

	$news = APBA_El::section(
		array(
			APBA_El::con(
				array(
					'content_width'         => 'full',
					'flex_direction'        => 'row',
					'flex_direction_mobile' => 'column',
					'flex_justify_content'  => 'space-between',
					'flex_align_items'      => 'flex-end',
					'flex_align_items_mobile' => 'flex-start',
					'flex_gap'              => APBA_El::gap( 16 ),
					'padding'               => APBA_El::dim( 0 ),
				),
				array(
					APBA_El::col( 70, array(), APBA_El::section_heading( 'Latest Updates', 'News' ) ),
					APBA_El::col(
						30,
						array( 'flex_align_items' => 'flex-end', 'flex_align_items_mobile' => 'flex-start', 'padding' => APBA_El::dim( 0, 0, 30, 0 ) ),
						array( APBA_El::button( 'View All News', '{{url:news}}', 'outline' ) )
					),
				)
			),
			APBA_El::shortcode( '[apba_posts category="news" count="3" columns="3"]' ),
		),
		array(),
		'surface'
	);

	return array(
		$hero,
		$intro,
		$pillars,
		$summit,
		$news,
		apba_cta_membership(),
	);
}

/**
 * Membership CTA band reused on several pages.
 *
 * @return array
 */
function apba_cta_membership() {
	return APBA_El::cta_band(
		'Become an APBA Member',
		'Take part in trade missions, conferences, exhibitions and networking events organised by APBA and its partners.',
		'Register Your Interest',
		'{{url:register-your-interest}}'
	);
}

/**
 * About Us.
 *
 * @return array
 */
function apba_page_about() {
	$pillar_cards = array();
	foreach ( apba_copy_pillars() as $p ) {
		$pillar_cards[] = array( APBA_El::icon_box( $p[0], $p[1], $p[2] ) );
	}

	$links = array(
		array( 'fas fa-bullseye', 'Vision & Mission', 'Our vision for ASEAN-centric partnership and our mission to promote networking and co-operation.', 'vision-and-mission' ),
		array( 'fas fa-users', 'Leadership', 'The people who guide the Alliance.', 'leadership' ),
		array( 'fas fa-building', 'Secretariat', 'Your first point of contact for membership and general enquiries.', 'secretariat' ),
	);
	$link_cards = array();
	foreach ( $links as $l ) {
		$link_cards[] = array(
			APBA_El::icon_box(
				$l[0],
				$l[1],
				$l[2],
				array( 'link' => array( 'url' => '{{url:' . $l[3] . '}}' ) )
			),
		);
	}

	return array(
		APBA_El::page_banner( 'About Us', 'Building Partnerships, Opening Markets' ),
		APBA_El::section(
			array(
				APBA_El::two_col(
					array_merge(
						APBA_El::section_heading( 'Who We Are', 'ASEAN Plus Business Alliance (APBA)' ),
						array( APBA_El::text( apba_copy_intro() ) )
					),
					array( APBA_El::image( 'about', array( '_css_classes' => 'apba-framed' ) ) ),
					55
				),
			)
		),
		APBA_El::section(
			array_merge(
				APBA_El::section_heading( 'What We Stand For', 'Advocacy. Capability Building. Thought Leadership.', '', 'center' ),
				array( APBA_El::card_row( $pillar_cards, 3 ) )
			),
			array( 'flex_gap' => APBA_El::gap( 32 ) ),
			'surface'
		),
		APBA_El::section(
			array_merge(
				APBA_El::section_heading( 'More About APBA', 'Our Organisation', '', 'center' ),
				array( APBA_El::card_row( $link_cards, 3 ) )
			),
			array( 'flex_gap' => APBA_El::gap( 32 ) )
		),
		apba_cta_membership(),
	);
}

/**
 * Vision & Mission.
 *
 * @return array
 */
function apba_page_vision() {
	$cards = array(
		array(
			APBA_El::icon_box( 'fas fa-eye', 'Our Vision', 'To be one of the key business platforms for ASEAN centric partnership and co-operation.', array( 'title_size' => 'h2' ) ),
		),
		array(
			APBA_El::icon_box( 'fas fa-flag', 'Our Mission', 'To promote greater networking and co-operation amongst businesses in ASEAN and globally to facilitate trade, investment, business and people-to-people connectivity by leveraging on the ASEAN Free Trade Area, and ASEAN-China Maritime Silk Road.', array( 'title_size' => 'h2' ) ),
		),
	);

	return array(
		APBA_El::page_banner( 'Vision & Mission' ),
		APBA_El::section(
			array(
				APBA_El::card_row( $cards, 2 ),
			)
		),
		apba_cta_membership(),
	);
}

/**
 * Leadership. Entries are editable Elementor widgets; verify names and titles
 * against the live site before launch (see docs/CONTENT-SOURCES.md).
 *
 * @return array
 */
function apba_page_leadership() {
	$people = array(
		array( 'Tan Chong Huat', 'Chairman' ),
		array( 'Victor Tay', 'Chief Executive Officer' ),
	);
	$cards  = array();
	foreach ( $people as $p ) {
		$cards[] = array(
			APBA_El::image( 'portrait', array( '_css_classes' => 'apba-portrait', 'image_size' => 'medium_large' ) ),
			APBA_El::heading( $p[0], 'h3', array( '__globals__' => array( 'title_color' => APBA_El::color( 'apbahead' ) ) ) ),
			APBA_El::heading( $p[1], 'p', array( '_css_classes' => 'apba-eyebrow' ) ),
		);
	}

	return array(
		APBA_El::page_banner( 'Leadership' ),
		APBA_El::section(
			array_merge(
				APBA_El::section_heading( 'Our Leadership', 'Leadership of the Alliance', '', 'center' ),
				array( APBA_El::card_row( $cards, 3 ) )
			),
			array( 'flex_gap' => APBA_El::gap( 32 ) )
		),
		apba_cta_membership(),
	);
}

/**
 * Secretariat.
 *
 * @return array
 */
function apba_page_secretariat() {
	return array(
		APBA_El::page_banner( 'Secretariat' ),
		APBA_El::section(
			array(
				APBA_El::two_col(
					array_merge(
						APBA_El::section_heading( 'APBA Secretariat', 'How We Can Help' ),
						array(
							APBA_El::text( '<p>The APBA Secretariat, based in Singapore, is the first point of contact for members, partners and the business community.</p><p>Please contact the Secretariat for:</p>' ),
							APBA_El::checklist(
								array(
									'Membership enquiries and registration',
									'Trade missions, conferences, exhibitions and networking events',
									'Certification courses and management/consultancy services',
									'Partnership and general enquiries',
								)
							),
						)
					),
					array(
						APBA_El::con(
							array(
								'content_width' => 'full',
								'css_classes'   => 'apba-box',
								'padding'       => APBA_El::dim( 36, 32 ),
								'flex_gap'      => APBA_El::gap( 16 ),
							),
							array(
								APBA_El::heading( 'Contact the Secretariat', 'h3' ),
								APBA_El::shortcode( '[apba_contact_info]' ),
								APBA_El::button( 'Contact Us', '{{url:contact-us}}', 'primary' ),
							)
						),
					),
					55,
					array( 'flex_align_items' => 'flex-start' )
				),
			)
		),
		apba_cta_membership(),
	);
}

/**
 * Membership.
 *
 * @return array
 */
function apba_page_membership() {
	$advocacy = array(
		APBA_El::icon_box( 'fas fa-globe-asia', 'Advocacy', '' ),
		APBA_El::checklist(
			array(
				'Participate in overseas trade missions and conferences, trade exhibitions and fairs organised by APBA and/or its collaboration partners.',
				'Enjoy subsidised rates for mission trips that have received approval for government subsidies.',
				'Attend meetings with visiting overseas trade representatives, delegates and diplomats to identify and seek business and investment opportunities.',
				'Participate in roundtable sessions, networking events and industry outreach sessions to identify new business opportunities.',
			)
		),
	);
	$consultancy = array(
		APBA_El::icon_box( 'fas fa-briefcase', 'Management and Consultancy Services', '' ),
		APBA_El::checklist(
			array(
				'APBA’s core team are well-connected ASEAN experts who are able to provide customised management/consultancy services for members who require more than introduction and networking opportunities with other businesses and trade promotion agencies.',
				'Certification courses on ASEAN free trade agreements, the ASEAN-China FTA and other trade and economic programmes, so members are fully equipped to leverage these agreements.',
				'Preferential rates for these bespoke management/consultancy services.',
			)
		),
	);

	return array(
		APBA_El::page_banner( 'Membership' ),
		APBA_El::section(
			array_merge(
				APBA_El::section_heading( 'Membership', 'APBA membership benefits at glance', '', 'center' ),
				array( APBA_El::card_row( array( $advocacy, $consultancy ), 2 ) )
			),
			array( 'flex_gap' => APBA_El::gap( 32 ) )
		),
		APBA_El::cta_band(
			'Register Your Interest',
			'Interested in becoming a member? Tell us about your organisation and the Secretariat will be in touch.',
			'Register Your Interest',
			'{{url:register-your-interest}}'
		),
	);
}

/**
 * Register Your Interest.
 *
 * @return array
 */
function apba_page_register() {
	return array(
		APBA_El::page_banner( 'Register Your Interest' ),
		APBA_El::section(
			array(
				APBA_El::two_col(
					array_merge(
						APBA_El::section_heading( 'Membership', 'Join the Alliance', '<p>Complete the form to register your interest in APBA membership. Fields marked with an asterisk (*) are required.</p>' ),
						array(
							APBA_El::checklist(
								array(
									'Overseas trade missions, conferences, exhibitions and fairs',
									'Meetings with visiting trade representatives, delegates and diplomats',
									'Roundtables, networking events and industry outreach sessions',
									'Customised management/consultancy services and certification courses',
								)
							),
							APBA_El::button( 'View Membership Benefits', '{{url:membership}}', 'outline' ),
						)
					),
					array(
						APBA_El::con(
							array(
								'content_width' => 'full',
								'css_classes'   => 'apba-form-card',
								'padding'       => APBA_El::dim( 40, 36 ),
								'padding_mobile' => APBA_El::dim( 28, 20 ),
							),
							array( APBA_El::shortcode( '{{form:register}}' ) )
						),
					),
					40,
					array( 'flex_align_items' => 'flex-start' )
				),
			)
		),
	);
}

/**
 * News listing.
 *
 * @return array
 */
function apba_page_news() {
	return array(
		APBA_El::page_banner( 'News' ),
		APBA_El::section(
			array( APBA_El::shortcode( '[apba_posts category="news" count="9" columns="3" pagination="yes" heading="h2"]' ) )
		),
	);
}

/**
 * Articles listing.
 *
 * @return array
 */
function apba_page_articles() {
	return array(
		APBA_El::page_banner( 'Articles' ),
		APBA_El::section(
			array( APBA_El::shortcode( '[apba_posts category="articles" count="9" columns="3" pagination="yes" heading="h2"]' ) )
		),
	);
}

/**
 * Contact Us.
 *
 * @return array
 */
function apba_page_contact() {
	return array(
		APBA_El::page_banner( 'Contact Us' ),
		APBA_El::section(
			array(
				APBA_El::two_col(
					array_merge(
						APBA_El::section_heading( 'Get in Touch', 'We’d love to hear from you', '<p>For enquiries about APBA, our events or membership, please send us a message and the Secretariat will get back to you.</p>' ),
						array( APBA_El::shortcode( '[apba_contact_info]' ) )
					),
					array(
						APBA_El::con(
							array(
								'content_width'  => 'full',
								'css_classes'    => 'apba-form-card',
								'padding'        => APBA_El::dim( 40, 36 ),
								'padding_mobile' => APBA_El::dim( 28, 20 ),
							),
							array( APBA_El::shortcode( '{{form:contact}}' ) )
						),
					),
					40,
					array( 'flex_align_items' => 'flex-start' )
				),
			)
		),
		APBA_El::con(
			array(
				'html_tag'      => 'section',
				'content_width' => 'full',
				'padding'       => APBA_El::dim( 0 ),
			),
			array( APBA_El::map( '1 Paya Lebar Link, PLQ 2 Paya Lebar Quarter, Singapore 408533' ) )
		),
	);
}

/**
 * Privacy Policy (template — review before launch).
 *
 * @return array
 */
function apba_page_privacy() {
	$html = '<p><em>Last updated: {{date}}</em></p>'
		. '<p>The ASEAN Plus Business Alliance (“APBA”, “we”, “us”) respects your privacy and handles personal data in accordance with Singapore’s Personal Data Protection Act 2012 (PDPA). This policy explains how we collect, use and protect personal data submitted through this website.</p>'
		. '<h2>Personal data we collect</h2>'
		. '<p>When you submit our contact or Register Your Interest forms we collect the details you provide, such as your name, email address, phone number, company name, designation, country, membership interest and message.</p>'
		. '<h2>How we use your personal data</h2>'
		. '<ul><li>To respond to your enquiries;</li><li>To process membership interest and administer membership;</li><li>To inform you about APBA events, programmes and activities where you have agreed to this.</li></ul>'
		. '<h2>Disclosure</h2>'
		. '<p>We do not sell personal data. We may share it with service providers and event partners who help us deliver our services, subject to appropriate confidentiality obligations, or where required by law.</p>'
		. '<h2>Cookies</h2>'
		. '<p>This website uses cookies that are necessary for it to function and, where enabled, cookies that help us understand how the site is used.</p>'
		. '<h2>Retention and protection</h2>'
		. '<p>We keep personal data only for as long as it is needed for the purposes above or as required by law, and take reasonable security measures to protect it.</p>'
		. '<h2>Access, correction and withdrawal of consent</h2>'
		. '<p>You may request access to or correction of your personal data, or withdraw your consent, by contacting our Secretariat at <a href="mailto:secretariat@caba.org.sg">secretariat@caba.org.sg</a>.</p>';

	return array(
		APBA_El::page_banner( 'Privacy Policy' ),
		APBA_El::section(
			array(
				APBA_El::text(
					$html,
					array(
						'_css_classes'          => 'apba-prose',
						'_element_width'        => 'initial',
						'_element_custom_width' => APBA_El::size( 860 ),
						'_element_custom_width_mobile' => APBA_El::size( 100, '%' ),
					)
				),
			)
		),
	);
}
