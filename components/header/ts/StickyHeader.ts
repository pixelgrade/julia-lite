import $ from 'jquery';
import * as imagesLoaded from 'imagesloaded';
import 'jquery-hoverintent';
import { BaseComponent } from '../../base/ts/models/DefaultComponent';
import { ProgressBar } from '../../base/ts/components/ProgressBar';
import { WindowService } from '../../base/ts/services/window.service';
import { Helper } from '../../base/ts/services/Helper';

interface JQueryExtended extends JQuery {
  hoverIntent?( params: any ): void;
  imagesLoaded?( params: any );
}

export class StickyHeader extends BaseComponent {

  private ProgressBar: ProgressBar;
  private $body: JQuery = $( 'body' );
  private $document: JQuery = $( document );
  private $mainMenu: JQuery = $( '.menu--primary' );
  private $mainMenuItems: JQueryExtended = this.$mainMenu.find( 'li' );
  private $readingBar: JQuery = $( '.js-reading-bar' );
  private $stickyHeader: JQuery = $( '.js-site-header-sticky' );
  private $menuToggle: JQuery = $( '#menu-toggle' );
  private $searchTrigger: JQuery;
  private isStickyHeaderEnabled: boolean = this.$body.hasClass( 'u-site-header-sticky' );
  private isSingular: boolean = this.$body.hasClass( 'single' );
  private isMobileHeaderInitialised: boolean = false;
  private isDesktopHeaderInitialised: boolean = false;
  private areMobileBindingsDone: boolean = false;
  private stickyHeaderShown: boolean = false;
  private hideReadingBar: boolean = this.$readingBar !== null;
  private currentScrollPosition: number = 0;
  private initialMenuOffset: number = 0;
  private shouldUpdate: boolean;
  private overflowTimeout;
  private subscriptionActive: boolean = true;
  private preventOneSelector: string = 'a.prevent-one';

  constructor() {
    super();

    imagesLoaded( $( '.c-navbar .c-logo' ), () => {

      this.bindEvents();
      this.eventHandlers();
      this.appendSearchTrigger();
      this.updateOnResize();

      this.refresh(WindowService.getScrollY());

    });
  }

  public destroy() {
    this.subscriptionActive = false;
  }

  public bindEvents() {
    if ( this.$mainMenu.length === 1 ) {
      this.$document.on( 'click', '.js-sticky-menu-trigger', this.onClickStickyMenu.bind( this ) );
    }

    this.$menuToggle.on( 'change', this.onMenuToggleChange.bind(this));

    this.$mainMenuItems.hoverIntent( {
      out: (e) => this.toggleSubMenu(e, false),
      over: (e) => this.toggleSubMenu(e, true),
      timeout: 300
    } );

    WindowService
      .onScroll()
      .takeWhile( () => this.subscriptionActive )
      .map(() => WindowService.getScrollY())
      .subscribe( (scrollPosition) => {
        this.refresh( scrollPosition );
      } );

    WindowService
      .onResize()
      .takeWhile( () => this.subscriptionActive )
      .subscribe( () => {
        this.updateOnResize();
      } );

  }

  public eventHandlers() {
    if ( Helper.below( 'lap' ) && !this.areMobileBindingsDone ) {
      this.$document.on( 'click', this.preventOneSelector, this.onMobileMenuExpand.bind(this) );
      this.areMobileBindingsDone = true;
    }

    if ( Helper.above( 'lap' ) && this.areMobileBindingsDone ) {
      this.$document.off( 'click', this.preventOneSelector, this.onMobileMenuExpand.bind(this) );
      this.areMobileBindingsDone = false;
    }
  }

  private onMobileMenuExpand(e: JQuery.Event): void {
    e.preventDefault();
    e.stopPropagation();

    const $button = $( e.currentTarget );
    const activeClass = 'active';
    const hoverClass = 'hover';

    if ( $button.hasClass( activeClass ) ) {
      window.location.href = $button.attr( 'href' );
      return;
    }

    $( this.preventOneSelector ).removeClass( activeClass );
    $button.addClass( activeClass );

    // When a parent menu item is activated,
    // close other menu items on the same level
    $button.parent().siblings().removeClass( hoverClass );

    // Open the sub menu of this parent item
    $button.parent().addClass( hoverClass );
  }

