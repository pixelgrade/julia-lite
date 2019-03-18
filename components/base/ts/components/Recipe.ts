import $ from 'jquery';
import { BaseComponent } from '../models/DefaultComponent';
import { Helper } from '../services/Helper';

const jetpackRecipeClass = 'jetpack-recipe';
const jetpackRecipePrintClass = 'jetpack-recipe-print';
const jetpackRecipeContentClass = 'jetpack-recipe-content';
const jetpackRecipeIngredientsClass = 'jetpack-recipe-ingredients';

export class Recipe extends BaseComponent {

  private $body: JQuery = $( 'body' );

  constructor() {
    super();

    this.bindEvents();
  }

  public destroy() {

  }

  public bindEvents() {

  }

  public positionPrintBtn( $container: JQuery = this.$body ) {
    $container.find( `.${jetpackRecipeClass}` ).each( ( index, element ) => {
      const $recipe = $( element );
      const $print = $recipe.find( `.${jetpackRecipePrintClass}` );
      const $recipeContent = $recipe.find( `.${jetpackRecipeContentClass}` );
      const $ingredients = $recipe.find( `.${jetpackRecipeIngredientsClass}` );

      $print
        .find('a')
        .clone( true )
        .appendTo( $recipeContent )
        .wrap( `<div class="${jetpackRecipePrintClass}"></div>` );

      if ( $ingredients.length ) {
        $recipeContent
          .find( `.${jetpackRecipePrintClass}` )
          .addClass( 'jetpack-has-ingredients' );
      }

      $print.remove();
    } );
  }

  public wrapRecipeElements( $container: JQuery = this.$body ) {
    $container
      .find( '.jetpack-recipe-image' )
      .wrap( '<div class="jetpack-recipe-image-container"></div>' );

    $container
      .find( `.${jetpackRecipeIngredientsClass} ul > li` )
      .each( ( index, element ) => {
        Helper.markFirstWord($( element ));
      } );

  }
}
