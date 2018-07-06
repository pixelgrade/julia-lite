import 'slick-carousel';
import $ from 'jquery';

import { BaseComponent } from '../models/DefaultComponent';
import { JQueryExtended } from '../BaseTheme';
import { Helper } from '../services/Helper';

export interface CarouselOptions {
  items_layout?: string;
  items_per_row?: number;
  show_pagination?: string;
}

const variableWidthDefaults = {
  infinite: true,
  slidesToScroll: 1,
  slidesToShow: 1,
  variableWidth: true
};

const fixedWidthDefaults = {
  infinite: true,
  slidesToScroll: 1,
  slidesToShow: 1,
  variableWidth: false,
};

export class Carousel extends BaseComponent {

  protected defaultSlickOptions = {
    dots: false,
    fade: false,
    nextArrow: '<div class="slick-next"></div>',
    prevArrow: '<div class="slick-prev"></div>',
    speed: 500,
  };
  protected slickOptions = this.defaultSlickOptions;

  public static customPagination(slider: JQuery, i: number ): JQuery {
    const index = i + 1;
    const sIndex = index <= 9 ? `0${index}` : index;
    return $('<button type="button" />').text( sIndex );
  }

  constructor( protected element: JQueryExtended, protected options: CarouselOptions = {} ) {
    super();

    this.extendOptions();
    this.bindEvents();

    // WindowService.onResize().debounce(300).subscribe( this.onResize.bind(this) );
  }

  public bindEvents() {
    this.bindSlick();
  }

  public destroy() {
    this.element.slick('unslick');
  }

  public onResize(): void {
    console.warn('carousel:resize');
    this.destroy();
    this.extendOptions();
    this.bindEvents();
    // setTimeout(() => {
    //
    // }, 100);
  }

  protected extendOptions() {
    if ( Helper.above( 'lap' ) ) {
      return this.extendDesktopOptions( this.options );
    } else {
      return this.extendMobileOptions( this.options );
    }
  }

  private extendMobileOptions( options: CarouselOptions ) {
    this.slickOptions = Object.assign( {}, this.defaultSlickOptions, {
      arrows: false,
      centerMode: true,
      centerPadding: '30px',
      dots: this.options.show_pagination === '',
      infinite: true,
      slidesToScroll: 1,
      slidesToShow: 1
    });
  }

  private extendDesktopOptions( options: CarouselOptions ) {

    this.slickOptions = Object.assign({}, this.defaultSlickOptions, {
      arrows: true,
      customPaging: Carousel.customPagination,
    });

    if ( this.options.show_pagination === '' ) {
      this.slickOptions.dots = true;
    }

    if ( this.options.items_layout === 'variable_width' ) {
      this.slickOptions = Object.assign({}, this.slickOptions, variableWidthDefaults);
    } else {
      this.slickOptions = Object.assign({}, this.slickOptions, fixedWidthDefaults);
    }

    if ( this.options.items_per_row ) {
      this.slickOptions = Object.assign({}, this.slickOptions, {
        slidesToScroll: this.options.items_per_row,
        slidesToShow: this.options.items_per_row
      });
    }
  }

  private bindSlick() {
    this.element.slick( this.slickOptions );
    this.element.find('.slick-cloned').find('img').addClass('is-loaded');
  }
}