  private onMenuToggleChange( e: JQuery.Event ): void {
    const isMenuOpen = $( e.currentTarget ).prop( 'checked' );
    this.$body.toggleClass( 'nav--is-open', isMenuOpen );
    if ( !isMenuOpen ) {
      setTimeout( () => {
        // Close the open submenus in the mobile menu overlay
        this.$mainMenuItems.removeClass( 'hover' );
        this.$mainMenuItems.find('a').removeClass( 'active' );
      }, 300 );
    }
  }

  private toggleSubMenu(e: JQuery.Event, toggle: boolean) {
    $( e.currentTarget ).toggleClass( 'hover', toggle );
  }

  private refresh( scrollPosition: number = 0 ) {
    this.shouldUpdate = this.$body.is( '.u-site-header-sticky' );

    if ( Helper.below( 'lap' ) ) {
      this.shouldUpdate = false;
    }

    this.updateOnScroll( scrollPosition );
  }

  private prepareDesktopMenuMarkup(): void {
    if ( this.isDesktopHeaderInitialised ) {
      return;
    }

    const htmlTop = parseInt( $( 'html' ).css( 'marginTop' ), 10 );

    this.$stickyHeader.css( 'top', htmlTop );

    // Figure out where is the offset of the Main Menu.
    // If there is no Main Menu set, show the reading bar
    // after passing the branding.
    if ( this.$mainMenu.length === 1 ) {
      this.initialMenuOffset = this.$mainMenu.offset().top - htmlTop;
    } else {
      const $branding = $( '.c-branding' );
      this.initialMenuOffset = $branding.offset().top + $branding.outerHeight();
    }

    // Fallback to the other, secondary menu (top left one).
    if ( this.$mainMenu.length === 0 ) {
      this.$mainMenu = $( '.menu--secondary' );
    }

    // If there is a menu, either the "true" main one or the fallback one,
    // clone it and append it to the reading bar.
    if ( this.$mainMenu.length === 1 ) {
      this.$mainMenu = this.$mainMenu
        .clone( true, true )
        .appendTo( this.$stickyHeader.find( '.c-navbar' ) );
    }

    this.$stickyHeader
        .find( '.c-navbar' )
        .css( 'height', this.$stickyHeader.height() );

    // this.$readingBar = null;
    // this.$progressBar = null;

    this.prepareSingleHeader();

    this.refresh();

    this.isDesktopHeaderInitialised = true;
  }

  private prepareMobileMenuMarkup() {
    // If if has not been done yet, prepare the mark-up for the mobile navigation
    if ( !this.isMobileHeaderInitialised ) {

      // Append the branding
      const $branding = $( '.c-branding' );
      const $navbarZone = $( '.c-navbar__zone--right' );
      $branding.clone().addClass('c-branding--mobile').appendTo( '.c-navbar' );
      $branding.find( 'img' ).removeClass( 'is--loading' );

      // Create the mobile site header
      const $siteHeaderMobile = $( '<div class="site-header-mobile u-container-sides-spacing"></div>' )
        .appendTo( '.c-navbar' );

      // Append the social menu
      const $socialMenu = $( '.c-navbar__zone--left .jetpack-social-navigation' ).clone();
      const $searchTrigger = $socialMenu.find('.js-search-trigger').parent().clone();
      $navbarZone.append( $socialMenu );
      $navbarZone.find('.js-search-trigger' ).parent().remove();
      $siteHeaderMobile.append( $socialMenu.empty().append( $searchTrigger ) );

      // Handle sub menus:
      // Make sure there are no open menu items
      $( '.menu-item-has-children' ).removeClass( 'hover' );

      // Add a class so we know the items to handle
      $( '.menu-item-has-children > a' ).each( ( index, element ) => {
        $( element ).addClass( 'prevent-one' );
      } );

      // Replace the label text and make it visible
      $( '.c-navbar__label-text ' ).html( $( '.js-menu-mobile-label' ).html() ).removeClass( 'screen-reader-text' );

      this.isMobileHeaderInitialised = true;
    }
  }

