
var Recipe = function () {

};

Recipe.prototype.positionPrintBtn = function( $container ) {

    $container = typeof $container !== 'undefined' ? $container : $body;

    $container.find( '.jetpack-recipe' ).each( function( i, obj ) {
        var $recipe = $( obj ),
            $print = $recipe.find( ' .jetpack-recipe-print ' ),
            $recipeContent = $recipe.find( '.jetpack-recipe-content' );

        $print.find('a').clone( true ).appendTo($recipeContent).wrap( '<div class="jetpack-recipe-print"></div>' );

        var $ingredients = $recipe.find( '.jetpack-recipe-ingredients' );

        if( $ingredients.length ) {
            $( '.jetpack-recipe-content' ).find( '.jetpack-recipe-print' ).addClass( 'jetpack-has-ingredients' );
        }

        $print.remove();
    } );
};

Recipe.prototype.wrapRecipeElements = function( $container ) {

    $container = typeof $container !== 'undefined' ? $container : $body;

    $container.find( '.jetpack-recipe-image' ).wrap( '<div class="jetpack-recipe-image-container"></div>' );
    $container.find( ".jetpack-recipe-ingredients ul > li" ).each( function() {
        $( this ).firstWord();
    } );

};
