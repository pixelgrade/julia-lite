var Julia = new pixelgradeTheme(),
	log = Julia.log,
  enableHeader = true,
	resizeEvent = 'ontouchstart' in window && 'onorientationchange' in window ? 'pxg:orientationchange' : 'pxg:resize',
	ieVersion = Util.getIEversion(),
	$html = $( 'html' ),
	$body = $( 'body' );

Julia.init = function() {
	log( 'Julia.init' );

	Julia.Recipe = new Recipe();
	Julia.SearchOverlay = new SearchOverlay();
  enableHeader && (Julia.Header = new Header());

	$html.toggleClass( 'is-IE', ieVersion && ieVersion < 12 );

	Julia.eventHandlersOnce();
	Julia.handleContent();
	Julia.adjustLayout();
	Julia.fadeIn();

	// expose pixelgradeTheme API
	$.Julia = Julia;
};

Julia.update = function() {
	log( 'Julia.update' );

	Julia.backToTop();
  enableHeader && Julia.Header.refresh( Julia.getScroll() );
};

// function used to wrap entry title around featured image
Julia.wrapTitle = function() {

	var $rightSideElement;

	// get featured image bounding box
	if( $body.hasClass( 'entry-image--portrait' ) ) {
		$rightSideElement = $( '.entry-thumbnail' );
	} else if ( $body.hasClass( 'has-sidebar' ) && $body.hasClass( 'single-post' ) ) {
		$rightSideElement = $( '.widget-area--post' );
	} else {
		return;
	}

	var $string, words, rightSideElementBox;

	$string = $( ".entry-title" );

	// split title into words
	words = $.trim( $string.text() ).split( " " );

	// empty title container
	$string.empty();

	// wrap each word in a span and add it back to the title container
	// there should be a trailing space after the closing tag
	$.each( words, function( i, w ) {
		$string.append( "<span>" + w + "</span> " );
	} );

	rightSideElementBox = $rightSideElement[0].getBoundingClientRect();

	$.fn.reverse = Array.prototype.reverse;

	// loop through each of the newly created spans
	// if it overlaps the bounding box of the featured image add a new line before it
	$string.find( 'span' ).reverse().each( function( i, obj ) {
		var $span = $( obj ),
			spanBox = obj.getBoundingClientRect();

		if ( spanBox.bottom > rightSideElementBox.top && spanBox.right > rightSideElementBox.left ) {
			$span.replaceWith( $( '<br><span>' + $span.text() + '</span>' ) );
			return false;
		}
	} );

	if( Util.above( 'small' ) ) {
		$( '.header-dropcap' ).each( function () {
			$( this ).fitText();
		} );
	}

};

// function used to scale or hide the meta separator on card elements
// the separator should be displayed only if the meta elements are displayed on one line
// while still having a width greater or equal with 14px
Julia.scaleCardSeparators = function() {

	// loop through each card
	$( '.c-card:not(.c-card--related)' ).each( function( i, obj ) {

		var $card = $( obj ),
			$meta = $card.find( '.c-meta' ),
			$separator = $card.find( '.js-card-meta-separator' ).hide(),
			width = $card.find( '.c-card__meta' ).outerWidth(),
			totalWidth = 0;

		// calculate the sum of the widths of the meta elements
		$meta.children().each( function( j, obj ) {
			totalWidth += obj.offsetWidth;
		} );

		console.log(totalWidth, width);

		// if there are still at least 14px left, display the separator
		if ( totalWidth + 14 <= width ) {
			$separator.show();
		}
	} );

};

// function used to adjust the layout of the page after load and resize events
Julia.adjustLayout = function() {
	log( 'Julia.adjustLayout' );

	var $gallery = $( '.js-masonry' );

	$gallery.each( function( i, obj ) {
		var $obj = $( obj );

		$obj.children().addClass( 'post--loaded' );

		$obj.imagesLoaded( function() {
			$obj.masonry( {transitionDuration: 0} );
		} );
	} );

	if( Util.above( 'small' ) ) {
		$( '.header-dropcap, .post:not(.has-post-thumbnail) .c-card__letter' ).each(function() {
			$( this ).css( 'opacity', 1 );
		});
	}

	Julia.wrapTitle();
	Julia.scaleCardSeparators();
	Julia.positionSidebar();

	// If the branding happens to be in the Left Zone (no Top Menu set), move it in the middle zone
	if ( $( '.header.nav' ).parent().hasClass( 'c-navbar__zone--left' ) ) {
		$( '.header.nav' ).appendTo( '.c-navbar__zone--middle' );
	}

	// If the Top Menu is not present, ensure that items in the left zone are aligned to the right
	if ( $( '.menu--secondary' ).length == 0 ) {
		$( '.c-navbar__zone--left' ).addClass( 'u-justify-end' );
	}

	$( '.entry-content' ).find( 'figure' ).filter( '.aligncenter, .alignnone' ).each( function( i, obj ) {
		var $figure = $( obj ),
			$image = $figure.find( 'img' ),
			figureWidth = $figure.outerWidth(),
			imageWidth = $image.outerWidth();

		if ( imageWidth < figureWidth ) {
			$figure.wrap( '<p>' );
		}
	} );

  enableHeader && Julia.Header.updateOnResize();

	$( '.header-dropcap, .c-card__letter' ).each( function() {
		$( this ).fitText();
	} );

	Julia.Gallery = new Gallery();

};