  private prepareSingleHeader(): void {
    if ( !this.isSingular || !this.isStickyHeaderEnabled ) {
      return;
    }

    $( '.c-reading-bar__wrapper-social' ).find( '.share-end' ).remove();

    const entryHeader = $( '.entry-header' );
    const entryContent = $( '.single-main' ).find( '.entry-content' );
    const entryHeaderHeight = entryHeader.outerHeight() || 0;
    const entryContentHeight = entryContent.outerHeight() || 0;
    let articleHeight = entryHeaderHeight + entryContentHeight;

    if ( this.$body.hasClass( 'entry-image--landscape' ) ) {
      articleHeight = articleHeight + $( '.entry-thumbnail' ).outerHeight();
    }

    this.ProgressBar = new ProgressBar({
      canShow: this.isSingular,
      max: entryHeader.offset().top + articleHeight - this.initialMenuOffset,
      offset: this.initialMenuOffset
    });

    if ( this.$mainMenu.length !== 1 ) {
      $( '.js-sticky-menu-trigger' ).remove();
    }

  }

  private updateOnScroll( scrollPosition: number = 0 ) {

    if ( !this.shouldUpdate ) {
      return;
    }

    const showSticky = scrollPosition > this.initialMenuOffset;
    const hideReadingBar = scrollPosition < this.currentScrollPosition && this.$mainMenu.length === 1;

    if ( this.$readingBar !== null && hideReadingBar !== this.hideReadingBar ) {
      clearTimeout( this.overflowTimeout );

      if ( !hideReadingBar ) {
        if ( this.$readingBar.length ) {
          this.$stickyHeader.css( 'overflow', 'hidden' );
        }
      } else {
        this.overflowTimeout = setTimeout( () => {
          this.$stickyHeader.css( 'overflow', '' );
        }, 350 );
      }

      this.$stickyHeader.toggleClass( 'reading-bar--hide', hideReadingBar );
      this.hideReadingBar = hideReadingBar;
    }

    if ( this.ProgressBar && null !== this.$readingBar ) {
      this.$readingBar.toggleClass( 'show-next-title', this.ProgressBar.isCloseToEnd() );
    }

    if ( showSticky !== this.stickyHeaderShown ) {
      this.$stickyHeader.toggleClass( 'site-header-sticky--show', showSticky );
      this.stickyHeaderShown = showSticky;
    }

    this.currentScrollPosition = scrollPosition;
  }

  private updateOnResize() {
    this.eventHandlers();

    if ( Helper.below( 'lap' ) ) {
      this.prepareMobileMenuMarkup();
    } else {
      this.prepareDesktopMenuMarkup();
    }
  }

  private onClickStickyMenu(): void {
    this.$stickyHeader.addClass( 'reading-bar--hide' );
    setTimeout( () => {
      this.$stickyHeader.css( 'overflow', '' );
    }, 350 );
  }

  private appendSearchTrigger() {
    const $headerSocialNavigation = $( '.c-navbar__zone--left .jetpack-social-navigation' );

    this.$searchTrigger = $( '.js-search-trigger' ).removeClass( 'u-hidden' );

    // Append the search trigger either to the social navigation
    if ( $headerSocialNavigation.length === 1 ) {
      this.$searchTrigger
          .clone()
          .wrap( '<li class="menu-item"></li>' )
          .parent()
          .appendTo( $headerSocialNavigation.find( '.menu' ) );
    } else {
      // Or directly to zone left if there is no social navigation
      this.$searchTrigger.clone().appendTo( $( '.c-navbar__zone--left' ) );
    }

    this.$searchTrigger.clone().appendTo( $( '.site-header-sticky .c-navbar' ) );

    this.$searchTrigger.remove();
  }
}
