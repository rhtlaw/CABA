/**
 * Header navigation: mobile menu toggle, accessible sub-menus, sticky shadow.
 * No dependencies.
 */
( function () {
	'use strict';

	var header = document.getElementById( 'site-header' );
	var toggle = document.querySelector( '.apba-menu-toggle' );
	var nav = document.getElementById( 'apba-primary-nav' );
	if ( ! header || ! nav ) {
		return;
	}

	var mobileQuery = window.matchMedia( '(max-width: 991px)' );

	function setMenuOpen( open ) {
		if ( ! toggle ) {
			return;
		}
		if ( open ) {
			// Panel starts right under the visible header (top bar may be scrolled away).
			var bottom = header.querySelector( '.apba-header__main' ).getBoundingClientRect().bottom;
			nav.style.setProperty( '--apba-nav-top', Math.max( 0, Math.round( bottom ) ) + 'px' );
		}
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		header.classList.toggle( 'is-menu-open', open );
		document.body.classList.toggle( 'apba-no-scroll', open );
	}

	function setSubmenu( button, open ) {
		var item = button.closest( '.menu-item' );
		button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		if ( item ) {
			item.classList.toggle( 'is-open', open );
		}
	}

	function closeAllSubmenus( except ) {
		nav.querySelectorAll( '.apba-submenu-toggle[aria-expanded="true"]' ).forEach( function ( btn ) {
			if ( btn !== except && ! btn.closest( '.menu-item' ).contains( except ) ) {
				setSubmenu( btn, false );
			}
		} );
	}

	if ( toggle ) {
		toggle.addEventListener( 'click', function () {
			setMenuOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
		} );
	}

	nav.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.apba-submenu-toggle' );
		if ( ! button ) {
			return;
		}
		var open = button.getAttribute( 'aria-expanded' ) !== 'true';
		if ( open && ! mobileQuery.matches ) {
			closeAllSubmenus( button );
		}
		setSubmenu( button, open );
	} );

	// Desktop: open on hover is handled in CSS; keep keyboard focus in sync.
	nav.addEventListener( 'focusout', function ( event ) {
		if ( mobileQuery.matches ) {
			return;
		}
		var item = event.target.closest( '.menu-item-has-children' );
		if ( item && ! item.contains( event.relatedTarget ) ) {
			var btn = item.querySelector( ':scope > .apba-submenu-toggle' );
			if ( btn ) {
				setSubmenu( btn, false );
			}
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key !== 'Escape' ) {
			return;
		}
		var openBtn = nav.querySelector( '.apba-submenu-toggle[aria-expanded="true"]' );
		if ( openBtn && nav.contains( document.activeElement ) ) {
			var items = nav.querySelectorAll( '.apba-submenu-toggle[aria-expanded="true"]' );
			var last = items[ items.length - 1 ];
			setSubmenu( last, false );
			last.focus();
			return;
		}
		if ( header.classList.contains( 'is-menu-open' ) ) {
			setMenuOpen( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( ! nav.contains( event.target ) && ! mobileQuery.matches ) {
			closeAllSubmenus( null );
		}
	} );

	// Reset state when crossing the breakpoint.
	var onBreakpoint = function () {
		setMenuOpen( false );
		closeAllSubmenus( null );
	};
	if ( mobileQuery.addEventListener ) {
		mobileQuery.addEventListener( 'change', onBreakpoint );
	} else if ( mobileQuery.addListener ) {
		mobileQuery.addListener( onBreakpoint );
	}

	// Expand the branch containing the current page in the mobile menu.
	nav.querySelectorAll( '.current-menu-ancestor > .apba-submenu-toggle' ).forEach( function ( btn ) {
		if ( mobileQuery.matches ) {
			setSubmenu( btn, true );
		}
	} );

	// Sticky header shadow once the page scrolls.
	if ( document.body.classList.contains( 'apba-has-sticky-header' ) ) {
		var ticking = false;
		window.addEventListener(
			'scroll',
			function () {
				if ( ticking ) {
					return;
				}
				ticking = true;
				window.requestAnimationFrame( function () {
					header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
					ticking = false;
				} );
			},
			{ passive: true }
		);
	}
}() );