Julia.handleContent = function( $container ) {
	$container = typeof $container !== 'undefined' ? $container : $body;

	log( 'Julia.handleContent', $container );

	Julia.Recipe.positionPrintBtn();
	Julia.Recipe.wrapRecipeElements();

	Util.unwrapImages( $container.find( '.entry-content' ) );
	Util.wrapEmbeds( $container.find( '.entry-content' ) );
	Util.handleVideos( $container );
	Util.handleCustomCSS( $container );
	Julia.wrapTitle();

	$('.single .entry-content .tiled-gallery').wrap( '<div class="aligncenter" />');

	$container.find( '.header-dropcap, .post:not(.has-post-thumbnail) .c-card__letter' ).each( function() {
		$( this ).css( 'opacity', 1 );
	} );

	// add every image on the page the .is-loaded class
	// after the image has actually loaded
	$container.find( '.widget_categories_image_grid, .c-card__frame, .entry-thumbnail' ).find( 'img' ).each( function( i, obj ) {
		var $each = $( obj );

		$each.imagesLoaded( function() {
			$each.addClass( 'is-loaded' );
		} );
	} );

	// Handle the masonry galleries
	$container.find( '.u-gallery-type--masonry, .c-gallery--masonry' ).each( function( i, obj ) {
		var $gallery = $( obj );

		$gallery.imagesLoaded( function() {
			$gallery.masonry( {
				transitionDuration: 0
			} );
		} );
	} );

	if ( $container.hasClass( 'page-template-front-page' ) ) {
		var $widgetArea = $container.find( '.content-area .widget-area' ).children().first(),
			$widget = $widgetArea.children( '.widget' ).first(),
			isFullWidth = $widgetArea.is( '.o-layout__full' ),
			isProperWidget = $widget.is( '.widget_featured_posts_5cards, .widget_featured_posts_6cards, .widget_featured_posts_grid' ),
			hasTitle = $widget.children( '.widget__title' ).length > 0;

		if ( isFullWidth && isProperWidget && ! hasTitle ) {
			$body.addClass( 'has-extended-header-background' );
		}
	}

	if ( $container.hasClass( 'blog' ) && ! $container.hasClass( 'u-site-header-short' ) && ! $( '.o-layout__side' ).length ) {
		$body.addClass( 'has-extended-header-background' );
	}

	$container.find( '.entry-content p' ).each(function(i, obj) {
		var $p = $(obj);

		if ( ! $p.children().length && ! $.trim( $p.text() ).length ) {
			$p.remove();
		}
	});

	if( $( '.comment-form' ).length ) {
		var $commentFormFooter = $( '<p class="comment-form-subscriptions"></p>' ).appendTo( $( '.comment-form' ) );
		$( '.comment-subscription-form' ).appendTo( $commentFormFooter );
	}

	Julia.eventHandlers( $container );
};

Julia.positionSidebar = function() {

	if ( $body.is( '.entry-image--portrait' ) ) {

		var $container = $( '.entry-content__wrapper' ),
			$sidebar = $( '.widget-area--post' ),
			containerHeight, containerOffset, sidebarHeight, sidebarOffset;

		if ( ! $container.length || ! $sidebar.length ) {
			return;
		}

		// remove possible properties set on prior calls of this function
		$container.css( 'min-height', '' );
		$sidebar.css( {
			position: '',
			top: '',
			right: ''
		} );

		if ( Util.below( 'lap' ) ) {
			return;
		}

		containerHeight = $container.outerHeight();
		containerOffset = $container.offset();
		sidebarHeight = $sidebar.outerHeight();
		sidebarOffset = $sidebar.offset();

//		if ( sidebarOffset.top + sidebarHeight >= containerOffset.top + containerHeight ) {
			$container.css( 'min-height', sidebarHeight + sidebarOffset.top - containerOffset.top );
//		}

		$sidebar.css( {
			position: 'absolute',
			top: sidebarOffset.top - containerOffset.top,
			right: 0
		} );

	}
};

var onJetpackPostLoad = function() {

	var $container = $( '#posts-container' ),
		$newBlocks = $container.children().not( '.post--loaded' ).addClass( 'post--loaded' );

	$newBlocks.imagesLoaded( function() {

		if ( $container.hasClass( 'js-masonry' ) ) {
			$container.masonry( 'appended', $newBlocks, true ).masonry( 'layout' );
			$( '.infinite-loader' ).hide();
		}

	} );

	Julia.handleContent( $container );
	Julia.adjustLayout();
};

Julia.eventHandlersOnce = function() {
	log( 'Julia.eventHandlersOnce' );

	// Leave comments area visible by default and
	// show it only if the URL links to a comment
	if ( window.location.href.indexOf( '#comment' ) === -1 ) {
		$( '.trigger-comments' ).removeAttr( 'checked' );
	}

	$( window ).on( resizeEvent, Julia.adjustLayout );
	$( window ).on( 'beforeunload', Julia.fadeOut );
	$( window ).on( 'load', Julia.adjustLayout );

	$( document.body ).on( 'post-load', onJetpackPostLoad );

	Julia.ev.on( 'render', Julia.update );
};

Julia.eventHandlers = function( $container ) {
	log( 'Julia.eventHandlers' );

	$container.find( '.back-to-top' ).on( 'click', function( e ) {
		e.preventDefault();
		Util.smoothScrollTo( 0, 1000 );
	} );

	$container.find( '.widget_categories select' ).select2();
};

Julia.fadeOut = function() {
	log( 'Julia.fadeOut' );

	$( 'html' ).removeClass( 'fade-in' ).addClass( 'fade-out' );
};

Julia.fadeIn = function() {
	log( 'Julia.fadeIn' );

	$( 'html' ).removeClass( 'fade-out no-transitions' ).addClass( 'fade-in' );
};

Julia.backToTop = function() {
	if ( this.getScroll() >= this.getWindowHeight() ) {
		$( '.back-to-top' ).css( 'opacity', 1 );
	} else {
		$( '.back-to-top' ).css( 'opacity', 0 );
	}
};

$( document ).ready( function() {
	Julia.init();
} );
