<?php
/**
 * Tiny helper for producing Elementor (free) page data as PHP arrays.
 *
 * Only core Elementor elements are used: Container (Flexbox), Heading,
 * Text Editor, Button, Image, Icon Box, Icon List, Divider, Spacer,
 * Google Maps and Shortcode.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Elementor data builder.
 */
class APBA_El {

	/**
	 * Element counter for deterministic IDs.
	 *
	 * @var int
	 */
	private static $n = 0;

	/**
	 * Seed for IDs (changes per page so IDs are unique site-wide).
	 *
	 * @var string
	 */
	private static $seed = 'apba';

	/**
	 * Media library IDs keyed by placeholder name, set by the importer.
	 *
	 * @var array<string,int>
	 */
	public static $media = array();

	/**
	 * Start a new document.
	 *
	 * @param string $seed Page slug.
	 */
	public static function reset( $seed ) {
		self::$n    = 0;
		self::$seed = $seed;
	}

	/**
	 * Elementor-style 7 char id.
	 *
	 * @return string
	 */
	private static function id() {
		++self::$n;
		return substr( md5( self::$seed . '-' . self::$n ), 0, 7 );
	}

	/**
	 * Dimension value.
	 *
	 * @param int|string $t Top.
	 * @param int|string $r Right.
	 * @param int|string $b Bottom.
	 * @param int|string $l Left.
	 * @param string     $unit Unit.
	 * @return array
	 */
	public static function dim( $t, $r = null, $b = null, $l = null, $unit = 'px' ) {
		$r = null === $r ? $t : $r;
		$b = null === $b ? $t : $b;
		$l = null === $l ? $r : $l;
		return array(
			'unit'     => $unit,
			'top'      => (string) $t,
			'right'    => (string) $r,
			'bottom'   => (string) $b,
			'left'     => (string) $l,
			'isLinked' => ( $t === $r && $r === $b && $b === $l ),
		);
	}

	/**
	 * Slider value.
	 *
	 * @param int|float $size Size.
	 * @param string    $unit Unit.
	 * @return array
	 */
	public static function size( $size, $unit = 'px' ) {
		return array(
			'unit'  => $unit,
			'size'  => $size,
			'sizes' => array(),
		);
	}

	/**
	 * Gap value.
	 *
	 * @param int $row    Row gap.
	 * @param int $column Column gap.
	 * @return array
	 */
	public static function gap( $row, $column = null ) {
		$column = null === $column ? $row : $column;
		return array(
			'unit'     => 'px',
			'size'     => $column,
			'column'   => (string) $column,
			'row'      => (string) $row,
			'isLinked' => $row === $column,
		);
	}

	/**
	 * Reference to a global colour (Elementor → Site Settings → Global Colors).
	 *
	 * @param string $id primary|secondary|text|accent|apbahead|apbasurf|...
	 * @return string
	 */
	public static function color( $id ) {
		return 'globals/colors?id=' . $id;
	}

	/**
	 * Reference to a global font.
	 *
	 * @param string $id primary|secondary|text|accent.
	 * @return string
	 */
	public static function font( $id ) {
		return 'globals/typography?id=' . $id;
	}

	/**
	 * Image setting from a placeholder key.
	 *
	 * @param string $key Media key.
	 * @return array
	 */
	public static function img( $key ) {
		$id = isset( self::$media[ $key ] ) ? (int) self::$media[ $key ] : 0;
		return array(
			'id'     => $id,
			'url'    => $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '',
			'alt'    => $id ? (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) : '',
			'source' => 'library',
		);
	}

	/**
	 * Flexbox container.
	 *
	 * @param array $settings Settings.
	 * @param array $children Child elements.
	 * @return array
	 */
	public static function con( $settings, $children = array() ) {
		$defaults = array(
			'content_width'  => 'boxed',
			'flex_direction' => 'column',
		);
		return array(
			'id'       => self::id(),
			'elType'   => 'container',
			'isInner'  => false,
			'settings' => array_merge( $defaults, $settings ),
			'elements' => array_values( array_filter( $children ) ),
		);
	}

