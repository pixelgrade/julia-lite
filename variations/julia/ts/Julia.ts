import $ from 'jquery';
import * as imagesLoaded from 'imagesloaded';
import * as Masonry from 'masonry-layout';
import StickySidebar from 'sticky-sidebar';
import 'select2';

import { GlobalService } from '../../../components/base/ts/services/global.service';
import { Helper } from '../../../components/base/ts/services/Helper';
import { BaseTheme, JQueryExtended } from '../../../components/base/ts/BaseTheme';
import { Carousel } from '../../../components/base/ts/components/Carousel';
import { Slideshow } from '../../../components/base/ts/components/Slideshow';
import { StickyHeader } from '../../../components/header/ts/StickyHeader';
import { SearchOverlay } from '../../../components/base/ts/components/SearchOverlay';
import { Recipe } from '../../../components/base/ts/components/Recipe';
import { Gallery } from '../../../components/base/ts/components/Gallery';

export class Julia extends BaseTheme {

  public Recipe: Recipe = new Recipe();
  public Header: StickyHeader;
  public SearchOverlay: SearchOverlay;

  private featuredCarousel: Carousel[] = [];
  private carousels: Carousel[] = [];
  private slideshows: Slideshow[] = [];
  private sidebars: StickySidebar[] = [];
  private masonrySelector: string = '.js-masonry, .u-gallery-type--masonry';

  constructor() {
    super();

    GlobalService.onCustomizerRender().subscribe( this.handleCustomizerChanges.bind(this) );

    this.handleContent();
  }

  public bindEvents() {
    super.bindEvents();
    $( document.body ).on( 'post-load', this.onJetpackPostLoad.bind(this) );
  }

  public onLoadAction() {
    super.onLoadAction();

    this.Header = new StickyHeader();
    this.SearchOverlay = new SearchOverlay();

    Object.assign(window, {StickyHeader: this.Header});

    this.adjustLayout();
    this.initCarousels();
  }

  public onResizeAction() {
    super.onResizeAction();
    this.adjustLayout();

    this.destroySlideshows();
    this.handleSlideshows();

    this.destroyCarousels();
    this.handleCarousels();

    this.initCarousels();
  }

  public onJetpackPostLoad() {
    const $container = ($( '#posts-container' ) as JQueryExtended );
    const $newBlocks = ($container
      .children()
      .not( '.post--loaded' ).addClass( 'post--loaded' ) as JQueryExtended);

    $newBlocks.imagesLoaded( () => {
      if ( $container.hasClass( 'js-masonry' ) ) {
        $container
          .masonry( 'appended', $newBlocks, true )
          .masonry( 'layout' );
        $( '.infinite-loader' ).hide();
      }
    } );

    this.handleContent( $container );
    this.adjustLayout();
  }

  public wrapTitle() {

    let $rightSideElement;

    // get featured image bounding box
    if ( this.$body.hasClass( 'entry-image--portrait' ) ) {
      $rightSideElement = $( '.entry-thumbnail' );
    } else if ( this.$body.hasClass( 'has-sidebar' ) && this.$body.hasClass( 'single-post' ) ) {
      $rightSideElement = $( '.widget-area--post' );
    } else {
      return;
    }

    if ( ! $rightSideElement.length ) {
      return;
    }

    let $string;
    let words;
    let rightSideElementBox;

    $string = $( '.entry-title' );

    // split title into words
    words = $.trim( $string.text() ).split( ' ' );

    // empty title container
    $string.empty();

    // wrap each word in a span and add it back to the title container
    // there should be a trailing space after the closing tag
    $.each( words, ( i, w ) => {
      $string.append( `<span> ${ w } </span> ` );
    } );

    rightSideElementBox = $rightSideElement[0].getBoundingClientRect();

    // loop through each of the newly created spans
    // if it overlaps the bounding box of the featured image add a new line before it
    const $reverseSpans = $($string.find( 'span' ).get().reverse());
    $reverseSpans.each( ( i, obj ) => {
      const $span = $( obj );
      const spanBox = obj.getBoundingClientRect();

      if ( spanBox.bottom > rightSideElementBox.top && spanBox.right > rightSideElementBox.left ) {
        $span.replaceWith( $( `<br><span> ${ $span.text() } </span>` ) );
        return false;
      }
    } );

    if ( Helper.above( 'small' ) ) {
      $( '.header-dropcap' ).each( ( index, element ) => {
        Helper.fitText( $( element ) );
      } );
    }
  }

