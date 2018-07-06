/* ---  Header Init --- */

var Header = function() {
	this.isMobileHeaderInitialised = false;
	this.isDesktopHeaderInitialised = false;
	this.areMobileBindingsDone = false;
	this.areGeneralBindingsDone = false;
	this.stickyHeaderShown = false;
	this.hideReadingBar = this.$readingBar !== null;


	this.onLoad();
	this.eventHandlers();
	this.appendSearchTrigger();
	this.updateOnResize();
};

Header.prototype.onLoad = function() {
	this.isStickyHeaderEnabled = $( 'body' ).hasClass( 'u-site-header-sticky' );
	this.isSingular = $( 'body' ).hasClass( 'single' );
};

Header.prototype.eventHandlers = function() {

	var showSubMenu = function() {
		$( this ).addClass( 'hover' );
	};

	var hideSubMenu = function() {
		$( this ).removeClass( 'hover' );
	};

	if ( ! this.areGeneralBindingsDone ) {
		// Mobile menu trigger
		$( '#menu-toggle' ).on( 'change', function( e ) {
			if ( $( this ).prop( 'checked' ) ) {
				$( 'body' ).addClass( 'nav--is-open' );
			} else {
				$( 'body' ).removeClass( 'nav--is-open' );

				setTimeout( function() {
					// Close the open submenus in the mobile menu overlay
					$( '.menu--primary li' ).removeClass( 'hover' );
					$( '.menu--primary a' ).removeClass( 'active' );
				}, 300 );
			}
		} );

		$( '.menu--primary li' ).hoverIntent( {
			over: showSubMenu,
			out: hideSubMenu,
			timeout: 300
		} );

		this.areGeneralBindingsDone = true;
	}

	if ( Util.below( 'lap' ) && ! this.areMobileBindingsDone ) {
		$( document ).on( 'click', 'a.prevent-one', function( e ) {
			e.preventDefault();
			e.stopPropagation();

			if ( $( this ).hasClass( 'active' ) ) {
				window.location.href = $( this ).attr( 'href' );
				return;
			}

			$( 'a.prevent-one' ).removeClass( 'active' );
			$( this ).addClass( 'active' );

			// When a parent menu item is activated,
			// close other menu items on the same level
			$( this ).parent().siblings().removeClass( 'hover' );

			// Open the sub menu of this parent item
			$( this ).parent().addClass( 'hover' );
		} );

		this.areMobileBindingsDone = true;
	}

	if ( Util.above( 'lap' ) && this.areMobileBindingsDone ) {
		// Make sure to undo the bindings for the mobile sub menus
		$( document ).off( 'click', 'a.prevent-one' );
		this.areMobileBindingsDone = false;
	}
};

Header.prototype.prepareSingleHeader = function() {
	if ( ! this.isSingular || ! this.isStickyHeaderEnabled ) {
		return;
	}

	// Handle Reading Bar
	this.$readingBar = $( '.js-reading-bar' );

	// Handle Progress Bar
	this.$progressBar = $( '.js-reading-progress' );

	$( '.c-reading-bar__wrapper-social' ).find( '.share-end' ).remove();

	var articleHeight = 0;

	if ( $body.hasClass( 'entry-image--landscape' ) ) {
		articleHeight = $( '.entry-header' ).outerHeight() + $( '.entry-thumbnail' ).outerHeight() + $( '.entry-content__wrapper .entry-content' ).outerHeight();
	} else {
		articleHeight = $( '.entry-header' ).outerHeight() + $( '.entry-content__wrapper .entry-content' ).outerHeight();
	}

	//	if ( articleHeight > Julia.getWindowHeight() ) {
	var max = $( '.entry-header' ).offset().top + articleHeight - this.initialMenuOffset;

	if ( max > Julia.getWindowHeight() ) {
		max -= Julia.getWindowHeight();
	}

	this.$progressBar.attr( 'max', max );
	//	}

	// Bind reading bar menu trigger
	if ( this.$mainMenu.length === 1 ) {
		var _this = this;
		$( document ).on( 'click', '.js-sticky-menu-trigger', function() {
			_this.$stickyHeader.addClass( 'reading-bar--hide' );
			var that = _this;
			setTimeout(function() {
				that.$stickyHeader.css( 'overflow', '' );
			}, 350);
		} );
	} else {
		$( '.js-sticky-menu-trigger' ).remove();
	}
};

Header.prototype.refresh = function( scrollPosition ) {
	this.shouldUpdate = $( 'body' ).is( '.u-site-header-sticky' );

	if( Util.below( 'lap' ) ) {
		this.shouldUpdate = false;
	}

	this.updateOnScroll( scrollPosition );
};

