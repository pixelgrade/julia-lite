import $ from 'jquery';
import 'slick-carousel';
import { Carousel, CarouselOptions } from './Carousel';
import { TimelineMax, Quart } from 'gsap';

export class Slideshow extends Carousel {

  constructor( element: JQuery, options: CarouselOptions = {} ) {
    super( element, options );
  }

  public destroy() {
    super.destroy();
    this.element.off('beforeChange');
  }

  public bindEvents() {
    this.element.on( 'beforeChange', this.onBeforeSlideChange.bind( this ) );
    this.slickOptions = Object.assign({}, this.slickOptions, {
      fade: true,
      infinite: true,
      speed: 1000,
    });
    this.element.slick( this.slickOptions );
  }

  public onResize(): void {
    console.warn('slideshow:resize');
    this.destroy();
    this.extendOptions();
    this.bindEvents();
  }

  private onBeforeSlideChange( event, slick, currentSlide, nextSlide ) {
    const $currentSlide: JQuery = $( slick.$slides[currentSlide] );
    const $nextSlide: JQuery = $( slick.$slides[nextSlide] );

    $( slick.$slides ).css( 'zIndex', 800 );

    this.transition( $currentSlide, $nextSlide, this.getDirection( slick, currentSlide, nextSlide ) );
  }

  private transition( $current, $next, sign: number = 1 ) {
    const timeline = new TimelineMax( {paused: true} );
    const duration = this.slickOptions.speed / 1000;
    const slideWidth = $current.outerWidth();
    const move = 300;

    timeline.fromTo( $next, duration, { x: sign * slideWidth }, { x: 0, ease: Quart.easeInOut }, 0 );
    timeline.fromTo( $next.find( '.c-hero__background' ), duration,
      { x: -sign * (slideWidth - move) }, { x: 0, ease: Quart.easeInOut }, 0 );
    timeline.fromTo( $next.find( '.c-hero__content' ), duration,
      { x: -sign * slideWidth }, { x: 0, ease: Quart.easeInOut }, 0 );

    timeline.fromTo( $current, duration, { x: 0 }, { x: -sign * slideWidth, ease: Quart.easeInOut }, 0 );
    timeline.fromTo( $current.find( '.c-hero__background' ), duration,
      { x: 0 }, { x: sign * (slideWidth - move), ease: Quart.easeInOut }, 0 );
    timeline.fromTo( $current.find( '.c-hero__content' ), duration,
      { x: 0 }, { x: sign * slideWidth, ease: Quart.easeInOut }, 0 );

    timeline.play();
  }

  private getDirection( slick, currentSlide: number, nextSlide: number ): number {
    if ( nextSlide > currentSlide ) {
      return 1;
    }
    return -1;
  }

}
