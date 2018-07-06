import $ from 'jquery';
import { BaseComponent } from '../models/DefaultComponent';
import { WindowService } from '../services/window.service';

export interface ProgressBarOptions {
  max?: number;
  offset?: number;
  canShow?: boolean;
}

export class ProgressBar extends BaseComponent {

  private $progressBar: JQuery = $( '.js-reading-progress' );
  private subscriptionActive: boolean = true;
  private options: ProgressBarOptions;
  private scrollPosition: number = 0;
  private max: number = 0;

  constructor( options: ProgressBarOptions ) {
    super();
    this.setOptions(options);
    this.init();
    this.bindEvents();
  }

  public init() {
    this.max = this.options.max > WindowService.getHeight() ?
      this.options.max - WindowService.getHeight() : this.options.max;
    this.$progressBar.attr( 'max', this.max );
  }

  public destroy() {
    this.subscriptionActive = false;
  }

  public bindEvents() {
    WindowService
      .onScroll()
      .takeWhile(() => this.subscriptionActive)
      .subscribe(() => {
        this.onScroll();
      });
  }

  public change( value: number ) {
    this.$progressBar.attr( 'value', value );
  }

  public setOptions( options: ProgressBarOptions ) {
    this.options = Object.assign({}, this.options, options);
  }

  public isCloseToEnd(): boolean {
    return this.max <= (this.scrollPosition - this.options.offset);
  }

  private onScroll() {
    this.scrollPosition = WindowService.getScrollY();

    if ( this.options.canShow && this.scrollPosition > this.options.offset ) {
      this.change(this.scrollPosition - this.options.offset);
    }
  }
}
