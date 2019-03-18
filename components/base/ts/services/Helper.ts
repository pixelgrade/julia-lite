import $ from 'jquery';

export class Helper {

  public static $body: JQuery = $( 'body' );

  public static isTouch(): boolean {
    // return 'ontouchstart' in window || 'DocumentTouch' in window && document instanceof DocumentTouch;
    return 'ontouchstart' in window || 'DocumentTouch' in window;
  }

  public static handleCustomCSS( $container: JQuery ): void {
    const $elements = typeof $container !== 'undefined' ? $container.find( '[data-css]' ) : $( '[data-css]' );

    if ( $elements.length ) {
      $elements.each( ( index, obj ) => {
        const $element = $( obj );
        const css = $element.data( 'css' );

        if ( typeof css !== 'undefined' ) {
          $element.replaceWith( '<style type="text/css">' + css + '</style>' );
        }
      } );
    }
  }

  /**
   * Search every image that is alone in a p tag and wrap it
   * in a figure element to behave like images with captions
   *
   * @param $container
   */
  public static unwrapImages( $container: JQuery = Helper.$body ): void {

    $container
      .find( 'p > img:first-child:last-child, p > a:first-child:last-child > img' )
      .each( ( index, obj ) => {
        const $obj = $( obj );
        const $image = $obj.closest( 'img' );
        const className = $image.attr( 'class' );
        const $p = $image.closest( 'p' );
        const $figure = $( '<figure />' ).attr( 'class', className );

        console.log($figure, $p, $.trim( $p.text() ).length);

        if ( $.trim( $p.text() ).length ) {
          return;
        }

        $figure.append( $image.removeAttr( 'class' ) ).insertAfter( $p );
        $p.remove();
      } );
  }

  public static wrapEmbeds( $container: JQuery = Helper.$body ): void {
    $container.children( 'iframe, embed, object' ).wrap( '<p>' );
  }

  /**
   * Initialize video elements on demand from placeholders
   *
   * @param $container
   */
  public static handleVideos( $container: JQuery = Helper.$body ): void {
    $container
      .find( '.video-placeholder' )
      .each( ( index, obj ) => {
        const $placeholder = $( obj );
        const video = document.createElement( 'video' );
        const $video = $( video ).addClass( 'c-hero__video' );

        // play as soon as possible
        video.onloadedmetadata = () => video.play();

        video.src = $placeholder.data( 'src' );
        video.poster = $placeholder.data( 'poster' );
        video.muted = true;
        video.loop = true;

        $placeholder.replaceWith( $video );
      });
  }

  public static smoothScrollTo( to: number = 0, duration: number = 1000, easing: string = 'swing' ) {
    $( 'html, body' ).stop().animate( {
      scrollTop: to
    }, duration, easing );
  }

  // Returns a function, that, as long as it continues to be invoked, will not
  // be triggered. The function will be called after it stops being called for
  // N milliseconds. If `immediate` is passed, trigger the function on the
  // leading edge, instead of the trailing.
  public static debounce( func: () => any, wait: number, immediate: boolean ): () => void {
    let timeout;
    return () => {
      const context = this;
      const args = arguments;
      const later = () => {
        timeout = null;
        if ( ! immediate ) {
          func.apply( context, args );
        }
      };
      const callNow = immediate && ! timeout;
      clearTimeout( timeout );
      timeout = setTimeout( later, wait );
      if ( callNow ) {
        func.apply( context, args );
      }
    };
  }

  // Returns a function, that, when invoked, will only be triggered at most once
  // during a given window of time. Normally, the throttled function will run
  // as much as it can, without ever going more than once per `wait` duration;
  // but if you'd like to disable the execution on the leading edge, pass
  // `{leading: false}`. To disable execution on the trailing edge, ditto.
  public static throttle( callback: () => any, limit: number ): () => void {
    let wait = false;
    return () => {
      if ( ! wait ) {
        callback();
        wait = true;
        setTimeout( () => {
          wait = false;
        }, limit );
      }
    };
  }

  public static mq( direction: string, query: string ): boolean {
    const $temp = $( '<div class="u-mq-' + direction + '-' + query + '">' ).appendTo( 'body' );
    const response = $temp.is( ':visible' );

    $temp.remove();
    return response;
  }

  public static below( query: string ): boolean {
    return Helper.mq( 'below', query );
  }

  public static above( query: string ): boolean {
    return Helper.mq( 'above', query );
  }

  public static getParamFromURL( param: string, url: string ) {
    let parameters = (
      url.split( '?' )
    );

    if ( typeof parameters[1] === 'undefined' ) {
      return parameters[1];
    }

    parameters = parameters[1].split( '&' );

    for ( const i of Array.from( Array(parameters.length).keys() )) {
      const parameter = parameters[i].split( '=' );
      if ( parameter[0] === param ) {
        return parameter[1];
      }
    }
  }

  public static reloadScript( filename: string ): void {
    const $old = $( 'script[src*="' + filename + '"]' );
    const $new = $( '<script>' );
    const src = $old.attr( 'src' );

    if ( ! $old.length ) {
      return;
    }

    $old.replaceWith( $new );
    $new.attr( 'src', src );
  }

  /**
   * returns version of IE or false, if browser is not Internet Explorer
   */
  public static getIEversion(): number | boolean {
    const ua = window.navigator.userAgent;
    const msie = ua.indexOf( 'MSIE ');

    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    const trident = ua.indexOf('Trident/');
    if (trident > 0) {
      // IE 11 => return version number
      const rv = ua.indexOf('rv:');
      return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    const edge = ua.indexOf('Edge/');
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    // other browser
    return false;
  }

  public static markFirstWord( $el: JQuery ): void {
    const text = $el.text().trim().split( ' ' );
    const first = text.shift();
    $el.html( ( text.length > 0 ? '<span class="first-word">' + first + '</span> ' : first ) + text.join( ' ' ) );
  }

  public static fitText( $el: JQuery ) {
    const currentFontSize = parseFloat( $el.css( 'fontSize' ) );
    const currentLineHeight = parseFloat( $el.css( 'lineHeight' ) );
    const parentHeight = $el.parent().outerHeight() || 1;

    $el.css( 'fontSize', currentFontSize * parentHeight / currentLineHeight );
  }

}
