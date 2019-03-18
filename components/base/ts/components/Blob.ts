import $ from 'jquery';
import * as anime from 'animejs';
import { BaseComponent } from '../models/DefaultComponent';

export class Blob extends BaseComponent {
  protected element: JQuery;
  protected presetOffset: number;

  private radius = 10;
  private sides;
  private preset;
  private complexity = 0.84;

  constructor(sides: number, complexity: number, preset: number, presetOffset: number = 0) {
    super();

    this.sides = sides;
    this.complexity = complexity;
    this.preset = preset + presetOffset;
    this.presetOffset = presetOffset;

    this.bindEvents();
    this.render();
  }

  public generateSvg() {
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg' );
    const polygon = document.createElementNS('http://www.w3.org/2000/svg', 'polygon' );

    svg.setAttribute( 'viewBox', '0 0 ' + 2 * this.radius + ' ' + 2 * this.radius );
    svg.setAttribute( 'fill', 'currentColor' );
    polygon.setAttribute( 'points', this.generatePoints( true ) );
    svg.appendChild( polygon );

    return svg;
  }

  public morph( morphDuration: number = 300 ) {
    anime({
      duration: morphDuration,
      offset: 0,
      points: this.generatePoints( true ),
      targets: this.element.find( 'polygon' ).get(0),
    });
  }

  public render() {
    const $svg = $( this.generateSvg() );

    if ( this.element ) {
      this.element.replaceWith( $svg );
    }

    this.element = $svg;
  }

  public getRatio(preset: number, i: number): number {
    const pow = Math.pow( preset, i );
    return ( 4 + 6 * this.getMagicDigit( pow ) / 9 ) / 10;
  }

  public setPreset(preset: number) {
    this.preset = preset + this.presetOffset;
  }

  public getMagicDigit( n ) {
    let sum = 0;

    while ( n > 0 || sum > 9 ) {
      if ( n === 0 ) {
        n = sum;
        sum = 0;
      }
      sum += n % 10;
      n = Math.floor(n / 10 );
    }
    return sum;
  }

  public setComplexity( complexity ) {
    this.complexity = complexity;
  }

  public setSides( sides ) {
    this.sides = sides;
  }

  public generatePoints( random: boolean = false ): string {
    const points = [];

    for (let i = 1; i <= this.sides; i++) {
      // generate a regular polygon
      // we add pi/2 to the angle to have the tip of polygons with odd number of edges pointing upwards
      const angle = 2 * Math.PI * i / this.sides - Math.PI / 2;

      // default ratio is 0.7 because the random one varies between 0.4 and 1
      const defaultRatio = 0.7;
      const ratio = defaultRatio + ( this.getRatio(this.preset, i) - defaultRatio ) * this.complexity;

      const x = this.radius * ( Math.cos( angle ) * ratio + 1 );
      const y = this.radius * ( Math.sin( angle ) * ratio + 1 );

      points.push( x + ',' + y );
    }

    return points.join(' ');
  }

  public getSvg(): JQuery {
    return this.element;
  }

  public getPreset(): number {
    return this.preset;
  }

  public bindEvents(): void {

  }

  public destroy(): void {

  }
}