  public scaleCardSeparators() {

    // loop through each card
    $( '.c-card' ).not( '.c-card--related' ).each( ( i, obj ) => {

      const $card = $( obj );
      const $meta = $card.find( '.c-meta' );
      const $separator = $card.find( '.c-meta__separator' ).hide();
      const width = $card[0].offsetWidth;
      let totalWidth = 0;

      // calculate the sum of the widths of the meta elements
      $meta.children().each( ( j, element ) => {
        totalWidth += (element as HTMLElement).offsetWidth;
      } );

      // if there are still at least 14px left, display the separator
      if ( totalWidth + 14 <= width ) {
        $separator.show();
      }
    } );

  }

  public addIsLoadedListener( $container: JQuery = this.$body ) {
    // add every image on the page the .is-loaded class
    // after the image has actually loaded
    $container
      .find( '.widget_categories_image_grid, .c-card__frame, .entry-thumbnail' )
      .find( 'img' )
      .each( ( i, element ) => {
        const $each = $( element ) as JQueryExtended;

        imagesLoaded(element, () => {
          $each.addClass( 'is-loaded' );
        });
        if ( Helper.below('pad') ) {
          $each.addClass( 'is-loaded' );
        }
      } );
  }

  public handleContent( $container: JQuery = this.$body ) {

    this.Recipe.positionPrintBtn();
    this.Recipe.wrapRecipeElements();

    Helper.unwrapImages( $container.find( '.entry-content' ) );
    Helper.wrapEmbeds( $container.find( '.entry-content' ) );
    Helper.handleVideos( $container );
    Helper.handleCustomCSS( $container );
    this.wrapTitle();

    $('.single .entry-content .tiled-gallery').wrap( '<div class="aligncenter" />');

    // $container
    //   .find( '.header-dropcap, .post:not(.has-post-thumbnail) .c-card__letter' )
    //   .each( ( index, element ) => {
    //     $( element ).css( 'opacity', 1 );
    //   } );

    this.addIsLoadedListener( $container );

    if ( $container.hasClass( 'page-template-front-page' ) ) {
      const $widgetArea = $container.find( '.content-area .widget-area' );
      const $widget = $widgetArea.children( '.widget' ).first();
      const isFullWidth = $widgetArea.is( '.o-layout__full' );
      const isProperWidget = $widget
        .is( '.widget_featured_posts_5cards, .widget_featured_posts_6cards, .widget_featured_posts_grid' );
      const hasTitle = $widget.children( '.widget__title' ).length > 0;

      if ( isFullWidth && isProperWidget && ! hasTitle ) {
        this.$body.addClass( 'has-extended-header-background' );
      }
    }

    if (
      $container.hasClass( 'blog' ) &&
      ! $container.hasClass( 'u-site-header-short' ) &&
      ! $( '.o-layout__side' ).length
    ) {
      this.$body.addClass( 'has-extended-header-background' );
    }

    $container.find( '.entry-content p' ).each((i, obj) => {
      const $p = $( obj );

      if ( ! $p.children().length && ! $.trim( $p.text() ).length ) {
        $p.remove();
      }
    });

    const $commentForm = $( '.comment-form' );
    if ( $commentForm.length ) {
      const $commentFormFooter = $( '<p class="comment-form-subscriptions"></p>' )
        .appendTo( $commentForm );
      $( '.comment-subscription-form' ).appendTo( $commentFormFooter );
    }

    $container.find( '.c-gallery' ).not( '.c-gallery--widget, .c-footer__gallery' ).each((index, element) => {
        new Gallery( $( element ) );
    });

    ( $container.find( '.widget_categories select' ) as JQueryExtended ).select2();

    this.handleCarousels();
    this.handleSlideshows();

    imagesLoaded( $container, this.initStickyWidget.bind(this) );

    this.eventHandlers( $container );
  }

  public destroyCarousels() {
    this.carousels.forEach( ( carousel ) => {
      carousel.destroy();
    });
    this.carousels = [];
  }

  public destroySlideshows() {
    this.slideshows.forEach( ( slideshow ) => {
      slideshow.destroy();
    });
    this.slideshows = [];
  }

  public handleCarousels() {
    this.getFeaturedPostsCarousels()
      .forEach( ( carousel ) => {
        const $carousel = $( carousel );
        this.carousels.push( new Carousel( $carousel, $carousel.data() ) );
      });
  }

