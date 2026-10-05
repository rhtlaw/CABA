<?php
/**
 * Posts for the News and Articles categories.
 *
 * Only content whose text is publicly verifiable is published. Older items whose
 * titles/dates are known but whose full text still has to be copied from the
 * live site are created as drafts with a migration note (see docs/CONTENT-SOURCES.md).
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Category definitions.
 *
 * @return array<string,array{0:string,1:string}> slug => [name, description].
 */
function apba_import_categories() {
	return array(
		'news'     => array( 'News', 'News and announcements from the ASEAN Plus Business Alliance (APBA).' ),
		'articles' => array( 'Articles', 'Articles and thought leadership on ASEAN business, trade and economic issues.' ),
	);
}

/**
 * Post definitions.
 *
 * @return array<int,array>
 */
function apba_import_posts() {
	$migrate = static function ( $url ) {
		return '<!-- wp:paragraph --><p><strong>Content migration needed:</strong> copy the full text and images of this item from <a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a>, then publish.</p><!-- /wp:paragraph -->';
	};

	return array(
		array(
			'slug'     => 'onerht-asean-summit-2026-positioning-asean-for-what-comes-next',
			'title'    => 'ONERHT ASEAN Summit 2026: Positioning ASEAN for What Comes Next',
			'category' => 'news',
			'date'     => '2026-08-20 18:00:00',
			'status'   => 'publish',
			'image'    => 'summit',
			'excerpt'  => 'The 10th ONERHT ASEAN Summit brought together policymakers, diplomats, regulators, investors and industry leaders, and marked the official launch of the ASEAN Plus Business Alliance (APBA).',
			'content'  => '<!-- wp:paragraph --><p>On 20 August 2026, the 10th ONERHT ASEAN Summit brought together policymakers, diplomats, regulators, investors and industry leaders at Suntec Singapore Convention &amp; Exhibition Centre.</p><!-- /wp:paragraph -->'
				. '<!-- wp:heading --><h2 class="wp-block-heading">Positioning ASEAN in a US–China Contest Era</h2><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>Centred on the theme “Positioning ASEAN in a US–China Contest Era”, the Summit explored how ASEAN can strengthen its strategic autonomy, resilience and global relevance amid an increasingly complex geopolitical landscape.</p><!-- /wp:paragraph -->'
				. '<!-- wp:paragraph --><p>From ASEAN’s strategic positioning to tokenisation, digital finance, private wealth and succession, the Summit highlighted the opportunities and challenges shaping the region’s next chapter.</p><!-- /wp:paragraph -->'
				. '<!-- wp:heading --><h2 class="wp-block-heading">Launch of the ASEAN Plus Business Alliance</h2><!-- /wp:heading -->'
				. '<!-- wp:paragraph --><p>A defining milestone was the official launch of the ASEAN Plus Business Alliance (APBA), officiated by Mr Heng Swee Keat, Chairman of the National Research Foundation and former Deputy Prime Minister of Singapore.</p><!-- /wp:paragraph -->'
				. '<!-- wp:paragraph --><p>APBA will serve as a regional platform to strengthen international business ties, deepen cross-border cooperation and connect businesses across ASEAN and beyond.</p><!-- /wp:paragraph -->',
		),
		array(
			'slug'     => 'onerht-launches-accelerator-programme-rebel-labs-at-the-8th-rht-caba-asean-summit',
			'title'    => 'ONERHT launches accelerator programme Rebel Labs at the 8th RHT CABA ASEAN Summit',
			'category' => 'news',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/onerht-launches-accelerator-programme-rebel-labs-at-the-8th-rht-caba-asean-summit/' ),
		),
		array(
			'slug'     => 'rht-caba-asean-summit-businesses-urged-to-act-now-for-a-more-sustainable-and-resilient-future',
			'title'    => 'RHT CABA ASEAN Summit: “Businesses urged to act now for a more sustainable and resilient future”',
			'category' => 'news',
			'date'     => '2021-09-17 12:00:00',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/rht-caba-asean-summit-businesses-urged-to-act-now-for-a-more-sustainable-and-resilient-future/' ),
		),
		array(
			'slug'     => 'china-and-us-vaccines-diplomacy-in-south-east-asia',
			'title'    => 'China and US Vaccines Diplomacy in South East Asia',
			'category' => 'articles',
			'date'     => '2021-09-10 12:00:00',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/articles/' ),
		),
		array(
			'slug'     => 'china-asean-business-alliance-and-the-importance-of-the-region',
			'title'    => 'China-ASEAN Business Alliance and the Importance of the Region',
			'category' => 'articles',
			'date'     => '2021-08-24 12:00:00',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/china-asean-business-alliance-and-the-importance-of-the-region/' ),
		),
		array(
			'slug'     => 'rcep-cptpp-and-opportunities-from-aseans-rise',
			'title'    => 'RCEP, CPTPP and opportunities from ASEAN’s rise',
			'category' => 'articles',
			'date'     => '2021-04-20 12:00:00',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/rcep-cptpp-and-opportunities-from-aseans-rise/' ),
		),
		array(
			'slug'     => 'dpm-heng-on-asean-centrality-and-cooperation',
			'title'    => 'DPM Heng on ASEAN Centrality and Cooperation',
			'category' => 'articles',
			'date'     => '2021-01-12 12:00:00',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/articles/' ),
		),
		array(
			'slug'     => 'regulating-new-technologies-singapore-and-china-can-work-together',
			'title'    => 'Regulating new technologies: Singapore and China can work together',
			'category' => 'articles',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/regulating-new-technologies-singapore-and-china-can-work-together/' ),
		),
		array(
			'slug'     => 'finding-the-sweet-spot-between-regulation-fintech-growth',
			'title'    => 'Finding the Sweet Spot Between Regulation & Fintech Growth',
			'category' => 'articles',
			'status'   => 'draft',
			'content'  => $migrate( 'https://www.caba.org.sg/finding-the-sweet-spot-between-regulation-fintech-growth/' ),
		),
	);
}
