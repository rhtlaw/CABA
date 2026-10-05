<?php
/**
 * One-click site setup: media, categories, forms, Elementor pages, posts,
 * menus, reading settings, Elementor Site Settings (global colours, fonts,
 * breakpoints) and Customizer defaults.
 *
 * Safe to run more than once: existing published content is left untouched
 * unless "overwrite" is chosen, and then only items the importer created are
 * rebuilt.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/content/pages.php';
require_once __DIR__ . '/content/posts.php';
require_once __DIR__ . '/content/forms.php';

/**
 * Importer.
 */
class APBA_Importer {

	const MAP_OPTION = 'apba_import_map';

	/**
	 * Created object IDs.
	 *
	 * @var array
	 */
	private $map = array();

	/**
	 * Rebuild importer-created content.
	 *
	 * @var bool
	 */
	private $force = false;

	/**
	 * Log lines.
	 *
	 * @var string[]
	 */
	private $log = array();

	/**
	 * Placeholder images shipped with the theme: key => [file, alt].
	 *
	 * @var array<string,array{0:string,1:string}>
	 */
	private $media = array(
		'hero'     => array( 'hero-placeholder.jpg', 'Placeholder hero image – replace with APBA photography' ),
		'banner'   => array( 'banner-placeholder.jpg', '' ),
		'about'    => array( 'about-placeholder.jpg', 'Placeholder image – replace with APBA photography' ),
		'summit'   => array( 'summit-placeholder.jpg', 'ONERHT ASEAN Summit 2026 (placeholder image)' ),
		'post'     => array( 'post-placeholder.jpg', '' ),
		'portrait' => array( 'portrait-placeholder.jpg', 'Portrait placeholder' ),
	);

	/**
	 * Constructor.
	 *
	 * @param bool $force Rebuild importer-created content.
	 */
	public function __construct( $force = false ) {
		$this->force = (bool) $force;
		$saved       = get_option( self::MAP_OPTION, array() );
		$this->map   = wp_parse_args(
			is_array( $saved ) ? $saved : array(),
			array(
				'media' => array(),
				'pages' => array(),
				'posts' => array(),
				'forms' => array(),
				'cats'  => array(),
				'menus' => array(),
			)
		);
	}

