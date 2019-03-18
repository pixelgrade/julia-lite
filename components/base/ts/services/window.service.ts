import * as Rx from 'rx-dom';
import $ from 'jquery';

export class WindowService {

  private static $window: JQuery = $( window );

  public static onLoad(): Rx.Observable<UIEvent> {
    return Rx.DOM.fromEvent( this.getWindowEl(), 'load' );
  }

  public static onResize(): Rx.Observable<UIEvent> {
    return Rx.DOM.resize( this.getWindowEl() );
  }

  public static onScroll(): Rx.Observable<UIEvent> {
    return Rx.DOM.scroll( this.getWindowEl() );
  }

  public static getWindow(): JQuery {
    return WindowService.$window;
  }

  public static getScrollY() {
    return (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0);
  }

  public static getWidth(): number {
    return WindowService.$window.width();
  }

  public static getHeight(): number {
    return WindowService.$window.height();
  }

  public static getWindowEl(): Element {
    return WindowService.$window[ 0 ];
  }

  public static getOrientation(): string {
    return WindowService.getWidth() > WindowService.getHeight() ? 'landscape' : 'portrait';
  }
}
