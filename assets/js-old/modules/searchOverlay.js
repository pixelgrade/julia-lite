var SearchOverlay = function () {
	this.eventHandlers();
};

SearchOverlay.prototype.eventHandlers = function() {
	// Header Search trigger behaviour
	var that = this;

	$( document ).on( 'click', '.js-search-trigger', function() {
		that.open();
	});

	$( document ).on( 'click', '.js-search-close', function() {
		that.close();
	});
}

SearchOverlay.prototype.open = function() {
	$( '.c-search-overlay .search-field' ).focus();
	$body.addClass( 'show-search-overlay' );

	// Bind overlay dismissal to escape key
	$( document ).on( 'keyup', { searchModule: this }, this.closeOnEsc );
};

SearchOverlay.prototype.close = function() {
	$body.removeClass( 'show-search-overlay' );
	$( '.c-search-overlay .search-field' ).blur();

	// Unbind overlay dismissal from escape key
	$( document ).off( 'keyup', this.closeOnEsc );
};

SearchOverlay.prototype.closeOnEsc = function ( e ) {
	if ( e.keyCode == 27 ) {
		e.data.searchModule.close();
	}
}
