/*!
 * jQuery Rellax Plugin v0.3.6.4
 * Examples and documentation at http://pixelgrade.github.io/rellax/
 * Copyright (c) 2016 PixelGrade http://www.pixelgrade.com
 * Licensed under MIT http://www.opensource.org/licenses/mit-license.php/
 */
;(
	function( $, window, document, undefined ) {

		if ( ! window.requestAnimationFrame ) {
			return;
		}

		function Rellax( element, options ) {
			this.$el = $( element );
			this.ready = false;
			this.options = $.extend( $.fn.rellax.defaults, options );
			this.$parent = this.$el.parent().closest( this.options.container );
			this.parent = this.$parent.data( "plugin_" + Rellax );

			var $el = this.$el,
				amount = $el.data( 'rellax-amount' ),
				bleed = $el.data( 'rellax-bleed' ),
				fill = $el.data( 'rellax-fill' ),
				scale = $el.data( 'rellax-scale' );

			this.options.amount = amount !== undefined ? parseFloat( amount ) : this.options.amount;
			this.options.bleed = bleed !== undefined ? parseFloat( bleed ) : this.options.bleed;
			this.options.scale = scale !== undefined ? parseFloat( scale ) : this.options.scale;
			this.options.fill = fill !== undefined;

			if ( this.options.amount == 0 ) {
				return;
			}

			elements.push( this );
		}

		$.extend( Rellax.prototype, {
			constructor: Rellax,
			_resetElement: function() {
				this.$el.css({
					position: '',
					top: '',
					left: '',
					width: '',
					height: '',
					transform: ''
				});
			},
			_reloadElement: function() {
				this.$el.css({
					position: '',
					top: '',
					left: '',
					width: '',
					height: ''
				});
				this.offset = this.$el.offset();
				this.height = this.$el.outerHeight();
				this.width = this.$el.outerWidth();

				if ( this.parent === undefined ) {
					this.offset.top -= this.options.bleed;
					this.height += 2 * this.options.bleed;
				}

				this.ready = true;
			},
			_scaleElement: function() {
				var parentHeight = this.$parent.outerHeight(),
					parentWidth = this.$parent.outerWidth(),
					scaleY = ( parentHeight + ( windowHeight - parentHeight ) * ( 1 - this.options.amount ) ) / this.height,
					scaleX = parentWidth / this.width,
					scale = Math.max(scaleX, scaleY);

				this.width = this.width * scale;
				this.height = this.height * scale;

				this.offset.top = ( parentHeight - this.height ) / 2;
				this.offset.left = ( parentWidth - this.width ) / 2;
			},
			_prepareElement: function() {
				if ( this.parent === undefined ) {
					this.$el.addClass( 'rellax-element' );
					this.$el.css({
						position: 'fixed',
						top: this.offset.top,
						left: this.offset.left,
						width: this.width,
						height: this.height
					});
				} else {
					this._scaleElement();
					this.$el.css({
						position: 'absolute',
						top: this.offset.top,
						left: this.offset.left,
						width: this.width,
						height: this.height
					});
				}
			},
			_setParentHeight: function() {
				if ( this.parent == undefined ) {
					var $parent = this.$el.parent(),
						parentHeight = $parent.css( 'minHeight', '' ).outerHeight();

					parentHeight = windowHeight < parentHeight ? windowHeight : parentHeight;
					$parent.css( 'minHeight', parentHeight );
				}
			},
			_updatePosition: function( forced ) {

				if ( this.ready !== true ) return;

				var progress = this._getProgress(),
					height = this.parent !== undefined ? this.parent.height : this.height,
					move = ( windowHeight + height ) * ( progress - 0.5 ) * this.options.amount,
					scale = 1 + ( this.options.scale - 1 ) * progress,
					scaleTransform = scale >= 1 ? 'scale(' + scale + ')' : '';

				if ( this.parent === undefined ) {
					move *= -1;
				}

				if ( forced !== true && ( progress < 0 || progress > 1 ) ) {
					this.$el.addClass( 'rellax-hidden' );
					return;
				}

				this.$el.removeClass( 'rellax-hidden' );

				this.$el.data( 'progress', progress );

				if ( this.$el.is( this.options.container ) ) {
					this.$el.css( 'transform', 'translate3d(0,' + ( - lastScrollY ) + 'px,0)' );
				} else {
					this.$el.css( 'transform', 'translate3d(0,' + move + 'px,0) ' + scaleTransform );
				}
			},
			_getProgress: function() {
				if ( this.parent !== undefined ) {
					return parseFloat( this.$parent.data( 'progress' ) );
				} else {
					return ( ( lastScrollY - this.offset.top + windowHeight ) / ( windowHeight + this.height ) );
				}
			}
		} );

		$.fn.rellax = function( options ) {
			return this.each( function() {
				var element = $.data( this, "plugin_" + Rellax ),
					idx;

				if ( typeof options !== "string" && typeof element === "undefined" ) {
					$.data( this, "plugin_" + Rellax, new Rellax( this, options ) );
				} else {
					if ( options === "destroy" ) {
						idx = elements.indexOf( element );
						if ( idx > -1 ) {
							elements.splice( idx, 1 );
						}
					}
				}
			} );
		};

		$.fn.rellax.defaults = {
			amount: 0.5,
			bleed: 0,
			scale: 1,
			container: "[data-rellax-container]"
		};

		var $window = $( window ),
			windowWidth = window.screen.width || window.innerWidth,
			windowHeight = window.screen.height || window.innerHeight ,
			lastScrollY = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0),
			frameRendered = true,
			elements = [];

		function render() {
			if ( frameRendered !== true ) {
				updateAll();
			}
			window.requestAnimationFrame( render );
			frameRendered = true;
		}

		function updateAll( forced ) {
			$.each(elements, function(i, element) {
				element._updatePosition( forced );
			});
		}

		function resetAll() {
			$.each(elements, function(i, element) {
				element._resetElement();
			});
		}

		function reloadAll() {
			$.each(elements, function(i, element) {
				element._reloadElement();
			});
		}

		function prepareAll() {
			$.each(elements, function(i, element) {
				element._prepareElement();
			});
		}

		function setHeights() {
			$.each(elements, function(i, element) {
				element._setParentHeight();
			});
		}

		function badRestart() {
			windowWidth = window.innerWidth;
			windowHeight = window.innerHeight;
			setHeights();
			resetAll();
			reloadAll();
			prepareAll();
			updateAll( true );
			$( window ).trigger( 'rellax:restart' );
		}


		function debounce(func, wait, immediate) {
			var timeout;
			return function() {
				var context = this, args = arguments;
				var later = function() {
					timeout = null;
					if (!immediate) func.apply(context, args);
				};
				var callNow = immediate && !timeout;
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
				if (callNow) func.apply(context, args);
			};
		}

		function bindEvents() {
			var restart = debounce( badRestart, 300, true );

			$( function() {
				restart();
				render();
			} );

			$window.on( 'scroll', function() {
				if ( frameRendered === true ) {
					lastScrollY = (window.pageYOffset || document.documentElement.scrollTop) - (document.documentElement.clientTop || 0);
				}
				frameRendered = false;
			});

			$window.on( 'rellax', restart );
		}

		bindEvents();
	}
)( jQuery, window, document );