	/**
	 * Inner container (column) helper: full content width with a % width.
	 *
	 * @param int|float $width    Desktop width %.
	 * @param array     $settings Extra settings.
	 * @param array     $children Children.
	 * @return array
	 */
	public static function col( $width, $settings, $children ) {
		$el            = self::con(
			array_merge(
				array(
					'content_width' => 'full',
					'width'         => self::size( $width, '%' ),
					'width_tablet'  => self::size( 100, '%' ),
					'width_mobile'  => self::size( 100, '%' ),
					'padding'       => self::dim( 0 ),
				),
				$settings
			),
			$children
		);
		$el['isInner'] = true;
		return $el;
	}

	/**
	 * Widget.
	 *
	 * @param string $type     Widget type.
	 * @param array  $settings Settings.
	 * @return array
	 */
	public static function w( $type, $settings ) {
		return array(
			'id'         => self::id(),
			'elType'     => 'widget',
			'widgetType' => $type,
			'isInner'    => false,
			'settings'   => $settings,
			'elements'   => array(),
		);
	}

	/**
	 * Heading widget.
	 *
	 * @param string $text  Text.
	 * @param string $tag   h1..h6|p|div.
	 * @param array  $extra Extra settings.
	 * @return array
	 */
	public static function heading( $text, $tag = 'h2', $extra = array() ) {
		return self::w(
			'heading',
			array_merge(
				array(
					'title'       => $text,
					'header_size' => $tag,
				),
				$extra
			)
		);
	}

	/**
	 * Text editor widget.
	 *
	 * @param string $html  HTML.
	 * @param array  $extra Extra settings.
	 * @return array
	 */
	public static function text( $html, $extra = array() ) {
		return self::w( 'text-editor', array_merge( array( 'editor' => $html ), $extra ) );
	}

	/**
	 * Button widget.
	 *
	 * @param string $text  Label.
	 * @param string $url   Link.
	 * @param string $style accent|primary|outline-light|outline.
	 * @param array  $extra Extra settings.
	 * @return array
	 */
	public static function button( $text, $url, $style = 'accent', $extra = array() ) {
		$settings = array(
			'text' => $text,
			'link' => array(
				'url'         => $url,
				'is_external' => '',
				'nofollow'    => '',
			),
			'size' => 'md',
		);
		switch ( $style ) {
			case 'primary':
				$settings['__globals__'] = array( 'background_color' => self::color( 'primary' ) );
				$settings['hover_color'] = '#FFFFFF';
				$settings['__globals__']['button_background_hover_color'] = self::color( 'secondary' );
				break;
			case 'outline-light':
				$settings['_css_classes'] = 'apba-button-outline-light';
				break;
			case 'outline':
				$settings['background_color']    = '#FFFFFF00';
				$settings['border_border']       = 'solid';
				$settings['border_width']        = self::dim( 2 );
				$settings['__globals__']         = array(
					'button_text_color' => self::color( 'primary' ),
					'border_color'      => self::color( 'primary' ),
					'button_background_hover_color' => self::color( 'primary' ),
				);
				$settings['hover_color'] = '#FFFFFF';
				break;
		}
		return self::w( 'button', array_merge( $settings, $extra ) );
	}

	/**
	 * Image widget.
	 *
	 * @param string $key   Media key.
	 * @param array  $extra Extra settings.
	 * @return array
	 */
	public static function image( $key, $extra = array() ) {
		return self::w(
			'image',
			array_merge(
				array(
					'image'      => self::img( $key ),
					'image_size' => 'large',
				),
				$extra
			)
		);
	}