  public handleSlideshows() {
    const blendedSelector: string = '.blend-with-header';
    const slideshowWidgetSelector: string = '.widget_featured_posts_slideshow';
    const headerBlendedClass: string = 'site-header--inverted';
    const $slideshow = $( '.featured-posts-slideshow' );
    const $siteHeader: JQuery = $( '.site-header' );

    const $blended = $slideshow.filter( blendedSelector ).first();

    if ( $blended.length ) {

      if ( Helper.above( 'lap' ) ) {
        const $widget = $blended.closest( slideshowWidgetSelector );
        const $placeholder = $( '<div class="js-slideshow-placeholder">' );

        $widget.data( 'placeholder', $placeholder );
        $placeholder.insertAfter( $widget );
        $widget.appendTo( $siteHeader );
        $siteHeader.addClass( headerBlendedClass );
      } else {
        $siteHeader.find( slideshowWidgetSelector ).each((i, obj) => {
          const $widget = $( obj );
          const $placeholder = $widget.data( 'placeholder' );
          $placeholder.replaceWith( $widget );
        });
        $siteHeader.removeClass( headerBlendedClass );
      }
    }

    $slideshow.each((i, obj) => {
      const $element = $(obj);
      this.slideshows.push( new Slideshow( $element.find( '.c-hero__slider' ), $element.data() ) );
    });

  }

  public positionSidebar() {

    if ( this.$body.is( '.entry-image--portrait' ) ) {

      const $sidebar = $( '.widget-area--post' );
      const $container = $sidebar.parent();
      let containerOffset;
      let sidebarHeight;
      let sidebarOffset;
      let sidebarBottom;

      if ( ! $container.length || ! $sidebar.length ) {
        return;
      }

      // remove possible properties set on prior calls of this function
      $container.css( {
        minHeight: '',
        position: ''
      } );

      $sidebar.css( {
        bottom: '',
        position: '',
        right: '',
        top: '',
      } );

      if ( Helper.below( 'pad' ) ) {
        return;
      }

      containerOffset = $container.offset();
      sidebarHeight = $sidebar.outerHeight();
      sidebarOffset = $sidebar.offset();

      sidebarBottom = $container.outerHeight() > sidebarHeight ? 0 : '';

      $container.css( {
        minHeight: sidebarHeight + sidebarOffset.top - containerOffset.top,
        position: 'relative'
      } );

      $sidebar.css( {
        bottom: sidebarBottom,
        position: 'absolute',
        right: 0,
        top: sidebarOffset.top - containerOffset.top,
      } );

    }
  }

  private getFeaturedPostsCarousels() {
    return [ ...$( '.featured-posts-carousel' ) ];
  }

  private handleCustomizerChanges( element: JQuery ) {
    this.addIsLoadedListener();

    if (element.hasClass('widget_featured_posts_slideshow')) {
      this.handleSlideshowsReload();
    }

    if (element.find('.featured-posts-carousel').length) {
      this.handleCarouselsReload();
    }

    if ( element['selector'] === '' ) {
      this.handleSlideshowsReload();
    }
  }

  private handleCarouselsReload() {
    this.destroyCarousels();
    this.handleCarousels();
  }

  private handleSlideshowsReload() {
    this.destroySlideshows();
    this.handleSlideshows();
  }

  private initStickyWidget() {
    const sidebars = $('.widget-area--side');

    imagesLoaded(sidebars, () => {

      this.positionSidebar();

      sidebars.each( (index, sidebar) => {
        const lastWidget = $(sidebar).find('.widget').last();

        if ( lastWidget.length === 0 ) {
          return;
        }

        lastWidget
          .wrap('<div class="sticky-sidebar"></div>')
          .wrap('<div class="sticky-sidebar__inner"></div>');

        const headerHeight = $( '.u-site-header-sticky .site-header-sticky' ).outerHeight() || 0;
        const adminBarHeight = $( '#wpadminbar' ).outerHeight() || 0;
        const margin = parseInt( lastWidget.find('sticky-sidebar' ).css('marginTop'), 10 ) || 56;
        const offset = headerHeight + adminBarHeight + margin;

        this.sidebars.push( new StickySidebar( $( sidebar ).find('.sticky-sidebar__inner')[0], {
          bottomSpacing: margin,
          containerSelector: '.sticky-sidebar',
          innerWrapperSelector: '.widget',
          topSpacing: offset
        }));
      });
    });
  }

  private changeHeaderDropcapOpacity() {
    if ( Helper.above( 'small' ) ) {
      $( '.header-dropcap, .post:not(.has-post-thumbnail) .c-card__letter' ).each( (index, element) => {
        $( element ).css( 'opacity', 1 );
      });
    }
  }

  private adjustLayout() {
    this.applyMasonryOnGallery();
    this.changeHeaderDropcapOpacity();

    this.wrapTitle();
    this.scaleCardSeparators();

    // If the branding happens to be in the Left Zone (no Top Menu set), move it in the middle zone
    const $headerNav = $( '.header.nav' );
    if ( $headerNav.parent().hasClass( 'c-navbar__zone--left' ) ) {
      $headerNav.appendTo( '.c-navbar__zone--middle' );
    }

    // If the Top Menu is not present, ensure that items in the left zone are aligned to the right
    if ( $( '.menu--secondary' ).length === 0 ) {
      $( '.c-navbar__zone--left' ).addClass( 'u-justify-end' );
    }

    this.wrapContentImages();
    setTimeout(this.modifyHeaderDropcap.bind(this), 100);
    // this.modifyHeaderDropcap();
  }

