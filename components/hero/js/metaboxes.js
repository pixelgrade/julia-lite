(function($){

	$(document).ready(function(){
		var featured_image = $('#postimagediv .inside'),
			project_color = $('#_project_color');

		if ( project_color.length > 0 ) {

			featured_image.on('html-change-post', function() {
				var image = featured_image.find('#set-post-thumbnail img');
				if ( image.length > 0 ) {

					var alt = $(image).attr('alt'),
						src = $(image).attr('src');

					$.ajax({
						type: "post",
						url: ajaxurl,
						data: { action: 'pxg_get_project_color', attachment_src: src },
						success:function(response){

							if ( typeof response.success !== "undefined" && ! response.success ) return;

							var color = '#' + response.data,
								isColor  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);

							if ( isColor ) {
								//$('#postimagediv').attr('style', 'background-color:'+ color);

								var palettes = get_colorpicker_palettes(color);

								// setup the color and the new palettes
								$('#_project_color')
									.iris('option', 'palettes', palettes )
									.val(color)
									.trigger('change');
							}
						}
					});
				}
			});

			var get_colorpicker_palettes = function( color ){

				var palettes = [],
					darker1 = ColorLuminance(color, -0.5),
					darker2 = ColorLuminance(color, -0.25),
					lighter1 = ColorLuminance(color, 0.25),
					lighter2 = ColorLuminance(color, 0.5),
					current_color = $('#_project_color').val();


				// in the future save the old color in palette
				//if(typeof(Storage) !== "undefined") {
				//
				//}

				palettes = ['#fff', lighter2, lighter1, color, darker2, darker1, '#000', current_color];

				return palettes;
			};

			$(document).on('mouseup', '#_project_aside .iris-square-inner, #_project_aside .iris-slider-offset, #_project_aside .iris-palette-container', function(){

				var el = $('<input type="hidden" id="_project_color_forced_by_user" name="_project_color_forced_by_user" value="'+ curent_colorpicker.val() +'" />');

				if ( $('#_project_color_forced_by_user').length == 0 ) {
					project_color.parent().append( el );
				} else {
					$('#_project_color_forced_by_user').val(project_color.val());
				}

			});
		}

        if( typeof (tinyMCE) === "undefined" ) return;

        // We need to do the hero editor background color init a little later to allow for all the other controls to be in top shape
        setTimeout(
            function() {
                var $hero_color =  $('#_hero_background_color');

                if ( $hero_color.length > 0 ) {
                    hero_add_editor_bg_color( $hero_color.val() );

                    $hero_color.on('wpcolorpicker:change', function ( ev ) {
                        var color = $(this).val();
                        hero_add_editor_bg_color(color);
                    } );
                }
            }, 1000);
	});

	$(window).load(function () {

		setTimeout(hero_toggleSlidesOptionsDisplay, 300 );
		setTimeout(hero_featuredProjectsHelperIds, 300 );

		// classify the gallery number
		$('#pixgallery, #pixvideos').on( 'html-change-post', function() {
			hero_toggleSlidesOptionsDisplay();
		});

		// We will use the legacy key also
		$('#_hero_featured_projects_ids, #_portfolio_featured_projects').on('change', function () {
			hero_toggleSlidesOptionsDisplay();
			hero_featuredProjectsHelperIds();
		});

        if( typeof (tinyMCE) === "undefined" ) return;

        $('<span class="hero-hidden-overlay  dashicons  dashicons-hidden"></span>').insertAfter('#_hero_content_description_ifr');

        hero_check_desc_visibility();

        var hero_editor = tinyMCE.get('_hero_content_description');

        if ( typeof hero_editor !== "undefined" && hero_editor !== null  ) {
            hero_editor.on('keyup', function  (e) {
                hero_check_desc_visibility();
            });
        }

        // Unfortunately, for good styling we need to add a class to the Featured Projects Title <li>
		$('#hero_area_content__page label[for="_hero_featured_projects_title"]').closest('li.cmb-type').addClass( '_hero_featured_projects_title' );
	});

	var hero_add_editor_bg_color = function( color ) {
		var $hero_desc_ifr = $('#wp-_hero_content_description-wrap').find('iframe');

		if ( $hero_desc_ifr.length > 0 ) {
			$hero_desc_ifr.contents().find('body').css({backgroundColor: color});
		}
	};

	// Redefines jQuery.fn.html() to add custom events that are triggered before and after a DOM element's innerHtml is changed
	// html-change-pre is triggered before the innerHtml is changed
	// html-change-post is triggered after the innerHtml is changed
	var eventName = 'html-change';
	// Save a reference to the original html function
	jQuery.fn.originalHtml = jQuery.fn.html;
	// Let's redefine the html function to include a custom event
	jQuery.fn.html = function() {
		var currentHtml = this.originalHtml();
		if(arguments.length) {
			this.trigger(eventName + '-pre', jQuery.merge([currentHtml], arguments));
			jQuery.fn.originalHtml.apply(this, arguments);
			this.trigger(eventName + '-post', jQuery.merge([currentHtml], arguments));
			return this;
		} else {
			return currentHtml;
		}
	};

	/**
	 * Position hero content accordingly after selecting
	 * positioning from the hero section
	 */
	$( window ).on( 'load', function() {
		// Get the contents in the hero description iframe
		var $document = $('#_hero_content_description_ifr').contents();
		$document.find( 'html' ).addClass( '_hero_description_html' );
		var $heroDescriptionBody = $document.find( 'body' );

		if ( $heroDescriptionBody.length === 1 ) {
			// When clicking on the position selector, get the position
			$( '.positions_map label' ).on( 'click', function() {
				var positions = $( 'input#' + $( this ).attr( 'for' ) ).attr( 'value' ).split( ' ' );

				// Clear all previous positions
				$heroDescriptionBody.removeClass( 'position--left' );
				$heroDescriptionBody.removeClass( 'position--right' );
				$heroDescriptionBody.removeClass( 'position--top' );
				$heroDescriptionBody.removeClass( 'position--bottom' );

				// Add the corresponding positioning classes
				if ( positions.length != 0 && positions != '' ) {
					$.each( positions, function( index, value ) {
						$heroDescriptionBody.addClass( 'position--' + value );
					});
				}
			});
		}
	});

    /**
     * check the number of slides for this page.It checks the image gallery, video playlist and the number of
     * featured projects if they are visible
     * @returns {*}
     */
    var hero_check_number_of_slides = function() {
        var $featured_projects = $('#_hero_featured_projects_ids'),
			featured = 0,
			$images = $('#pixgalleries'),
            images = 0,
			$videos = $('#pixplaylist'),
            videos = 0;

        // If we haven't found the featured projects field, try the legacy one
        if ( ! $featured_projects.length ) {
			$featured_projects = $('#_portfolio_featured_projects');
		}

		if ( $featured_projects.length && $featured_projects.select2('data').length && $featured_projects.parents('.cmb-type').is(':visible') ) {
			featured = $featured_projects.select2('data').length;
		}

        if ( typeof $images.val() !== "undefined" && '' !== $images.val() ) {
            images = $images.val().split(',').length;
        }

        if (  typeof $videos.val() !== "undefined" && '' !== $videos.val() ) {
            videos = $videos.val().split(',').length;
        }

        return ( images + videos + featured );
    };

    /**
     * Here we check if we need to display the slider settings or not
     */
    var hero_toggleSlidesOptionsDisplay = function () {
        hero_check_desc_visibility();

        $('#_hero_slideshow_options__title').parents('.cmb-type.cmb-type-title').addClass('slideshow-area-title');

        if ( hero_check_number_of_slides() > 1 ) {
            $('.slideshow-area-title').addClass('is--enabled').removeClass('is--disabled');

            $('#_hero_slideshow_options__show_adjacent_slides, #_hero_slideshow_options__autoplay, #_hero_slideshow_options__delay').each(function () {
                $(this).parents('.cmb-type').removeClass('has--no-slides');
            })
        } else {
            $('.slideshow-area-title').addClass('is--disabled').removeClass('is--enabled');

            $('#_hero_slideshow_options__show_adjacent_slides, #_hero_slideshow_options__autoplay, #_hero_slideshow_options__delay').each(function () {
                $(this).parents('.cmb-type').addClass('has--no-slides');
            })
        }
    };

    /*
     * Here we append to the Featured Projects field description, the selected ids as a helper for use in shortcodes (exclude IDs maybe?)
     */
    var hero_featuredProjectsHelperIds = function () {
		var $featured_projects = $('#_hero_featured_projects_ids');

		// If we haven't found the featured projects field, try the legacy one
		if ( ! $featured_projects.length ) {
			$featured_projects = $('#_portfolio_featured_projects');
		}

		if ( $featured_projects.length ) {
			var $featured_projects_desc = $featured_projects.siblings('.cmb_metabox_description').eq(0);

			if ( ! $featured_projects_desc.length ) {
				$featured_projects.parents('.cmb-type').append('<div class="cmb_metabox_description"><div class="hero_featured_projects_helper_ids"></div></div>');
				$featured_projects_desc = $featured_projects.siblings('.cmb_metabox_description').eq(0);
			}

			var $featured_projects_helper_ids = $featured_projects_desc.children('.hero_featured_projects_helper_ids').eq(0);

			if ( ! $featured_projects_helper_ids.length ) {
				$featured_projects_desc.append('<div class="hero_featured_projects_helper_ids"></div>');
				$featured_projects_helper_ids = $featured_projects_desc.children('.hero_featured_projects_helper_ids').eq(0);
			}

			// We have no featured projects selected
			if ( '' === $featured_projects.val() ) {
				$featured_projects_helper_ids.empty();
			} else {
				$featured_projects_helper_ids.text( pixelgrade_hero_admin.featured_projects_ids_helper );
				$featured_projects_helper_ids.append( '<span>' + $featured_projects.val() + '</span>' )
			}
		}
	};

    var has_description_content = function() {
        if( typeof (tinyMCE) === "undefined" ) {
            return !!$('#_hero_content_description').val();
        }

        var hero_editor = tinyMCE.get('_hero_content_description');

        if ( typeof hero_editor === "undefined" || hero_editor === null ) {
            return false;
        }
        return hero_editor.getContent().length;
    };

    var hero_check_desc_visibility = function () {

        $('#wp-_hero_content_description-wrap').siblings('.cmb_metabox_description').addClass('hero-visibility');
        if ( ! has_description_content() && hero_check_number_of_slides() < 1  ) {
            $('#hero_area_content__page').addClass('is--hidden').removeClass('is--visible');
        } else {
            $('#hero_area_content__page').addClass('is--visible').removeClass('is--hidden');
        }
    };

})(jQuery);

function ColorLuminance(hex, lum) {

	// validate hex string
	hex = String(hex).replace(/[^0-9a-f]/gi, '');
	if (hex.length < 6) {
		hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
	}
	lum = lum || 0;

	// convert to decimal and change luminosity
	var rgb = "#", c, i;
	for (i = 0; i < 3; i++) {
		c = parseInt(hex.substr(i*2,2), 16);
		c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
		rgb += ("00"+c).substr(c.length);
	}

	return rgb;
}
