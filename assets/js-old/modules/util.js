var Util = {
	/**
	 *
	 * @returns {boolean}
	 */
	isTouch: function() {
		return ! ! (
			"ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch
		);
	},

	handleCustomCSS: function( $container ) {
		var $elements = typeof $container !== "undefined" ? $container.find( "[data-css]" ) : $( "[data-css]" );

		if ( $elements.length ) {
			$elements.each( function( i, obj ) {
				var $element = $( obj ),
					css = $element.data( 'css' );

				if ( typeof css !== "undefined" ) {
					$element.replaceWith( '<style type="text/css">' + css + '</style>' );
				}
			} );
		}
	},


	/**
	 * Search every image that is alone in a p tag and wrap it
	 * in a figure element to behave like images with captions
	 *
	 * @param $container
	 */
	unwrapImages: function( $container ) {

		$container = typeof $container !== "undefined" ? $container : $body;

		$container.find( 'p > img:first-child:last-child, p > a:first-child:last-child > img' ).each( function( i, obj ) {
			var $obj = $( obj ),
				$image = $obj.closest( 'img' ),
				className = $image.attr( 'class' ),
				$p = $image.closest( 'p' ),
				$figure = $( '<figure />' ).attr( 'class', className );

			if ( $.trim( $p.text() ).length ) {
				return;
			}

			$figure.append( $image.removeAttr( 'class' ) );
			$p.replaceWith( $figure );
		} );
	},

	wrapEmbeds: function( $container ) {
		$container = typeof $container !== "undefined" ? $container : $( 'body' );
		$container.children( 'iframe, embed, object' ).wrap( '<p>' );
	},

	/**
	 * Initialize video elements on demand from placeholders
	 *
	 * @param $container
	 */
	handleVideos: function( $container ) {
		$container = typeof $container !== "undefined" ? $container : $body;

		$container.find( '.video-placeholder' ).each( function( i, obj ) {
			var $placeholder = $( obj ),
				video = document.createElement( 'video' ),
				$video = $( video ).addClass( 'c-hero__video' );

			// play as soon as possible
			video.onloadedmetadata = function() {
				video.play();
			};

			video.src = $placeholder.data( 'src' );
			video.poster = $placeholder.data( 'poster' );
			video.muted = true;
			video.loop = true;

			$placeholder.replaceWith( $video );
		} );
	},

	smoothScrollTo: function( to, duration, easing ) {
		to = to || 0;
		duration = duration || 1000;
		easing = easing || 'swing';

		$( "html, body" ).stop().animate( {
			scrollTop: to
		}, duration, easing );

	},

	// Returns a function, that, as long as it continues to be invoked, will not
	// be triggered. The function will be called after it stops being called for
	// N milliseconds. If `immediate` is passed, trigger the function on the
	// leading edge, instead of the trailing.
	debounce: function( func, wait, immediate ) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if ( ! immediate ) {
					func.apply( context, args );
				}
			};
			var callNow = immediate && ! timeout;
			clearTimeout( timeout );
			timeout = setTimeout( later, wait );
			if ( callNow ) {
				func.apply( context, args );
			}
		};
	},

	// Returns a function, that, when invoked, will only be triggered at most once
	// during a given window of time. Normally, the throttled function will run
	// as much as it can, without ever going more than once per `wait` duration;
	// but if you'd like to disable the execution on the leading edge, pass
	// `{leading: false}`. To disable execution on the trailing edge, ditto.
	throttle: function( callback, limit ) {
		var wait = false;
		return function() {
			if ( ! wait ) {
				callback.call();
				wait = true;
				setTimeout( function() {
					wait = false;
				}, limit );
			}
		}
	},

	mq: function( direction, string ) {
		var $temp = $( '<div class="u-mq-' + direction + '-' + string + '">' ).appendTo( 'body' ),
			response = $temp.is( ':visible' );

		$temp.remove();
		return response;
	},

	below: function( string ) {
		return this.mq( 'below', string );
	},

	above: function( string ) {
		return this.mq( 'above', string );
	},

	getParamFromURL: function( param, url ) {
		var parameters = (
			url.split( '?' )
		)[1];

		if ( typeof parameters === "undefined" ) {
			return parameters;
		}

		parameters = parameters.split( '&' );

		for ( var i = 0; i < parameters.length; i ++ ) {
			var parameter = parameters[i].split( '=' );
			if ( parameter[0] === param ) {
				return parameter[1];
			}
		}
	},

	reloadScript: function( filename ) {
		var $old = $( 'script[src*="' + filename + '"]' ),
			$new = $( '<script>' ),
			src = $old.attr( 'src' );

		if ( ! $old.length ) {
			return;
		}

		$old.replaceWith( $new );
		$new.attr( 'src', src );
	},

	/**
	 * returns version of IE or false, if browser is not Internet Explorer
	 */
	getIEversion: function() {
		var ua = window.navigator.userAgent;

		var msie = ua.indexOf('MSIE ');
		if (msie > 0) {
			// IE 10 or older => return version number
			return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		}

		var trident = ua.indexOf('Trident/');
		if (trident > 0) {
			// IE 11 => return version number
			var rv = ua.indexOf('rv:');
			return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		}

		var edge = ua.indexOf('Edge/');
		if (edge > 0) {
			// Edge (IE 12+) => return version number
			return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
		}

		// other browser
		return false;
	}

};