Header.prototype.updateOnScroll = function( scrollPosition ) {

	if ( ! this.shouldUpdate ) {
		return;
	}

	var that = this,
		showSticky = scrollPosition > this.initialMenuOffset,
		hideReadingBar = scrollPosition < this.currentScrollPosition && this.$mainMenu.length === 1,
		showNextTitle;


	if ( this.isSingular && scrollPosition > this.initialMenuOffset ) {
		that.$progressBar.attr( 'value', scrollPosition - that.initialMenuOffset );
	}

	if ( this.$readingBar !== null && hideReadingBar !== this.hideReadingBar ) {
		clearTimeout( that.overflowTimeout );

		if ( ! hideReadingBar ) {
			that.$stickyHeader.css( 'overflow', 'hidden' );
		} else {
			this.overflowTimeout = setTimeout(function() {
				that.$stickyHeader.css( 'overflow', '' );
			}, 350);
		}

		this.$stickyHeader.toggleClass( 'reading-bar--hide', hideReadingBar );
		this.hideReadingBar = hideReadingBar;
	}

	if ( this.$progressBar !== null ) {
		showNextTitle = this.$progressBar.attr( 'max' ) <= scrollPosition - this.initialMenuOffset;
		this.$readingBar.toggleClass( 'show-next-title', showNextTitle );
	}

	if ( showSticky !== this.stickyHeaderShown ) {
		this.$stickyHeader.toggleClass( 'site-header-sticky--show', showSticky );
		this.stickyHeaderShown = showSticky;
	}

	this.currentScrollPosition = scrollPosition;
};

Header.prototype.updateOnResize = function() {
	this.eventHandlers();

	// Header behaviour below lap
	if ( Util.below( 'lap' ) ) {
		// First, do the bindings for the mobile sub menus
		this.prepareMobileMenuMarkup();
	} else {
		if ( this.isStickyHeaderEnabled ) {
			this.prepareDesktopMenuMarkup();
		}
	}
};

Header.prototype.prepareMobileMenuMarkup = function() {
	// If if has not been done yet, prepare the mark-up for the mobile navigation
	if ( ! this.isMobileHeaderInitialised ) {

		// Create the mobile site header
		var $siteHeaderMobile = $( '<div class="site-header-mobile u-container-sides-spacing"></div>' ).appendTo( '.c-navbar' );

		// Append the branding
		$( '.c-branding' ).clone().appendTo( $siteHeaderMobile );
		$( '.c-branding' ).find( 'img' ).removeClass( 'is--loading' );

		// Append the social menu
		$( '.c-navbar__zone--left .jetpack-social-navigation' ).clone().appendTo( $siteHeaderMobile );

		// Handle sub menus:
		// Make sure there are no open menu items
		$( '.menu-item-has-children' ).removeClass( 'hover' );

		// Add a class so we know the items to handle
		$( '.menu-item-has-children > a' ).each( function() {
			$( this ).addClass( 'prevent-one' );
		} );

		// Replace the label text and make it visible
		$( '.c-navbar__label-text ' ).html( $( '.js-menu-mobile-label' ).html() ).removeClass( 'screen-reader-text' );

		this.isMobileHeaderInitialised = true;
	}
}

Header.prototype.prepareDesktopMenuMarkup = function() {
	// If it has not been done yet, prepare the mark-up for the desktop navigation
	if ( ! this.isDesktopHeaderInitialised ) {
		this.$stickyHeader = $( '.js-site-header-sticky' );
		this.htmlTop = parseInt( $( 'html' ).css( 'marginTop' ), 10 );
		this.$stickyHeader.css( 'top', this.htmlTop );

		this.$mainMenu = $( '.menu--primary' );

		// Figure out where is the offset of the Main Menu.
		// If there is no Main Menu set, show the reading bar
		// after passing the branding.
		if ( this.$mainMenu.length === 1 ) {
			this.initialMenuOffset = this.$mainMenu.offset().top - this.htmlTop;
		} else {
			var $branding = $( '.c-branding' );
			this.initialMenuOffset = $branding.offset().top + $branding.outerHeight();
		}

		// Fallback to the other, secondary menu (top left one).
		if ( this.$mainMenu.length === 0 ) {
			this.$mainMenu = $( '.menu--secondary' );
		}

		// If there is a menu, either the "true" main one or the fallback one,
		// clone it and append it to the reading bar.
		if ( this.$mainMenu.length === 1 ) {
			this.$mainMenu = this.$mainMenu.clone( true, true ).appendTo( this.$stickyHeader.find( '.c-navbar' ) );
		}

		this.$stickyHeader.find( '.c-navbar' ).css( 'height', this.$stickyHeader.height() );

		this.currentScrollPosition = 0;

		this.$readingBar = null;
		this.$progressBar = null;

		this.prepareSingleHeader();

		this.refresh();

		this.isDesktopHeaderInitialised = true;
	}
};

Header.prototype.appendSearchTrigger = function() {
	var $headerSocialNavigation = $( '.c-navbar__zone--left .jetpack-social-navigation' );

	this.$searchTrigger = $( '.js-search-trigger' ).removeClass( 'u-hidden' );

	// Append the search trigger either to the social navigation
	if ( $headerSocialNavigation.length === 1 ) {
		this.$searchTrigger.clone().wrap( '<li class="menu-item"></li>' ).parent().appendTo( $headerSocialNavigation.find( '.menu' ) );
	} else {
		// Or directly to zone left if there is no social navigation
		this.$searchTrigger.clone().appendTo( $( '.c-navbar__zone--left' ) );
	}

	this.$searchTrigger.clone().appendTo( $( '.site-header-sticky .c-navbar' ) );

	this.$searchTrigger.remove();
};