	/**
	 * Icon box widget.
	 *
	 * @param string $icon  Font Awesome class (fas fa-…).
	 * @param string $title Title.
	 * @param string $desc  Description.
	 * @param array  $extra Extra settings.
	 * @return array
	 */
	public static function icon_box( $icon, $title, $desc, $extra = array() ) {
		return self::w(
			'icon-box',
			array_merge(
				array(
					'selected_icon'    => array(
						'value'   => $icon,
						'library' => 0 === strpos( $icon, 'far' ) ? 'fa-regular' : 'fa-solid',
					),
					'title_text'       => $title,
					'description_text' => $desc,
					'title_size'       => 'h3',
					'position'         => 'top',
					'text_align'       => 'left',
					'icon_size'        => self::size( 34 ),
					'icon_space'       => self::size( 18 ),
					'title_bottom_space' => self::size( 12 ),
					'__globals__'      => array(
						'primary_color'           => self::color( 'primary' ),
						'title_color'             => self::color( 'apbahead' ),
						'description_color'       => self::color( 'text' ),
						'title_typography_typography' => self::font( 'secondary' ),
					),
				),
				$extra
			)
		);
	}

	/**
	 * Icon list widget.
	 *
	 * @param string[] $items Items (plain text).
	 * @param array    $extra Extra settings.
	 * @return array
	 */
	public static function checklist( $items, $extra = array() ) {
		$list = array();
		foreach ( $items as $i => $item ) {
			$list[] = array(
				'_id'           => substr( md5( self::$seed . 'li' . self::$n . $i ), 0, 7 ),
				'text'          => $item,
				'selected_icon' => array(
					'value'   => 'fas fa-check-circle',
					'library' => 'fa-solid',
				),
			);
		}
		return self::w(
			'icon-list',
			array_merge(
				array(
					'icon_list'    => $list,
					'space_between' => self::size( 12 ),
					'icon_size'    => self::size( 16 ),
					'text_indent'  => self::size( 10 ),
					'icon_self_vertical_align' => 'flex-start',
					'icon_vertical_offset' => self::size( 5 ),
					'_css_classes' => 'apba-checklist',
					'__globals__'  => array(
						'icon_color'           => self::color( 'accent' ),
						'text_color'           => self::color( 'text' ),
						'icon_typography_typography' => self::font( 'text' ),
					),
				),
				$extra
			)
		);
	}

	/**
	 * Shortcode widget.
	 *
	 * @param string $shortcode Shortcode.
	 * @param array  $extra     Extra settings.
	 * @return array
	 */
	public static function shortcode( $shortcode, $extra = array() ) {
		return self::w( 'shortcode', array_merge( array( 'shortcode' => $shortcode ), $extra ) );
	}

	/**
	 * Divider widget.
	 *
	 * @param array $extra Extra settings.
	 * @return array
	 */
	public static function divider( $extra = array() ) {
		return self::w(
			'divider',
			array_merge(
				array(
					'width'       => self::size( 56 ),
					'weight'      => self::size( 3 ),
					'gap'         => self::size( 4 ),
					'__globals__' => array( 'color' => self::color( 'accent' ) ),
				),
				$extra
			)
		);
	}

