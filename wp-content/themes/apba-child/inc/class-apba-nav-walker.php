<?php
/**
 * Primary navigation walker: adds an accessible toggle button after every
 * parent item so sub-menus work by keyboard, touch and mouse.
 *
 * @package APBA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Walker for the header menu.
 */
class APBA_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Running counter for unique sub-menu IDs.
	 *
	 * @var int
	 */
	private static $submenu_index = 0;

	/**
	 * ID of the sub-menu about to be opened.
	 *
	 * @var string
	 */
	private $pending_submenu_id = '';

	/**
	 * Opens a sub-menu list.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$id      = $this->pending_submenu_id ? ' id="' . esc_attr( $this->pending_submenu_id ) . '"' : '';
		$output .= '<ul class="sub-menu"' . $id . '>';
	}

	/**
	 * Opens an item; appends the toggle button for parents.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );

		$el_output = '';
		parent::start_el( $el_output, $item, $depth, $args, $id );

		if ( $has_children ) {
			++self::$submenu_index;
			$this->pending_submenu_id = 'apba-submenu-' . self::$submenu_index;

			$label = sprintf(
				/* translators: %s: menu item title. */
				__( 'Show %s submenu', 'apba' ),
				wp_strip_all_tags( $item->title )
			);

			$el_output .= sprintf(
				'<button type="button" class="apba-submenu-toggle" aria-expanded="false" aria-controls="%1$s"><span class="screen-reader-text">%2$s</span><svg aria-hidden="true" focusable="false" width="12" height="12" viewBox="0 0 12 12"><path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg></button>',
				esc_attr( $this->pending_submenu_id ),
				esc_html( $label )
			);
		} else {
			$this->pending_submenu_id = '';
		}

		$output .= $el_output;
	}
}
