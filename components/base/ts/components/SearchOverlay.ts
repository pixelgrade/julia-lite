import $ from 'jquery';
import * as Rx from 'rx-dom';
import { BaseComponent } from '../models/DefaultComponent';
import Observable = Rx.Observable;

const activeClass = 'show-search-overlay';
const openClass = '.js-search-trigger';
const closeClass = '.js-search-close';
const escKeyCode = 27;

export class SearchOverlay extends BaseComponent {

  private $body: JQuery = $( 'body' );
  private $document: JQuery = $( document );
  private $searchField: JQuery = $( '.c-search-overlay' ).find( '.search-field' );
  private closeSub: Observable<Event>;
  private keyupSub: Observable<Event>;
  private subscriptionActive: boolean = true;
  private keyupSubscriptionActive: boolean = true;

  constructor() {
    super();
    this.bindEvents();
  }

  public destroy() {
    this.subscriptionActive = false;
    this.keyupSubscriptionActive = false;
    this.$document.off( 'click.SearchOverlay' );
  }

  public bindEvents() {
    this.$document.on( 'click.SearchOverlay', openClass, this.open.bind( this ) );

    this.closeSub = Rx.DOM.click(document.querySelector(closeClass));
    this.keyupSub = Rx.DOM.keyup(document.querySelector('body' ));

    this.closeSub
        .takeWhile( () => this.subscriptionActive )
        .subscribe( this.close.bind( this ) );
  }

  public createKeyupSubscription() {
    this.keyupSubscriptionActive = true;
    this.keyupSub
        .takeWhile( () => this.keyupSubscriptionActive )
        .subscribe( this.closeOnEsc.bind( this ) );
  }

  public open() {
    this.$searchField.focus();
    this.$body.addClass( activeClass );

    this.createKeyupSubscription();
  }

  public close() {
    this.$body.removeClass( activeClass );
    this.$searchField.blur();
    this.keyupSubscriptionActive = false;
  }

  private closeOnEsc( e: JQuery.Event ) {
    if ( e.keyCode === escKeyCode ) {
      this.close();
    }
  }
}
