// the semi-colon before the function invocation is a safety
// net against concatenated scripts and/or other plugins
// that are not closed properly.
;(function ($, window, document, undefined) {

    function PluginName(element, options) {
        this.element = element;

        // jQuery has an extend method that merges the
        // contents of two or more objects, storing the
        // result in the first object. The first object
        // is generally empty because we don't want to alter
        // the default options for future instances of the plugin
        this.options = $.extend($.fn.pluginName.defaults, options);
    }

    PluginName.prototype = {
        constructor: PluginName,
        _myMethod: function() {
            // ...
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn.pluginName = function ( options ) {
        return this.each(function () {
            if ( ! $.data(this, "plugin_" + PluginName) ) {
                $.data(this, "plugin_" + PluginName, new PluginName( this, options ));
            }
        });
    };

    // This object should contain as many options as possible
    // Users should be able to overwrite any option in their Custom JavaScript textarea like so:
    // $.fn.pluginName.defaults.option = myNewDefaultValue;
    $.fn.pluginName.defaults = {
    };

})( jQuery, window, document );