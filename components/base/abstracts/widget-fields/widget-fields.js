(function ($) {

    $(document).on('ready', function () {
		// Initialize fields sections logic
		$('#widgets-right .widget-content .accordion-container').each(function () {
			toggleWidgetSections(this);
		});

    	// Initialize conditional fields logic
        $('#widgets-right .widget-field-display_on').each(function () {
            toggleWidgetFields(this);
        });

		$('#widgets-right .widget-content').each(function () {
			// Initialize range fields logic
			handleRangeFields(this);

			// Initialize select2 fields logic
			handleSelect2Fields(this);
		});
    });

    // This is for when a new widget is added
    $(document).on('widget-added', function ( e, widgetContainer ) {
		// Initialize fields sections logic
		widgetContainer.find('.widget-content .accordion-container').each(function () {
			toggleWidgetSections(this);
		});

		// Initialize conditional fields logic
        widgetContainer.find('.widget-field-display_on').each(function () {
            toggleWidgetFields(this);
        });

		widgetContainer.find('.widget-content').each(function () {
			// Initialize range fields logic
			handleRangeFields(this);

			// Initialize select2 fields logic
			handleSelect2Fields(this);
		});
    });

    // This is for when a widget is saved
    $(document).on('widget-updated', function ( e, widgetContainer ) {
		// Initialize fields sections logic
		widgetContainer.find('.widget-content .accordion-container').each(function () {
			toggleWidgetSections(this);
		});

		// Initialize conditional fields logic
        widgetContainer.find('.widget-field-display_on').each(function () {
            toggleWidgetFields(this);
        });

		widgetContainer.find('.widget-content').each(function () {
			// Initialize range fields logic
			handleRangeFields(this);

			// Initialize select2 fields logic
			handleSelect2Fields(this);
		});
    });

    // When a widget is closed we close all the sections
	$(document.body).bind('click.widgets-toggle', function(e) {
		var target = $(e.target),
			widget, inside, section;

		// We only want to react to buttons from individual widgets, not whole sidebars
		if ( ( target.parents(' .widget.open > .widget-top').length && ! target.parents('#available-widgets').length ) || target.hasClass('widget-control-close') ) {
			widget = target.closest('div.widget');
			inside = widget.children('.widget-inside');

			inside.find('.accordion-section').removeClass('open').addClass('closed').attr( 'aria-expanded', 'false' );
			// Find the hidden input that keeps the open/closed state of the section and make sure it is closed
			inside.find('.accordion-section input._section-state').val('closed');

			e.preventDefault();
		}
	});

    // The Customizer also triggers 'widget-synced' but we may not be needing it.

	/**
	 * WIDGET SECTIONS TOGGLE LOGIC
	 */

	var toggleWidgetSections = function (el) {
		var $self = $(el);

		$(el).on( 'click', '.hndle', function (e) {
			e.preventDefault();
			e.stopPropagation();

			// Find the accordion main wrapper
			var $section = $( this ).closest('.accordion-section');

			if ( $section.length ) {
				// Find the hidden input that keeps the open/closed state of the section
				var $input = $section.find('input._section-state').first();

				$section.toggleClass('open closed');

				if ( $section.hasClass( 'open' ) ) {
					$section.attr( 'aria-expanded', 'true' );
					$input.val('open');
				} else {
					$section.attr( 'aria-expanded', 'false' );
					$input.val('closed');
				}
			}
		});
	}

	/**
	 * CONDITIONAL WIDGET FIELDS LOGIC - display_on
	 *
	 * Fold elements when the entire page is loaded.
	 * Some css classes are added dynamically, they can only be used after the load event.
	 */

    /**
     * Hide and show widget fields depending on each other
     */
    var toggleWidgetFields = function (el) {

        var $self = $(el),
            action = $self.data('action'),
            field = $self.attr('data-when_key'),
            value = $self.data('has_value'),
            selector = '[name="' + field + '"]',
            $selector = $(selector),
            currentValue = '';

        //we need to treat radio groups differently
        if ($selector.length > 1) {
            //we assume that we are in a group
            //then we need to get the value through other means
            currentValue = $('[name="' + field + '"]:checked').val();
        } else if( $selector.is(':checkbox') ) {
            currentValue = $selector.is(':checked');
        } else {
            currentValue = $selector.val();
        }

        if ( valueCheck( currentValue, value ) ) {
            toggleField(el, action);
        } else {
            toggleFieldOpposite(el, action);
        }

        //each time it changes get down to business
        $(document).on('change', selector, function (e) {
            //we need to treat radio groups differently
            if ($selector.length > 1) {
                //we assume that we are in a group
                //then we need to get the value through other means
                currentValue = $('[name="' + field + '"]:checked').val();
            } else if( $selector.is(':checkbox') ) {
                currentValue = $selector.is(':checked');
            } else {
                currentValue = $selector.val();
            }

            if ( valueCheck( currentValue, value ) ) {
                toggleField(el, action);
            } else {
                toggleFieldOpposite(el, action);
            }

        });
    };

    var toggleField = function (selector, action) {
        var when_key = $(selector).data('when_key'),
            $target = $('#' + when_key),
            $parent = $target.parent().parent();

        /**
         * Check if the curent element needs to be showed
         * Also if it's parent is hidden the child needs to follow
         */
        if (action == 'show' && !$parent.hasClass('hidden')) {
            $(selector).show().removeClass('hidden');
        } else {
            $(selector).hide().addClass('hidden');
        }

        /**
         * Trigger a change!
         * This way our children (elements) will know that something is changed and they should follow
         */
        $(selector).find('select, input:radio').trigger('change');
    };

    var toggleFieldOpposite = function (selector, action) {
        var when_key = $(selector).data('when_key'),
            $target = $('#' + when_key),
            $parent = $target.parent().parent();
        /**
         * Check if the curent element needs to be showed
         * Also if it's parent is hidden the child needs to follow
         */
        if (action == 'hide' && !$parent.hasClass('hidden')) {
            $(selector).show().removeClass('hidden');
        } else {
            $(selector).hide().addClass('hidden');
        }

        /**
         * Trigger a change!
         * This way our children (elements) will now that something is changed and they should follow
         */
        $(selector).find('select, input:radio').trigger('change');
    };

    var valueCheck = function(currentValue, value ) {
        var valueTest = false;

        // if is not an object convert to string
        if ( typeof value === 'number' || typeof value === 'boolean' ) {
            value = String(value);
        }

        //check for single or multiple values
        if ( typeof value === 'string' && currentValue == value ) {
            valueTest = true;
        } else if ( typeof value === 'object' && value.indexOf( currentValue ) > -1) {
            // in case there are multiple values check if our current values is inside the array
            valueTest = true;
        }

        return valueTest;
    };

	var handleRangeFields = function (el) {

		// For each range input add a number field (for preview mainly - but it can also be used for input)
		$(el).find('input[type="range"]').each(function () {
			if ( ! $(this).siblings('.range-value').length ) {
				var $clone = $(this).clone();

				$clone
					.attr('type', 'number')
					.attr('class', 'range-value');

				$(this).after($clone);
			}

			// Update the number field when changing the range
			$(this).on('input', function () {
				$(this).siblings('.range-value').val($(this).val());
			});

			// And the other way around, update the range field when changing the number
			$($clone).on('input', function () {
				$(this).siblings('input[type="range"]').val($(this).val());
			});
		});
	}

	var handleSelect2Fields = function (el) {

		// Initialize each select with the appropriate class
		$(el).find('select.js-select2').each(function () {
			$(this).select2();

			$(this).on('select2:select', function(e){
				var elm = e.params.data.element;
				$elm = $(elm);
				$t = $(this);
				$t.append($elm);
				$t.trigger('change.select2');
			});
		});
	}

	/**
	 * Inspired by the Tribe Image Widget Javascript
	 * @link https://github.com/moderntribe/image-widget
	 */
	widgetImageFields = {

		// Call this from the upload button to initiate the upload frame.
		uploader : function( widget_id, widget_id_string ) {

			var frame = wp.media({
				title : pixelgradeWidgetFields.image.frame_title,
				multiple : false,
				library : { type : 'image' },
				button : { text : pixelgradeWidgetFields.image.button_title }
			});

			// Handle results from media manager.
			frame.on('close',function( ) {
				var attachments = frame.state().get('selection').toJSON();
				widgetImageFields.render( widget_id, widget_id_string, attachments[0] );
			});

			frame.open();
			return false;
		},

		// Output Image preview and populate widget form.
		render : function( widget_id, widget_id_string, attachment ) {

			var $attachment_id = $( document.getElementById( widget_id_string ) );
			var $image_url     = $( document.getElementById( widget_id_string + '-imageurl' ) );
			var $preview     = $( document.getElementById( widget_id_string + '-preview' ) );

			// Make sure that we know if we have an image or not
			if ( ! attachment.id ) {
				$preview.addClass( 'no-image' );
			} else {
				$preview.removeClass( 'no-image' )
			}

			// Delete the previous img element, if any
			$preview.find('img').remove();
			// Add the new img element
			$("#" + widget_id_string + '-preview').append(widgetImageFields.imgHTML( attachment ) );

			// update the attachment id if it has changed
			if ( $attachment_id.val() !== attachment.id ) {
				$attachment_id.val( attachment.id ).trigger( 'change' );
			}

			// update the url if it has changed
			if ( $image_url.val() !== attachment.url ) {
				$image_url.val( attachment.url ).trigger( 'change' );
			}
		},

		// Delete the currently saved image
		clear : function( widget_id, widget_id_string ) {
			var $attachment_id = $( document.getElementById( widget_id_string ) );
			var $image_url     = $( document.getElementById( widget_id_string + '-imageurl' ) );
			var $preview     = $( document.getElementById( widget_id_string + '-preview' ) );

			// Delete the attachment ID and image URL
			$attachment_id.val('0');
			$image_url.val('');

			// Delete the image element
			$preview.find('img').remove();
			$preview.addClass( 'no-image' );

			// And trigger a change so the widget knows it should save
			$attachment_id.trigger( 'change' );
		},

		// Render html for the image.
		imgHTML : function( attachment ) {
			var img_html = '<img src="' + attachment.url + '" ';
			img_html += 'width="' + attachment.width + '" ';
			img_html += 'height="' + attachment.height + '" ';
			if ( attachment.alt != '' ) {
				img_html += 'alt="' + attachment.alt + '" ';
			}
			img_html += '/>';
			return img_html;
		},

	}

})(window.jQuery);
