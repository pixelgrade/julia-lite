$.fn.firstWord = function() {
    var text = this.text().trim().split( " " );
    var first = text.shift();
    this.html( ( text.length > 0 ? "<span class='first-word'>" + first + "</span> " : first ) + text.join( " " ) );
};

$.fn.fitText = function() {
	var $text = $( this );
	var letterFontSize = parseFloat( $text.css( 'fontSize' ) );
	var textHeight = $text.outerHeight();

	var parentHeight = $text.parent().outerHeight();

	$text.css( 'fontSize', letterFontSize * parentHeight / textHeight );
}