	/**
	 * Missing requirements (empty when ready).
	 *
	 * @return string[]
	 */
	public static function missing_requirements() {
		$missing = array();
		if ( ! did_action( 'elementor/loaded' ) ) {
			$missing[] = 'Elementor (free) plugin must be installed and active.';
		}
		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			$missing[] = 'Contact Form 7 plugin must be installed and active (forms are skipped otherwise).';
		}
		if ( 'hello-elementor' !== get_template() ) {
			$missing[] = 'Parent theme "Hello Elementor" must be installed.';
		}
		return $missing;
	}

	/**
	 * Run everything.
	 *
	 * @return string[] Log.
	 */
	public function run() {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}

		// Pretty permalinks must exist before links are written into pages and menus.
		if ( ! get_option( 'permalink_structure' ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
			$GLOBALS['wp_rewrite']->init();
		}

		$this->import_media();
		APBA_El::$media = $this->map['media'];

		$this->import_categories();
		$this->create_pages();
		$this->create_posts();
		$this->import_forms();
		$this->build_pages();
		$this->import_menus();
		$this->configure_site();
		// Site-wide design settings are applied once so later edits in Site Settings survive re-runs.
		if ( empty( $this->map['configured'] ) || $this->force ) {
			$this->configure_elementor();
			$this->map['configured'] = time();
		} else {
			$this->log( 'Kept existing Elementor Site Settings (use rebuild to reapply).' );
		}
		$this->configure_customizer();

		update_option( self::MAP_OPTION, $this->map, false );
		$this->log( 'Done.' );
		return $this->log;
	}

	/**
	 * Add a log line.
	 *
	 * @param string $line Message.
	 */
	private function log( $line ) {
		$this->log[] = $line;
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::log( $line );
		}
	}

	/* ------------------------------------------------------------------ */

	/**
	 * Copy placeholder images into the media library.
	 */
	private function import_media() {
		require_once ABSPATH . 'wp-admin/includes/image.php';

		foreach ( $this->media as $key => $info ) {
			if ( ! empty( $this->map['media'][ $key ] ) && get_post( $this->map['media'][ $key ] ) ) {
				continue;
			}
			$path = APBA_DIR . '/assets/images/' . $info[0];
			if ( ! file_exists( $path ) ) {
				continue;
			}
			$upload = wp_upload_bits( 'apba-' . $info[0], null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( ! empty( $upload['error'] ) ) {
				$this->log( 'Media error (' . $key . '): ' . $upload['error'] );
				continue;
			}
			$id = wp_insert_attachment(
				array(
					'post_mime_type' => 'image/jpeg',
					'post_title'     => 'APBA placeholder – ' . $key,
					'post_status'    => 'inherit',
				),
				$upload['file']
			);
			if ( is_wp_error( $id ) || ! $id ) {
				continue;
			}
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
			update_post_meta( $id, '_wp_attachment_image_alt', $info[1] );
			$this->map['media'][ $key ] = $id;
			$this->log( 'Imported image: ' . $key );
		}
	}

	/**
	 * News / Articles categories.
	 */
	private function import_categories() {
		foreach ( apba_import_categories() as $slug => $cat ) {
			$term = get_term_by( 'slug', $slug, 'category' );
			if ( ! $term ) {
				$created = wp_insert_term( $cat[0], 'category', array( 'slug' => $slug, 'description' => $cat[1] ) );
				if ( is_wp_error( $created ) ) {
					$this->log( 'Category error: ' . $created->get_error_message() );
					continue;
				}
				$term_id = $created['term_id'];
				$this->log( 'Created category: ' . $cat[0] );
			} else {
				$term_id = $term->term_id;
			}
			$this->map['cats'][ $slug ] = (int) $term_id;
		}
	}

	/**
	 * Should an existing page/post be (re)written by the importer?
	 *
	 * @param WP_Post $post   Existing post.
	 * @param string  $group  Map group.
	 * @param string  $slug   Slug.
	 * @return bool
	 */
	private function may_overwrite( $post, $group, $slug ) {
		$ours = isset( $this->map[ $group ][ $slug ] ) && (int) $this->map[ $group ][ $slug ] === (int) $post->ID;
		if ( $ours ) {
			return $this->force;
		}
		// WordPress's auto-created draft (e.g. Privacy Policy) can be taken over.
		return 'publish' !== $post->post_status && 'builder' !== get_post_meta( $post->ID, '_elementor_edit_mode', true );
	}

	/**
	 * Pass 1: make sure every page exists so links can be resolved.
	 */
	private function create_pages() {
		$this->pending_pages = array();
		foreach ( apba_import_pages() as $slug => $def ) {
			$existing = get_page_by_path( $slug, OBJECT, 'page' );
			if ( $existing ) {
				$this->map['pages'][ $slug ] = isset( $this->map['pages'][ $slug ] ) ? $this->map['pages'][ $slug ] : null;
				if ( $this->may_overwrite( $existing, 'pages', $slug ) ) {
					wp_update_post(
						array(
							'ID'           => $existing->ID,
							'post_title'   => $def[0],
							'post_status'  => 'publish',
							'post_excerpt' => $def[2],
						)
					);
					$this->map['pages'][ $slug ]  = $existing->ID;
					$this->pending_pages[ $slug ] = $existing->ID;
					$this->log( 'Updated page: ' . $def[0] );
				} else {
					$this->log( 'Kept existing page: ' . $def[0] );
				}
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $def[0],
					'post_name'    => $slug,
					'post_excerpt' => $def[2],
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				$this->log( 'Page error (' . $slug . '): ' . $id->get_error_message() );
				continue;
			}
			$this->map['pages'][ $slug ]  = $id;
			$this->pending_pages[ $slug ] = $id;
			$this->log( 'Created page: ' . $def[0] );
		}
	}

	/**
	 * Pages awaiting Elementor content.
	 *
	 * @var array<string,int>
	 */
	private $pending_pages = array();

	/**
	 * Posts.
	 */
	private function create_posts() {
		foreach ( apba_import_posts() as $p ) {
			$existing = get_page_by_path( $p['slug'], OBJECT, 'post' );
			if ( $existing && ! $this->may_overwrite( $existing, 'posts', $p['slug'] ) ) {
				continue;
			}
			$data = array(
				'post_type'     => 'post',
				'post_status'   => $p['status'],
				'post_title'    => $p['title'],
				'post_name'     => $p['slug'],
				'post_content'  => $p['content'],
				'post_excerpt'  => isset( $p['excerpt'] ) ? $p['excerpt'] : '',
				'post_category' => isset( $this->map['cats'][ $p['category'] ] ) ? array( $this->map['cats'][ $p['category'] ] ) : array(),
			);
			if ( ! empty( $p['date'] ) ) {
				$data['post_date']     = $p['date'];
				$data['post_date_gmt'] = get_gmt_from_date( $p['date'] );
			}
			if ( $existing ) {
				$data['ID'] = $existing->ID;
			}
			$id = wp_insert_post( wp_slash( $data ), true );
			if ( is_wp_error( $id ) ) {
				$this->log( 'Post error (' . $p['slug'] . '): ' . $id->get_error_message() );
				continue;
			}
			if ( ! empty( $p['image'] ) && ! empty( $this->map['media'][ $p['image'] ] ) ) {
				set_post_thumbnail( $id, $this->map['media'][ $p['image'] ] );
			}
			$this->map['posts'][ $p['slug'] ] = $id;
			$this->log( ( $existing ? 'Updated' : 'Created' ) . ' post (' . $p['status'] . '): ' . $p['title'] );
		}
	}

	/**
	 * Contact Form 7 forms.
	 */
	private function import_forms() {
		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			$this->log( 'Contact Form 7 not active – forms skipped.' );
			return;
		}
		$recipient = apba_opt( 'apba_email' ) ? apba_opt( 'apba_email' ) : get_option( 'admin_email' );

		foreach ( apba_import_forms() as $key => $def ) {
			$existing_id = isset( $this->map['forms'][ $key ] ) ? (int) $this->map['forms'][ $key ] : 0;
			$form        = $existing_id ? wpcf7_contact_form( $existing_id ) : null;
			if ( $form && ! $this->force ) {
				continue;
			}
			if ( ! $form ) {
				$form = WPCF7_ContactForm::get_template( array( 'title' => $def['title'] ) );
			}
			$props = $form->get_properties();

			$mail                       = $props['mail'];
			$mail['subject']            = $def['subject'];
			$mail['body']               = $def['body'];
			$mail['recipient']          = $recipient;
			$mail['additional_headers'] = 'Reply-To: ' . $def['reply_to'];
			$mail['use_html']           = false;

			$props['form']     = $this->resolve_string( $def['form'] );
			$props['mail']     = $mail;
			$props['mail_2']   = array_merge( $props['mail_2'], array( 'active' => false ) );
			$props['messages'] = array_merge( $props['messages'], $def['messages'] );

			$form->set_title( $def['title'] );
			$form->set_properties( $props );
			$form_id = $form->save();

			$this->map['forms'][ $key ] = (int) ( $form_id ? $form_id : $form->id() );
			$this->log( 'Saved form: ' . $def['title'] );
		}
	}

	/**
	 * CF7 shortcode for a form key.
	 *
	 * @param string $key Form key.
	 * @return string
	 */
	private function form_shortcode( $key ) {
		$id = isset( $this->map['forms'][ $key ] ) ? (int) $this->map['forms'][ $key ] : 0;
		if ( ! $id || ! function_exists( 'wpcf7_contact_form' ) ) {
			return '';
		}
		$form = wpcf7_contact_form( $id );
		if ( ! $form ) {
			return '';
		}
		$ref = method_exists( $form, 'hash' ) ? $form->hash() : $id;
		return sprintf( '[contact-form-7 id="%s" title="%s"]', esc_attr( $ref ), esc_attr( $form->title() ) );
	}

	/**
	 * Replace {{url:slug}}, {{form:key}} and {{date}} tokens.
	 *
	 * @param string $value Value.
	 * @return string
	 */
	private function resolve_string( $value ) {
		if ( false === strpos( $value, '{{' ) ) {
			return $value;
		}
		return preg_replace_callback(
			'/\{\{(url|form|date)(?::([a-z0-9\-]+))?\}\}/',
			function ( $m ) {
				if ( 'date' === $m[1] ) {
					return wp_date( get_option( 'date_format' ) );
				}
				if ( 'form' === $m[1] ) {
					return $this->form_shortcode( $m[2] );
				}
				$id = 0;
				if ( isset( $this->map['pages'][ $m[2] ] ) ) {
					$id = (int) $this->map['pages'][ $m[2] ];
				} elseif ( isset( $this->map['posts'][ $m[2] ] ) ) {
					$id = (int) $this->map['posts'][ $m[2] ];
				} else {
					$found = get_page_by_path( $m[2], OBJECT, array( 'page', 'post' ) );
					$id    = $found ? $found->ID : 0;
				}
				if ( 'home' === $m[2] ) {
					return home_url( '/' );
				}
				return $id ? get_permalink( $id ) : home_url( '/' . $m[2] . '/' );
			},
			$value
		);
	}

	/**
	 * Resolve tokens in nested Elementor data.
	 *
	 * @param array $data Data.
	 * @return array
	 */
	private function resolve_tokens( $data ) {
		array_walk_recursive(
			$data,
			function ( &$value ) {
				if ( is_string( $value ) ) {
					$value = $this->resolve_string( $value );
				}
			}
		);
		return $data;
	}

	/**
	 * Pass 2: write Elementor data to the pages created/claimed in pass 1.
	 */
	private function build_pages() {
		$defs = apba_import_pages();
		foreach ( $this->pending_pages as $slug => $page_id ) {
			APBA_El::reset( $slug );
			$data = $this->resolve_tokens( call_user_func( $defs[ $slug ][1] ) );
			$this->save_elementor( $page_id, $data );
			$this->log( 'Built Elementor layout: ' . $defs[ $slug ][0] );
		}
	}

	/**
	 * Save Elementor data through Elementor's document API when available.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $data    Elements.
	 */
	private function save_elementor( $post_id, $data ) {
		update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );

		if ( class_exists( '\Elementor\Plugin' ) && current_user_can( 'edit_post', $post_id ) ) {
			$document = \Elementor\Plugin::$instance->documents->get( $post_id, false );
			if ( $document ) {
				$document->set_is_built_with_elementor( true );
				$document->save(
					array(
						'elements' => $data,
						'settings' => array(
							'hide_title' => 'yes',
							'template'   => 'elementor_header_footer',
						),
					)
				);
				return;
			}
		}

		// Fallback: raw meta (Elementor regenerates CSS on first view).
		update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
		update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
		delete_post_meta( $post_id, '_elementor_css' );
	}

	/* ------------------------------------------------------------------ */

	/**
	 * Header, footer and legal menus.
	 */
	private function import_menus() {
		$p = $this->map['pages'];

		$menus = array(
			'apba-primary' => array(
				'name'  => 'Primary Menu',
				'items' => array(
					array( 'home', 'Home' ),
					array(
						'about-us',
						'About Us',
						array(
							array( 'about-us', 'About Us' ),
							array( 'vision-and-mission', 'Vision & Mission' ),
							array( 'leadership', 'Leadership' ),
							array( 'secretariat', 'Secretariat' ),
						),
					),
					array(
						'membership',
						'Membership',
						array(
							array( 'membership', 'Membership Benefits' ),
							array( 'register-your-interest', 'Register Your Interest' ),
						),
					),
					array(
						'news',
						'News & Articles',
						array(
							array( 'news', 'News' ),
							array( 'articles', 'Articles' ),
						),
					),
					array( 'contact-us', 'Contact Us' ),
				),
			),
			'apba-footer'  => array(
				'name'  => 'Footer Menu',
				'items' => array(
					array( 'about-us', 'About Us' ),
					array( 'leadership', 'Leadership' ),
					array( 'membership', 'Membership' ),
					array( 'register-your-interest', 'Register Your Interest' ),
					array( 'news', 'News & Articles' ),
					array( 'contact-us', 'Contact Us' ),
				),
			),
			'apba-legal'   => array(
				'name'  => 'Legal Menu',
				'items' => array(
					array( 'privacy-policy', 'Privacy Policy' ),
				),
			),
		);

		$locations = get_theme_mod( 'nav_menu_locations', array() );

		foreach ( $menus as $location => $menu ) {
			$existing = wp_get_nav_menu_object( $menu['name'] );
			if ( $existing && ! $this->force ) {
				$locations[ $location ] = $existing->term_id;
				continue;
			}
			if ( $existing ) {
				foreach ( (array) wp_get_nav_menu_items( $existing->term_id ) as $item ) {
					wp_delete_post( $item->ID, true );
				}
				$menu_id = $existing->term_id;
			} else {
				$menu_id = wp_create_nav_menu( $menu['name'] );
				if ( is_wp_error( $menu_id ) ) {
					continue;
				}
			}
			$position = 0;
			foreach ( $menu['items'] as $item ) {
				$parent = $this->add_menu_item( $menu_id, $item[0], $item[1], 0, ++$position );
				if ( ! empty( $item[2] ) && $parent ) {
					foreach ( $item[2] as $child ) {
						$this->add_menu_item( $menu_id, $child[0], $child[1], $parent, ++$position );
					}
				}
			}
			$locations[ $location ]           = $menu_id;
			$this->map['menus'][ $location ] = $menu_id;
			$this->log( 'Built menu: ' . $menu['name'] );
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Add one page link to a menu.
	 *
	 * @param int    $menu_id  Menu.
	 * @param string $slug     Page slug.
	 * @param string $title    Label.
	 * @param int    $parent   Parent item ID.
	 * @param int    $position Order.
	 * @return int Item ID.
	 */
	private function add_menu_item( $menu_id, $slug, $title, $parent, $position ) {
		$page_id = isset( $this->map['pages'][ $slug ] ) ? (int) $this->map['pages'][ $slug ] : 0;
		if ( ! $page_id ) {
			$page    = get_page_by_path( $slug );
			$page_id = $page ? $page->ID : 0;
		}
		if ( ! $page_id ) {
			return 0;
		}
		$id = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $title,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent,
				'menu-item-position'  => $position,
			)
		);
		return is_wp_error( $id ) ? 0 : (int) $id;
	}

	/**
	 * Reading, permalinks, privacy page, site title, dates.
	 */
	private function configure_site() {
		if ( ! empty( $this->map['pages']['home'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', (int) $this->map['pages']['home'] );
		}
		if ( ! empty( $this->map['pages']['privacy-policy'] ) ) {
			update_option( 'wp_page_for_privacy_policy', (int) $this->map['pages']['privacy-policy'] );
		}

		$defaults_names = array( '', 'WordPress', 'My WordPress Website', 'My Blog' );
		if ( in_array( get_option( 'blogname' ), $defaults_names, true ) ) {
			update_option( 'blogname', 'ASEAN Plus Business Alliance (APBA)' );
		}
		if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site' ), true ) ) {
			update_option( 'blogdescription', 'Building Partnerships, Opening Markets' );
		}
		if ( empty( $this->map['configured'] ) ) {
			update_option( 'date_format', 'j F Y' );
		}
		if ( ! get_option( 'timezone_string' ) && ! (float) get_option( 'gmt_offset' ) ) {
			update_option( 'timezone_string', 'Asia/Singapore' );
		}
		// Comments are not part of the site.
		update_option( 'default_comment_status', 'closed' );
		update_option( 'default_ping_status', 'closed' );

		flush_rewrite_rules( false );

		// Remove WordPress's untouched sample content.
		$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
		if ( $hello && 'Hello world!' === $hello->post_title ) {
			wp_trash_post( $hello->ID );
		}
		$sample = get_page_by_path( 'sample-page' );
		if ( $sample && 'Sample Page' === $sample->post_title ) {
			wp_trash_post( $sample->ID );
		}
		$this->log( 'Configured reading settings, permalinks and privacy page.' );
	}

	/**
	 * Elementor options and Site Settings (global colours, fonts, layout, breakpoints).
	 */
	private function configure_elementor() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		update_option( 'elementor_cpt_support', array( 'page', 'post' ) );
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
		update_option( 'elementor_experiment-container', 'active' );
		update_option( 'elementor_experiment-e_font_icon_svg', 'active' );
		update_option( 'elementor_font_display', 'swap' );
		update_option( 'elementor_load_fa4_shim', '' );

		$kits = \Elementor\Plugin::$instance->kits_manager;
		$kit  = $kits->get_active_kit();
		if ( ! $kit || ! $kit->get_id() ) {
			$this->log( 'Elementor kit not found – Site Settings skipped.' );
			return;
		}

		$c = static function ( $id ) {
			return 'globals/colors?id=' . $id;
		};
		$t = static function ( $id ) {
			return 'globals/typography?id=' . $id;
		};

		$settings = array(
			'system_colors'          => array(
				array( '_id' => 'primary', 'title' => 'Primary (Navy)', 'color' => '#0B2D5B' ),
				array( '_id' => 'secondary', 'title' => 'Secondary (Blue)', 'color' => '#1D5FA8' ),
				array( '_id' => 'text', 'title' => 'Body Text', 'color' => '#3A4453' ),
				array( '_id' => 'accent', 'title' => 'Accent (Red)', 'color' => '#C8102E' ),
			),
			'custom_colors'          => array(
				array( '_id' => 'apbahead', 'title' => 'Heading', 'color' => '#0B2D5B' ),
				array( '_id' => 'apbabg', 'title' => 'Background', 'color' => '#FFFFFF' ),
				array( '_id' => 'apbasurf', 'title' => 'Surface', 'color' => '#F3F5F9' ),
				array( '_id' => 'apbabord', 'title' => 'Border', 'color' => '#DCE2EB' ),
				array( '_id' => 'apbamute', 'title' => 'Muted Text', 'color' => '#5F6B7A' ),
				array( '_id' => 'apbadark', 'title' => 'Dark (Footer)', 'color' => '#071D3B' ),
			),
			'system_typography'      => array(
				array(
					'_id'                    => 'primary',
					'title'                  => 'Headings',
					'typography_typography'  => 'custom',
					'typography_font_family' => 'Montserrat',
					'typography_font_weight' => '700',
				),
				array(
					'_id'                    => 'secondary',
					'title'                  => 'Sub-headings & Card Titles',
					'typography_typography'  => 'custom',
					'typography_font_family' => 'Montserrat',
					'typography_font_weight' => '600',
				),
				array(
					'_id'                    => 'text',
					'title'                  => 'Body Text',
					'typography_typography'  => 'custom',
					'typography_font_family' => 'Open Sans',
					'typography_font_weight' => '400',
				),
				array(
					'_id'                       => 'accent',
					'title'                     => 'Buttons & Labels',
					'typography_typography'     => 'custom',
					'typography_font_family'    => 'Montserrat',
					'typography_font_weight'    => '700',
					'typography_text_transform' => 'uppercase',
					'typography_letter_spacing' => array( 'unit' => 'px', 'size' => 0.5 ),
				),
			),

			// Theme Style → Typography.
			'body_typography_typography'     => 'custom',
			'body_typography_font_family'    => 'Open Sans',
			'body_typography_font_size'      => array( 'unit' => 'px', 'size' => 16 ),
			'body_typography_line_height'    => array( 'unit' => 'em', 'size' => 1.7 ),
			'h1_typography_typography'       => 'custom',
			'h1_typography_font_family'      => 'Montserrat',
			'h1_typography_font_weight'      => '700',
			'h1_typography_font_size'        => array( 'unit' => 'px', 'size' => 44 ),
			'h1_typography_font_size_tablet' => array( 'unit' => 'px', 'size' => 36 ),
			'h1_typography_font_size_mobile' => array( 'unit' => 'px', 'size' => 30 ),
			'h1_typography_line_height'      => array( 'unit' => 'em', 'size' => 1.2 ),
			'h2_typography_typography'       => 'custom',
			'h2_typography_font_family'      => 'Montserrat',
			'h2_typography_font_weight'      => '700',
			'h2_typography_font_size'        => array( 'unit' => 'px', 'size' => 34 ),
			'h2_typography_font_size_tablet' => array( 'unit' => 'px', 'size' => 30 ),
			'h2_typography_font_size_mobile' => array( 'unit' => 'px', 'size' => 26 ),
			'h2_typography_line_height'      => array( 'unit' => 'em', 'size' => 1.25 ),
			'h3_typography_typography'       => 'custom',
			'h3_typography_font_family'      => 'Montserrat',
			'h3_typography_font_weight'      => '600',
			'h3_typography_font_size'        => array( 'unit' => 'px', 'size' => 22 ),
			'h3_typography_font_size_mobile' => array( 'unit' => 'px', 'size' => 20 ),
			'h3_typography_line_height'      => array( 'unit' => 'em', 'size' => 1.35 ),
			'h4_typography_typography'       => 'custom',
			'h4_typography_font_family'      => 'Montserrat',
			'h4_typography_font_weight'      => '600',
			'h4_typography_font_size'        => array( 'unit' => 'px', 'size' => 18 ),
			'body_color'                     => '#3A4453',
			'link_normal_color'              => '#1D5FA8',
			'link_hover_color'               => '#C8102E',
			'h1_color'                       => '#0B2D5B',
			'h2_color'                       => '#0B2D5B',
			'h3_color'                       => '#0B2D5B',
			'h4_color'                       => '#0B2D5B',
			'h5_color'                       => '#0B2D5B',
			'h6_color'                       => '#0B2D5B',

			// Theme Style → Buttons.
			'button_typography_typography'       => 'custom',
			'button_typography_font_family'      => 'Montserrat',
			'button_typography_font_weight'      => '700',
			'button_typography_font_size'        => array( 'unit' => 'px', 'size' => 14 ),
			'button_typography_text_transform'   => 'uppercase',
			'button_typography_letter_spacing'   => array( 'unit' => 'px', 'size' => 0.5 ),
			'button_text_color'                  => '#FFFFFF',
			'button_background_background'       => 'classic',
			'button_background_color'            => '#C8102E',
			'button_hover_text_color'            => '#FFFFFF',
			'button_hover_background_background' => 'classic',
			'button_hover_background_color'      => '#A50D26',
			'button_border_radius'               => array( 'unit' => 'px', 'top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'isLinked' => true ),
			'button_padding'                     => array( 'unit' => 'px', 'top' => '14', 'right' => '28', 'bottom' => '14', 'left' => '28', 'isLinked' => false ),

			// Link theme style values to the globals so changing a global colour updates everything.
			'__globals__'                        => array(
				'body_color'                    => $c( 'text' ),
				'link_normal_color'             => $c( 'secondary' ),
				'link_hover_color'              => $c( 'accent' ),
				'h1_color'                      => $c( 'apbahead' ),
				'h2_color'                      => $c( 'apbahead' ),
				'h3_color'                      => $c( 'apbahead' ),
				'h4_color'                      => $c( 'apbahead' ),
				'h5_color'                      => $c( 'apbahead' ),
				'h6_color'                      => $c( 'apbahead' ),
				'button_background_color'       => $c( 'accent' ),
				'body_typography_typography'    => $t( 'text' ),
				'button_typography_typography'  => $t( 'accent' ),
			),

			// Layout.
			'container_width'        => array( 'unit' => 'px', 'size' => 1200 ),
			'container_padding'      => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
			'space_between_widgets'  => array( 'unit' => 'px', 'size' => 20, 'column' => '20', 'row' => '20', 'isLinked' => true ),
			'page_title_selector'    => 'h1.entry-title',

			// Breakpoints: mobile < 768, tablet 768–991, laptop 992–1199, desktop 1200+.
			'active_breakpoints'     => array( 'viewport_mobile', 'viewport_tablet', 'viewport_laptop' ),
			'viewport_mobile'        => 767,
			'viewport_tablet'        => 991,
			'viewport_laptop'        => 1199,
			'viewport_md'            => 768,
			'viewport_lg'            => 992,
		);

		$current = $kit->get_settings();
		$merged  = array_merge( is_array( $current ) ? $current : array(), $settings );
		$kit->save( array( 'settings' => $merged ) );

		\Elementor\Plugin::$instance->files_manager->clear_cache();
		$this->log( 'Configured Elementor Site Settings (colours, fonts, buttons, layout, breakpoints).' );
	}

	/**
	 * Write Customizer defaults so they show in the Customizer.
	 */
	private function configure_customizer() {
		foreach ( apba_customizer_defaults() as $key => $value ) {
			if ( false === get_theme_mod( $key, false ) ) {
				set_theme_mod( $key, $value );
			}
		}
		if ( ! get_theme_mod( 'apba_default_card_image' ) && ! empty( $this->map['media']['post'] ) ) {
			set_theme_mod( 'apba_default_card_image', (int) $this->map['media']['post'] );
		}
		$this->log( 'Saved Customizer defaults (APBA Site Options).' );
	}
}