  private applyMasonryOnGallery() {
    const $gallery = $( this.masonrySelector );

    $gallery.each( ( i, obj ) => {
      const $obj = $( obj ) as JQueryExtended;
      $obj.children().addClass( 'post--loaded' );

      imagesLoaded( $obj, () => {
        new Masonry( $obj.get(0), { transitionDuration: 0 } );
      });
    } );
  }

  private wrapContentImages() {
    $( '.entry-content' )
      .find( 'figure' )
      .filter( '.aligncenter, .alignnone' )
      .each( ( index, element ) => {
        const $figure = $( element );
        const $image = $figure.find( 'img' );
        const figureWidth = $figure.outerWidth();
        const imageWidth = $image.outerWidth();

        if ( imageWidth < figureWidth ) {
          $figure.wrap( '<p>' );
        }
      } );
  }

  private modifyHeaderDropcap() {
    $( '.header-dropcap, .c-card__letter' ).each( ( index, element ) => {
      Helper.fitText( $( element ) );
    } );
  }

  private initCarousels() {
    if ( this.featuredCarousel.length > 0 ) {
      return;
    }

    if ( Helper.above( 'pad' ) ) {
      return;
    }

    this.getXCardsCarousels()
      .forEach( ( element ) => {
        this.initXCardsCarousel( $(element) );
      });

    this.getSlideshows()
      .forEach( ( element ) => {
        this.initSlideshowCarousel( $(element) );
      });
  }

  private initSlideshowCarousel( $element: JQueryExtended ) {
    const $slides = $element.find( '.c-hero__slide' );
    const $elementClone = $element.clone().empty().removeAttr('id').addClass('featured-posts-cards--mobile' );
    let newHTML = '';

    $slides.each((i, obj) => {
      const $slide = $(obj);
      const $image = $slide.find( '.c-hero__image' ).first();
      const $meta = $slide.find( '.c-meta' );
      const title = $slide.find('.c-hero__title-mask h2' ).text();
      const $excerpt = $slide.find( '.c-hero__excerpt' ).html();
      const link = $slide.find( '.c-hero__link' ).attr( 'href' );

      const $cardImage = $image.clone().removeClass( 'c-hero__image' );
      const $cardFrame =  $( '<div class="c-card__frame">' );
      const $cardLetter = $( '<div class="c-card__letter">' + title.charAt(0) + '</div>' );
      const $cardAside = $( '<div class="c-card__aside c-card__thumbnail-background"></div>');
      const $cardMeta = $( '<div class="c-card__meta">' ).append( $meta );
      const $cardTitle = $( '<div class="c-card__title"><span>' + title + '</span></div>');
      const $cardExcerpt = $( '<div class="c-card__excerpt">' ).append( $excerpt );
      const $cardLink = $( '<a class="c-card__link" href="' + link + '"></a>');

      const $card = $( '<div class="c-card"></div>' );
      const $cardContent = $( '<div class="c-card__content"></div>' );
      $cardFrame.append( $cardImage, $cardLetter );
      $cardAside.append( $cardFrame );
      $cardContent.append( $cardMeta, $cardTitle, $cardExcerpt );
      $card.append( $cardAside, $cardContent, $cardLink );

      newHTML += $card.wrap( '<article>' ).parent().prop('outerHTML');
    });

    $elementClone.html( newHTML ).insertAfter( $element );

    this.featuredCarousel.push(new Carousel( $elementClone, { show_pagination: '' } ));
  }

  private initXCardsCarousel( $element: JQueryExtended ) {
    const $articles = [
      ...$element.find('.posts-wrapper--main').find('article').clone(),
      ...$element.find('.posts-wrapper--left').find('article').clone(),
      ...$element.find('.posts-wrapper--right').find('article').clone()
    ];

    const $elementClone = $element.clone().empty().append( $articles ).addClass('featured-posts-cards--mobile');
    $element.addClass('featured-posts-cards--desktop');
    $element.parent().append( $elementClone );

    this.featuredCarousel.push(new Carousel( $elementClone, { show_pagination: '' } ));
  }

  private getSlideshows() {
    return [
      ...$( '.widget_featured_posts_slideshow' ),
    ];
  }

  private getXCardsCarousels() {
    return [
      ...$( '.featured-posts-5cards' ),
      ...$( '.featured-posts-6cards' ),
    ];
  }
}
