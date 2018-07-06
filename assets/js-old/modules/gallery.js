var Gallery = function( $container ) {

	var _this = this;

	_this.$el = $();

	$container = typeof $container !== "undefined" ? $container : $( 'body' );

	$container.find( '.c-gallery--packed' ).each( function( i, obj ) {
		var $gallery = $( obj );

		if ( ! $gallery.children().length ) {
			return;
		}

		_this.$el = _this.$el.add( $gallery );

		$gallery.data( 'offset', $gallery.offset() );

		_this.refresh( $gallery );
	} );
};

Gallery.prototype.refresh = function( $galleries ) {
	var _this = this;

	if ( typeof $galleries === "undefined" ) {
		$galleries = _this.$el;
	}

	$galleries.each( function( i, obj ) {

		var $gallery = $( obj );

		var minWidth = $gallery.children()[0].getBoundingClientRect().width;

		$gallery.children().each( function() {
			var width = this.getBoundingClientRect().width;

			if ( width < minWidth ) {
				minWidth = width;
			}
		} );

		$gallery.masonry( {
			isAnimated: false,
			columnWidth: minWidth
		} );

	} );

};
