import * as Masonry from 'masonry-layout';
import { BaseComponent } from '../models/DefaultComponent';
import { JQueryExtended } from '../BaseTheme';
import { WindowService } from '../services/window.service';
import { GlobalService } from '../services/global.service';

export class Gallery extends BaseComponent {
  protected element: JQueryExtended;

  private subscriptionActive: boolean = true;
  private masonryGallerySelector: string = '.c-gallery--packed, .c-gallery--masonry';
  private masonry;

  constructor( element: JQueryExtended ) {
    super();

    this.element = element;
    this.layout();

    WindowService
      .onResize()
      .debounce(300 )
      .takeWhile( () => this.subscriptionActive )
      .subscribe( () => {
          this.layout();
      } );

    GlobalService
      .onCustomizerRender()
      .debounce( 300 )
      .takeWhile( () => this.subscriptionActive )
      .subscribe( () => {
          this.layout();
      } );

    GlobalService
      .onCustomizerChange()
      .debounce( 300 )
      .takeWhile( () => this.subscriptionActive )
      .subscribe( () => {
          this.layout();
      } );
  }

  public bindEvents() {

  }

  public destroy() {
    this.subscriptionActive = false;
  }

  private layout() {
    const $items = this.element.children();
    let minColumnWidth;

    if ( ! $items.length || ! this.element.is( this.masonryGallerySelector ) ) {
      return;
    }

    minColumnWidth = this.element.children().get(0).getBoundingClientRect().width;

    $items.each( (index, element) => {
      const width = element.getBoundingClientRect().width;
      minColumnWidth = width < minColumnWidth ? width : minColumnWidth;
    } );

    if ( this.masonry ) {
      this.masonry.destroy();
    }

    this.masonry = new Masonry( this.element.get(0), {
      columnWidth: minColumnWidth,
      transitionDuration: 0,
    } );
  }
}
