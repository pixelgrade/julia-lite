/**
 * "Extend" the Jetpack Gallery Settings with some spacing controls
 */
(function($) {
	var media = wp.media;

	// Wrap the render() function to append controls.
	media.view.Settings.Gallery = media.view.Settings.Gallery.extend({
		render: function() {
			var $el = this.$el;

			media.view.Settings.prototype.render.apply( this, arguments );

			// Append the 'type' template and update the settings.
			$el.append( media.template( 'jetpack-gallery-settings' ) );
			media.gallery.defaults.type = 'default'; // lil' hack that lets media know there's a type attribute.
			this.update.apply( this, ['type'] );

			// Append the 'spacing' template and update the settings.
			$el.append( media.template( 'pixelgrade-gallery-settings' ) );
			media.gallery.defaults.spacing = 'default'; // lil hack that lets media know there's a spacing attribute.
			this.update.apply( this, ['spacing'] );

			// Go through all the fields and add some helper classes to the top wrapper (it's a label element :( )
			$el.children( 'label' ).each( function() {
				// Find a child to this label with the data-setting attribute and get that value
				var dataSetting = $(this).children( '[data-setting]' ).first().data( 'setting' );

				if ( typeof dataSetting !== 'undefined' && dataSetting.length ) {
					$(this).addClass( 'gallery-field-' + dataSetting );
					$(this).addClass( 'current-posttype-' + pixelgradeGallerySettings.postType );
				}
			});

			// Hide the Columns and Spacing setting for all types except Default
			$el.find( 'select[name=type]' ).on( 'change', function () {
				var columnSetting = $el.find( 'select[name=columns]' ).closest( 'label.setting' ),
					spacingSetting = $el.find( 'select[name=spacing]' ).closest( 'label.setting' );

				if ( 'default' === $( this ).val() || 'thumbnails' === $( this ).val() || 'masonry' === $( this ).val() ) {
					if ( typeof columnSetting !== 'undefined' ) {
						columnSetting.show();
					}

					if ( typeof spacingSetting !== 'undefined' ) {
						spacingSetting.show();
					}
				} else {
					if ( typeof columnSetting !== 'undefined' ) {
						columnSetting.hide();
					}

					if ( typeof spacingSetting !== 'undefined' ) {
						spacingSetting.hide();
					}
				}

			} ).change();

			return this;
		}
	});
})(jQuery);
