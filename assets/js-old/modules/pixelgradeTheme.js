/*!
 * pixelgradeTheme v1.0.1
 * Copyright (c) 2017 PixelGrade http://www.pixelgrade.com
 * Licensed under MIT http://www.opensource.org/licenses/mit-license.php/
 */
var pixelgradeTheme = function() {

	var _this = this,
		windowWidth = window.innerWidth,
		windowHeight = window.innerHeight,
		lastScrollY = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0),
		orientation = windowWidth > windowHeight ? 'landscape' : 'portrait';

	_this.ev = $( {} );
	_this.frameRendered = false;
	_this.debug = false;

	_this.log = function() {
		if ( _this.debug ) {
			console.log.apply(this, arguments)
		}
	};

	_this.getScroll = function() {
		return lastScrollY;
	};

	_this.getWindowWidth = function() {
		return windowWidth;
	};

	_this.getWindowHeight = function() {
		return windowHeight;
	};

	_this.getOrientation = function() {
		return orientation;
	};

	_this.onScroll = function() {
		if ( _this.frameRendered === false ) {
			return;
		}
		lastScrollY = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0);
		_this.frameRendered = false;
	};

	_this.onResize = function() {
		windowWidth = window.innerWidth;
		windowHeight = window.innerHeight;

		var newOrientation = windowWidth > windowHeight ? 'landscape' : 'portrait';

		_this.debouncedResize();

		if ( orientation !== newOrientation ) {
			_this.debouncedOrientationChange();
		}

		orientation = newOrientation;
	};

	_this.debouncedResize = Util.debounce(function() {
		$( window ).trigger( 'pxg:resize' );
	}, 300);

	_this.debouncedOrientationChange = Util.debounce(function() {
		$( window ).trigger( 'pxg:orientationchange' );
	}, 300);

	_this.renderLoop = function() {
		if ( _this.frameRendered === false ) {
			_this.ev.trigger( 'render' );
		}
		requestAnimationFrame( function() {
			_this.renderLoop();
			_this.frameRendered = true;
			_this.ev.trigger( 'afterRender' );
		} );
	};

	_this.eventHandlers = function() {
		$( document ).ready( _this.onReady );
		$( window )
		.on( 'scroll', _this.onScroll )
		.on( 'resize', _this.onResize )
		.on( 'load', _this.onLoad );
	};

	_this.eventHandlers();
	_this.renderLoop();
};

pixelgradeTheme.prototype.onReady = function() {
	$( 'html' ).addClass( 'is-ready' );
};

pixelgradeTheme.prototype.onLoad = function() {
	$( 'html' ).addClass( 'is-loaded' );
};