	/**
	 * Google Maps widget (free, no API key).
	 *
	 * @param string $address Address.
	 * @return array
	 */
	public static function map( $address ) {
		return self::w(
			'google_maps',
			array(
				'address'      => $address,
				'zoom'         => self::size( 16 ),
				'height'       => self::size( 380 ),
				'height_mobile' => self::size( 280 ),
				'_css_classes' => 'apba-map',
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Composite blocks shared by several pages.
	 * ------------------------------------------------------------------- */

	/**
	 * Section heading group: eyebrow + H2 with accent rule + optional intro.
	 *
	 * @param string $eyebrow Eyebrow text ('' to skip).
	 * @param string $title   Title.
	 * @param string $intro   Intro HTML ('' to skip).
	 * @param string $align   left|center.
	 * @param bool   $light   White text (dark backgrounds).
	 * @return array[]
	 */
	public static function section_heading( $eyebrow, $title, $intro = '', $align = 'left', $light = false ) {
		$out = array();
		if ( $eyebrow ) {
			$out[] = self::heading(
				$eyebrow,
				'p',
				array(
					'align'        => $align,
					'_css_classes' => 'apba-eyebrow',
					'_margin'      => self::dim( 0, 0, -8, 0 ),
				)
			);
		}
		$heading_settings = array(
			'align'        => $align,
			'_css_classes' => 'apba-title-rule' . ( 'center' === $align ? ' apba-title-rule--center' : '' ),
			'__globals__'  => array(
				'title_color'           => $light ? '' : self::color( 'apbahead' ),
				'typography_typography' => self::font( 'primary' ),
			),
		);
		if ( $light ) {
			$heading_settings['title_color'] = '#FFFFFF';
		}
		$out[] = self::heading( $title, 'h2', $heading_settings );
		if ( $intro ) {
			$intro_settings = array( 'align' => $align );
			if ( $light ) {
				$intro_settings['text_color'] = '#FFFFFFD9';
			} else {
				$intro_settings['__globals__'] = array( 'text_color' => self::color( 'text' ) );
			}
			$out[] = self::text( $intro, $intro_settings );
		}
		return $out;
	}

	/**
	 * Inner page banner: breadcrumbs + H1 + optional subtitle on the banner image.
	 *
	 * @param string $title    H1.
	 * @param string $subtitle Subtitle.
	 * @return array
	 */
	public static function page_banner( $title, $subtitle = '' ) {
		$children = array(
			self::shortcode( '[apba_breadcrumbs]', array( '_margin' => self::dim( 0, 0, -6, 0 ) ) ),
			self::heading(
				$title,
				'h1',
				array(
					'title_color' => '#FFFFFF',
					'__globals__' => array( 'typography_typography' => self::font( 'primary' ) ),
				)
			),
		);
		if ( $subtitle ) {
			$children[] = self::text( '<p>' . esc_html( $subtitle ) . '</p>', array( 'text_color' => '#FFFFFFD9', '_margin' => self::dim( 0 ) ) );
		}
		return self::con(
			array(
				'css_classes'                   => 'apba-page-banner',
				'min_height'                    => self::size( 260 ),
				'min_height_mobile'             => self::size( 200 ),
				'flex_justify_content'          => 'center',
				'flex_gap'                      => self::gap( 12 ),
				'padding'                       => self::dim( 56, 24, 52, 24 ),
				'padding_mobile'                => self::dim( 40, 16, 36, 16 ),
				'background_background'         => 'classic',
				'background_image'              => self::img( 'banner' ),
				'background_position'           => 'center center',
				'background_size'               => 'cover',
				'background_overlay_background' => 'gradient',
				'background_overlay_color'      => '#071D3B',
				'background_overlay_color_b'    => '#1D5FA8',
				'background_overlay_gradient_angle' => self::size( 110, 'deg' ),
				'background_overlay_opacity'    => self::size( 0.9 ),
				'html_tag'                      => 'section',
			),
			$children
		);
	}

	/**
	 * Full-width CTA band.
	 *
	 * @param string $title  Title.
	 * @param string $text   Text.
	 * @param string $label  Button label.
	 * @param string $url    Button URL.
	 * @return array
	 */
	public static function cta_band( $title, $text, $label, $url ) {
		return self::con(
			array(
				'html_tag'              => 'section',
				'padding'               => self::dim( 72, 24 ),
				'padding_mobile'        => self::dim( 56, 16 ),
				'background_background' => 'classic',
				'__globals__'           => array( 'background_color' => self::color( 'primary' ) ),
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_justify_content'  => 'space-between',
				'flex_align_items'      => 'center',
				'flex_align_items_tablet' => 'flex-start',
				'flex_gap'              => self::gap( 24, 40 ),
			),
			array(
				self::col(
					70,
					array( 'flex_gap' => self::gap( 8 ) ),
					array(
						self::heading( $title, 'h2', array( 'title_color' => '#FFFFFF' ) ),
						self::text( '<p>' . esc_html( $text ) . '</p>', array( 'text_color' => '#FFFFFFD9', '_margin' => self::dim( 0 ) ) ),
					)
				),
				self::col(
					30,
					array(
						'flex_align_items'        => 'flex-end',
						'flex_align_items_tablet' => 'flex-start',
					),
					array( self::button( $label, $url, 'accent', array( 'size' => 'lg' ) ) )
				),
			)
		);
	}

	/**
	 * Standard content section wrapper.
	 *
	 * @param array  $children Children.
	 * @param array  $extra    Extra settings.
	 * @param string $bg       '' | 'surface'.
	 * @return array
	 */
	public static function section( $children, $extra = array(), $bg = '' ) {
		$settings = array(
			'html_tag'       => 'section',
			'padding'        => self::dim( 80, 24 ),
			'padding_tablet' => self::dim( 64, 24 ),
			'padding_mobile' => self::dim( 48, 16 ),
			'flex_gap'       => self::gap( 24 ),
		);
		if ( 'surface' === $bg ) {
			$settings['background_background'] = 'classic';
			$settings['__globals__']           = array( 'background_color' => self::color( 'apbasurf' ) );
		}
		return self::con( array_merge( $settings, $extra ), $children );
	}

	/**
	 * Two-column row (stacks on tablet/mobile).
	 *
	 * @param array $left   Left children.
	 * @param array $right  Right children.
	 * @param int   $split  Left width %.
	 * @param array $extra  Extra row settings.
	 * @return array
	 */
	public static function two_col( $left, $right, $split = 50, $extra = array() ) {
		$gap = 56;
		return self::con(
			array_merge(
				array(
					'content_width'         => 'full',
					'flex_direction'        => 'row',
					'flex_direction_tablet' => 'column',
					'flex_gap'              => self::gap( 32, $gap ),
					'flex_align_items'      => 'center',
					'padding'               => self::dim( 0 ),
				),
				$extra
			),
			array(
				self::col( $split, array( 'flex_gap' => self::gap( 16 ) ), $left ),
				self::col( 100 - $split, array( 'flex_gap' => self::gap( 16 ) ), $right ),
			)
		);
	}

	/**
	 * Row of equal cards (each child list becomes a bordered box).
	 *
	 * @param array[] $cards   List of children arrays.
	 * @param int     $per_row Cards per row on desktop.
	 * @return array
	 */
	public static function card_row( $cards, $per_row = 3 ) {
		$cols = array();
		// Slightly under 1/n so n cards plus the 30px gaps fit; "grow" fills the rest.
		$widths = array( 1 => 100, 2 => 47, 3 => 30, 4 => 22 );
		$width  = isset( $widths[ $per_row ] ) ? $widths[ $per_row ] : 30;
		foreach ( $cards as $children ) {
			$cols[] = self::col(
				$width,
				array(
					'css_classes'  => 'apba-box',
					'width_tablet' => self::size( $per_row > 2 ? 48 : 100, '%' ),
					'width_mobile' => self::size( 100, '%' ),
					'padding'      => self::dim( 36, 32 ),
					'padding_mobile' => self::dim( 28, 22 ),
					'flex_gap'     => self::gap( 12 ),
					'flex_justify_content' => 'flex-start',
					'_flex_size'   => 'grow',
				),
				$children
			);
		}
		return self::con(
			array(
				'content_width'        => 'full',
				'flex_direction'       => 'row',
				'flex_wrap'            => 'wrap',
				'flex_align_items'     => 'stretch',
				'flex_gap'             => self::gap( 30 ),
				'flex_gap_tablet'      => self::gap( 24 ),
				'padding'              => self::dim( 0 ),
			),
			$cols
		);
	}
}
