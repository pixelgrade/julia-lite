import * as Rx from 'rx-dom';

export interface ExtendedWindow extends Window {
  wp?: any;
  safari?: any;
}

export class GlobalService {

  public static onCustomizerRender(): Rx.Observable<JQuery> {
    const exWindow: ExtendedWindow = window;

    return Rx.Observable.create( ( observer ) => {
      if ( exWindow.wp && exWindow.wp.customize && exWindow.wp.customize.selectiveRefresh ) {
        exWindow.wp.customize.selectiveRefresh.bind( 'partial-content-rendered', (placement) => {
          observer.onNext($(placement.container));
        });
      }
    });
  }

  public static onCustomizerChange(): Rx.Observable<JQuery> {
    const exWindow: ExtendedWindow = window;

    return Rx.Observable.create( ( observer ) => {
      if ( exWindow.wp && exWindow.wp.customize ) {
        exWindow.wp.customize.bind( 'change', ( setting ) => {
          observer.onNext( setting );
        });
      }
    });
  }

  public static onReady(): Rx.Observable<UIEvent> {
    return Rx.DOM.ready();
  }

}
