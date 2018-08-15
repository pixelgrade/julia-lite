// phpcs:disable
webpackJsonp([0],[
/* 0 */,
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return BaseComponent; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var BaseComponent = function BaseComponent() {
    _classCallCheck(this, BaseComponent);
};

/***/ }),
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Helper; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }


var Helper = function () {
    function Helper() {
        _classCallCheck(this, Helper);
    }

    _createClass(Helper, null, [{
        key: 'isTouch',
        value: function isTouch() {
            // return 'ontouchstart' in window || 'DocumentTouch' in window && document instanceof DocumentTouch;
            return 'ontouchstart' in window || 'DocumentTouch' in window;
        }
    }, {
        key: 'handleCustomCSS',
        value: function handleCustomCSS($container) {
            var $elements = typeof $container !== 'undefined' ? $container.find('[data-css]') : __WEBPACK_IMPORTED_MODULE_0_jquery___default()('[data-css]');
            if ($elements.length) {
                $elements.each(function (index, obj) {
                    var $element = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                    var css = $element.data('css');
                    if (typeof css !== 'undefined') {
                        $element.replaceWith('<style type="text/css">' + css + '</style>');
                    }
                });
            }
        }
        /**
         * Search every image that is alone in a p tag and wrap it
         * in a figure element to behave like images with captions
         *
         * @param $container
         */

    }, {
        key: 'unwrapImages',
        value: function unwrapImages() {
            var $container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : Helper.$body;

            $container.find('p > img:first-child:last-child, p > a:first-child:last-child > img').each(function (index, obj) {
                var $obj = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                var $image = $obj.closest('img');
                var className = $image.attr('class');
                var $p = $image.closest('p');
                var $figure = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<figure />').attr('class', className);
                console.log($figure, $p, __WEBPACK_IMPORTED_MODULE_0_jquery___default.a.trim($p.text()).length);
                if (__WEBPACK_IMPORTED_MODULE_0_jquery___default.a.trim($p.text()).length) {
                    return;
                }
                $figure.append($image.removeAttr('class')).insertAfter($p);
                $p.remove();
            });
        }
    }, {
        key: 'wrapEmbeds',
        value: function wrapEmbeds() {
            var $container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : Helper.$body;

            $container.children('iframe, embed, object').wrap('<p>');
        }
        /**
         * Initialize video elements on demand from placeholders
         *
         * @param $container
         */

    }, {
        key: 'handleVideos',
        value: function handleVideos() {
            var $container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : Helper.$body;

            $container.find('.video-placeholder').each(function (index, obj) {
                var $placeholder = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                var video = document.createElement('video');
                var $video = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(video).addClass('c-hero__video');
                // play as soon as possible
                video.onloadedmetadata = function () {
                    return video.play();
                };
                video.src = $placeholder.data('src');
                video.poster = $placeholder.data('poster');
                video.muted = true;
                video.loop = true;
                $placeholder.replaceWith($video);
            });
        }
    }, {
        key: 'smoothScrollTo',
        value: function smoothScrollTo() {
            var to = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
            var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1000;
            var easing = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'swing';

            __WEBPACK_IMPORTED_MODULE_0_jquery___default()('html, body').stop().animate({
                scrollTop: to
            }, duration, easing);
        }
        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.

    }, {
        key: 'debounce',
        value: function debounce(func, wait, immediate) {
            var _this = this,
                _arguments = arguments;

            var timeout = void 0;
            return function () {
                var context = _this;
                var args = _arguments;
                var later = function later() {
                    timeout = null;
                    if (!immediate) {
                        func.apply(context, args);
                    }
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) {
                    func.apply(context, args);
                }
            };
        }
        // Returns a function, that, when invoked, will only be triggered at most once
        // during a given window of time. Normally, the throttled function will run
        // as much as it can, without ever going more than once per `wait` duration;
        // but if you'd like to disable the execution on the leading edge, pass
        // `{leading: false}`. To disable execution on the trailing edge, ditto.

    }, {
        key: 'throttle',
        value: function throttle(callback, limit) {
            var wait = false;
            return function () {
                if (!wait) {
                    callback();
                    wait = true;
                    setTimeout(function () {
                        wait = false;
                    }, limit);
                }
            };
        }
    }, {
        key: 'mq',
        value: function mq(direction, query) {
            var $temp = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="u-mq-' + direction + '-' + query + '">').appendTo('body');
            var response = $temp.is(':visible');
            $temp.remove();
            return response;
        }
    }, {
        key: 'below',
        value: function below(query) {
            return Helper.mq('below', query);
        }
    }, {
        key: 'above',
        value: function above(query) {
            return Helper.mq('above', query);
        }
    }, {
        key: 'getParamFromURL',
        value: function getParamFromURL(param, url) {
            var parameters = url.split('?');
            if (typeof parameters[1] === 'undefined') {
                return parameters[1];
            }
            parameters = parameters[1].split('&');
            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {
                for (var _iterator = Array.from(Array(parameters.length).keys())[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    var i = _step.value;

                    var parameter = parameters[i].split('=');
                    if (parameter[0] === param) {
                        return parameter[1];
                    }
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
            }
        }
    }, {
        key: 'reloadScript',
        value: function reloadScript(filename) {
            var $old = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('script[src*="' + filename + '"]');
            var $new = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<script>');
            var src = $old.attr('src');
            if (!$old.length) {
                return;
            }
            $old.replaceWith($new);
            $new.attr('src', src);
        }
        /**
         * returns version of IE or false, if browser is not Internet Explorer
         */

    }, {
        key: 'getIEversion',
        value: function getIEversion() {
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf('MSIE ');
            if (msie > 0) {
                // IE 10 or older => return version number
                return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
            }
            var trident = ua.indexOf('Trident/');
            if (trident > 0) {
                // IE 11 => return version number
                var rv = ua.indexOf('rv:');
                return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
            }
            var edge = ua.indexOf('Edge/');
            if (edge > 0) {
                // Edge (IE 12+) => return version number
                return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
            }
            // other browser
            return false;
        }
    }, {
        key: 'markFirstWord',
        value: function markFirstWord($el) {
            var text = $el.text().trim().split(' ');
            var first = text.shift();
            $el.html((text.length > 0 ? '<span class="first-word">' + first + '</span> ' : first) + text.join(' '));
        }
    }, {
        key: 'fitText',
        value: function fitText($el) {
            var currentFontSize = parseFloat($el.css('fontSize'));
            var currentLineHeight = parseFloat($el.css('lineHeight'));
            var parentHeight = $el.parent().outerHeight() || 1;
            $el.css('fontSize', currentFontSize * parentHeight / currentLineHeight);
        }
    }]);

    return Helper;
}();
Helper.$body = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('body');

/***/ }),
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return WindowService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_rx_dom__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_rx_dom___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_rx_dom__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_jquery__);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }



var WindowService = function () {
    function WindowService() {
        _classCallCheck(this, WindowService);
    }

    _createClass(WindowService, null, [{
        key: 'onLoad',
        value: function onLoad() {
            return __WEBPACK_IMPORTED_MODULE_0_rx_dom__["DOM"].fromEvent(this.getWindowEl(), 'load');
        }
    }, {
        key: 'onResize',
        value: function onResize() {
            return __WEBPACK_IMPORTED_MODULE_0_rx_dom__["DOM"].resize(this.getWindowEl());
        }
    }, {
        key: 'onScroll',
        value: function onScroll() {
            return __WEBPACK_IMPORTED_MODULE_0_rx_dom__["DOM"].scroll(this.getWindowEl());
        }
    }, {
        key: 'getWindow',
        value: function getWindow() {
            return WindowService.$window;
        }
    }, {
        key: 'getScrollY',
        value: function getScrollY() {
            return (window.pageYOffset || document.documentElement.scrollTop) - (document.documentElement.clientTop || 0);
        }
    }, {
        key: 'getWidth',
        value: function getWidth() {
            return WindowService.$window.width();
        }
    }, {
        key: 'getHeight',
        value: function getHeight() {
            return WindowService.$window.height();
        }
    }, {
        key: 'getWindowEl',
        value: function getWindowEl() {
            return WindowService.$window[0];
        }
    }, {
        key: 'getOrientation',
        value: function getOrientation() {
            return WindowService.getWidth() > WindowService.getHeight() ? 'landscape' : 'portrait';
        }
    }]);

    return WindowService;
}();
WindowService.$window = __WEBPACK_IMPORTED_MODULE_1_jquery___default()(window);

/***/ }),
/* 4 */,
/* 5 */,
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return GlobalService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_rx_dom__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_rx_dom___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_rx_dom__);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }


var GlobalService = function () {
    function GlobalService() {
        _classCallCheck(this, GlobalService);
    }

    _createClass(GlobalService, null, [{
        key: 'onCustomizerRender',
        value: function onCustomizerRender() {
            var exWindow = window;
            return __WEBPACK_IMPORTED_MODULE_0_rx_dom__["Observable"].create(function (observer) {
                if (exWindow.wp && exWindow.wp.customize && exWindow.wp.customize.selectiveRefresh) {
                    exWindow.wp.customize.selectiveRefresh.bind('partial-content-rendered', function (placement) {
                        observer.onNext($(placement.container));
                    });
                }
            });
        }
    }, {
        key: 'onCustomizerChange',
        value: function onCustomizerChange() {
            var exWindow = window;
            return __WEBPACK_IMPORTED_MODULE_0_rx_dom__["Observable"].create(function (observer) {
                if (exWindow.wp && exWindow.wp.customize) {
                    exWindow.wp.customize.bind('change', function (setting) {
                        observer.onNext(setting);
                    });
                }
            });
        }
    }, {
        key: 'onReady',
        value: function onReady() {
            return __WEBPACK_IMPORTED_MODULE_0_rx_dom__["DOM"].ready();
        }
    }]);

    return GlobalService;
}();

/***/ }),
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Carousel; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_slick_carousel__ = __webpack_require__(14);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_slick_carousel___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_slick_carousel__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__models_DefaultComponent__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__services_Helper__ = __webpack_require__(2);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }





var variableWidthDefaults = {
    infinite: true,
    slidesToScroll: 1,
    slidesToShow: 1,
    variableWidth: true
};
var fixedWidthDefaults = {
    infinite: true,
    slidesToScroll: 1,
    slidesToShow: 1,
    variableWidth: false
};
var Carousel = function (_BaseComponent) {
    _inherits(Carousel, _BaseComponent);

    function Carousel(element) {
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

        _classCallCheck(this, Carousel);

        var _this = _possibleConstructorReturn(this, (Carousel.__proto__ || Object.getPrototypeOf(Carousel)).call(this));

        _this.element = element;
        _this.options = options;
        _this.defaultSlickOptions = {
            dots: false,
            fade: false,
            nextArrow: '<div class="slick-next"></div>',
            prevArrow: '<div class="slick-prev"></div>',
            speed: 500
        };
        _this.slickOptions = _this.defaultSlickOptions;
        _this.extendOptions();
        _this.bindEvents();
        // WindowService.onResize().debounce(300).subscribe( this.onResize.bind(this) );
        return _this;
    }

    _createClass(Carousel, [{
        key: 'bindEvents',
        value: function bindEvents() {
            this.bindSlick();
        }
    }, {
        key: 'destroy',
        value: function destroy() {
            this.element.slick('unslick');
        }
    }, {
        key: 'onResize',
        value: function onResize() {
            console.warn('carousel:resize');
            this.destroy();
            this.extendOptions();
            this.bindEvents();
            // setTimeout(() => {
            //
            // }, 100);
        }
    }, {
        key: 'extendOptions',
        value: function extendOptions() {
            if (__WEBPACK_IMPORTED_MODULE_3__services_Helper__["a" /* Helper */].above('lap')) {
                return this.extendDesktopOptions(this.options);
            } else {
                return this.extendMobileOptions(this.options);
            }
        }
    }, {
        key: 'extendMobileOptions',
        value: function extendMobileOptions(options) {
            this.slickOptions = Object.assign({}, this.defaultSlickOptions, {
                arrows: false,
                centerMode: true,
                centerPadding: '30px',
                dots: this.options.show_pagination === '',
                infinite: true,
                slidesToScroll: 1,
                slidesToShow: 1
            });
        }
    }, {
        key: 'extendDesktopOptions',
        value: function extendDesktopOptions(options) {
            this.slickOptions = Object.assign({}, this.defaultSlickOptions, {
                arrows: true,
                customPaging: Carousel.customPagination
            });
            if (this.options.show_pagination === '') {
                this.slickOptions.dots = true;
            }
            if (this.options.items_layout === 'variable_width') {
                this.slickOptions = Object.assign({}, this.slickOptions, variableWidthDefaults);
            } else {
                this.slickOptions = Object.assign({}, this.slickOptions, fixedWidthDefaults);
            }
            if (this.options.items_per_row) {
                this.slickOptions = Object.assign({}, this.slickOptions, {
                    slidesToScroll: this.options.items_per_row,
                    slidesToShow: this.options.items_per_row
                });
            }
        }
    }, {
        key: 'bindSlick',
        value: function bindSlick() {
            this.element.slick(this.slickOptions);
            this.element.find('.slick-cloned').find('img').addClass('is-loaded');
        }
    }], [{
        key: 'customPagination',
        value: function customPagination(slider, i) {
            var index = i + 1;
            var sIndex = index <= 9 ? '0' + index : index;
            return __WEBPACK_IMPORTED_MODULE_1_jquery___default()('<button type="button" />').text(sIndex);
        }
    }]);

    return Carousel;
}(__WEBPACK_IMPORTED_MODULE_2__models_DefaultComponent__["a" /* BaseComponent */]);

/***/ }),
/* 14 */,
/* 15 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Julia__ = __webpack_require__(16);

new __WEBPACK_IMPORTED_MODULE_0__Julia__["a" /* Julia */]();

/***/ }),
/* 16 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Julia; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_imagesloaded__ = __webpack_require__(9);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_imagesloaded___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_imagesloaded__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_masonry_layout__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_masonry_layout___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_masonry_layout__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_sticky_sidebar__ = __webpack_require__(21);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_select2__ = __webpack_require__(22);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_select2___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_select2__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__components_base_ts_services_global_service__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__components_base_ts_BaseTheme__ = __webpack_require__(25);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__components_base_ts_components_Carousel__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_9__components_base_ts_components_Slideshow__ = __webpack_require__(26);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_10__components_header_ts_StickyHeader__ = __webpack_require__(28);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_11__components_base_ts_components_SearchOverlay__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_12__components_base_ts_components_Recipe__ = __webpack_require__(32);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_13__components_base_ts_components_Gallery__ = __webpack_require__(33);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }















var Julia = function (_BaseTheme) {
    _inherits(Julia, _BaseTheme);

    function Julia() {
        _classCallCheck(this, Julia);

        var _this = _possibleConstructorReturn(this, (Julia.__proto__ || Object.getPrototypeOf(Julia)).call(this));

        _this.Recipe = new __WEBPACK_IMPORTED_MODULE_12__components_base_ts_components_Recipe__["a" /* Recipe */]();
        _this.featuredCarousel = [];
        _this.carousels = [];
        _this.slideshows = [];
        _this.sidebars = [];
        _this.masonrySelector = '.js-masonry, .u-gallery-type--masonry';
        __WEBPACK_IMPORTED_MODULE_5__components_base_ts_services_global_service__["a" /* GlobalService */].onCustomizerRender().subscribe(_this.handleCustomizerChanges.bind(_this));
        _this.handleContent();
        return _this;
    }

    _createClass(Julia, [{
        key: 'bindEvents',
        value: function bindEvents() {
            _get(Julia.prototype.__proto__ || Object.getPrototypeOf(Julia.prototype), 'bindEvents', this).call(this);
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()(document.body).on('post-load', this.onJetpackPostLoad.bind(this));
        }
    }, {
        key: 'onLoadAction',
        value: function onLoadAction() {
            _get(Julia.prototype.__proto__ || Object.getPrototypeOf(Julia.prototype), 'onLoadAction', this).call(this);
            this.Header = new __WEBPACK_IMPORTED_MODULE_10__components_header_ts_StickyHeader__["a" /* StickyHeader */]();
            this.SearchOverlay = new __WEBPACK_IMPORTED_MODULE_11__components_base_ts_components_SearchOverlay__["a" /* SearchOverlay */]();
            Object.assign(window, { StickyHeader: this.Header });
            this.adjustLayout();
            this.initCarousels();
        }
    }, {
        key: 'onResizeAction',
        value: function onResizeAction() {
            _get(Julia.prototype.__proto__ || Object.getPrototypeOf(Julia.prototype), 'onResizeAction', this).call(this);
            this.adjustLayout();
            this.destroySlideshows();
            this.handleSlideshows();
            this.destroyCarousels();
            this.handleCarousels();
            this.initCarousels();
        }
    }, {
        key: 'onJetpackPostLoad',
        value: function onJetpackPostLoad() {
            var $container = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('#posts-container');
            var $newBlocks = $container.children().not('.post--loaded').addClass('post--loaded');
            $newBlocks.imagesLoaded(function () {
                if ($container.hasClass('js-masonry')) {
                    $container.masonry('appended', $newBlocks, true).masonry('layout');
                    __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.infinite-loader').hide();
                }
            });
            this.handleContent($container);
            this.adjustLayout();
        }
    }, {
        key: 'wrapTitle',
        value: function wrapTitle() {
            var $rightSideElement = void 0;
            // get featured image bounding box
            if (this.$body.hasClass('entry-image--portrait')) {
                $rightSideElement = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.entry-thumbnail');
            } else if (this.$body.hasClass('has-sidebar') && this.$body.hasClass('single-post')) {
                $rightSideElement = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.widget-area--post');
            } else {
                return;
            }
            if (!$rightSideElement.length) {
                return;
            }
            var $string = void 0;
            var words = void 0;
            var rightSideElementBox = void 0;
            $string = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.entry-title');
            // split title into words
            words = __WEBPACK_IMPORTED_MODULE_0_jquery___default.a.trim($string.text()).split(' ');
            // empty title container
            $string.empty();
            // wrap each word in a span and add it back to the title container
            // there should be a trailing space after the closing tag
            __WEBPACK_IMPORTED_MODULE_0_jquery___default.a.each(words, function (i, w) {
                $string.append('<span> ' + w + ' </span> ');
            });
            rightSideElementBox = $rightSideElement[0].getBoundingClientRect();
            // loop through each of the newly created spans
            // if it overlaps the bounding box of the featured image add a new line before it
            var $reverseSpans = __WEBPACK_IMPORTED_MODULE_0_jquery___default()($string.find('span').get().reverse());
            $reverseSpans.each(function (i, obj) {
                var $span = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                var spanBox = obj.getBoundingClientRect();
                if (spanBox.bottom > rightSideElementBox.top && spanBox.right > rightSideElementBox.left) {
                    $span.replaceWith(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('<br><span> ' + $span.text() + ' </span>'));
                    return false;
                }
            });
            if (__WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].above('small')) {
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.header-dropcap').each(function (index, element) {
                    __WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].fitText(__WEBPACK_IMPORTED_MODULE_0_jquery___default()(element));
                });
            }
        }
    }, {
        key: 'scaleCardSeparators',
        value: function scaleCardSeparators() {
            // loop through each card
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-card').not('.c-card--related').each(function (i, obj) {
                var $card = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                var $meta = $card.find('.c-meta');
                var $separator = $card.find('.c-meta__separator').hide();
                var width = $card[0].offsetWidth;
                var totalWidth = 0;
                // calculate the sum of the widths of the meta elements
                $meta.children().each(function (j, element) {
                    totalWidth += element.offsetWidth;
                });
                // if there are still at least 14px left, display the separator
                if (totalWidth + 14 <= width) {
                    $separator.show();
                }
            });
        }
    }, {
        key: 'addIsLoadedListener',
        value: function addIsLoadedListener() {
            var $container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.$body;

            // add every image on the page the .is-loaded class
            // after the image has actually loaded
            $container.find('.widget_categories_image_grid, .c-card__frame, .entry-thumbnail').find('img').each(function (i, element) {
                var $each = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(element);
                __WEBPACK_IMPORTED_MODULE_1_imagesloaded__(element, function () {
                    $each.addClass('is-loaded');
                });
                if (__WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].below('pad')) {
                    $each.addClass('is-loaded');
                }
            });
        }
    }, {
        key: 'handleContent',
        value: function handleContent() {
            var $container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.$body;

            this.Recipe.positionPrintBtn();
            this.Recipe.wrapRecipeElements();
            __WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].unwrapImages($container.find('.entry-content'));
            __WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].wrapEmbeds($container.find('.entry-content'));
            __WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].handleVideos($container);
            __WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].handleCustomCSS($container);
            this.wrapTitle();
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.single .entry-content .tiled-gallery').wrap('<div class="aligncenter" />');
            // $container
            //   .find( '.header-dropcap, .post:not(.has-post-thumbnail) .c-card__letter' )
            //   .each( ( index, element ) => {
            //     $( element ).css( 'opacity', 1 );
            //   } );
            this.addIsLoadedListener($container);
            if ($container.hasClass('page-template-front-page')) {
                var $widgetArea = $container.find('.content-area .widget-area');
                var $widget = $widgetArea.children('.widget').first();
                var isFullWidth = $widgetArea.is('.o-layout__full');
                var isProperWidget = $widget.is('.widget_featured_posts_5cards, .widget_featured_posts_6cards, .widget_featured_posts_grid');
                var hasTitle = $widget.children('.widget__title').length > 0;
                if (isFullWidth && isProperWidget && !hasTitle) {
                    this.$body.addClass('has-extended-header-background');
                }
            }
            if ($container.hasClass('blog') && !$container.hasClass('u-site-header-short') && !__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.o-layout__side').length) {
                this.$body.addClass('has-extended-header-background');
            }
            $container.find('.entry-content p').each(function (i, obj) {
                var $p = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                if (!$p.children().length && !__WEBPACK_IMPORTED_MODULE_0_jquery___default.a.trim($p.text()).length) {
                    $p.remove();
                }
            });
            var $commentForm = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.comment-form');
            if ($commentForm.length) {
                var $commentFormFooter = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<p class="comment-form-subscriptions"></p>').appendTo($commentForm);
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.comment-subscription-form').appendTo($commentFormFooter);
            }
            $container.find('.c-gallery').not('.c-gallery--widget, .c-footer__gallery').each(function (index, element) {
                new __WEBPACK_IMPORTED_MODULE_13__components_base_ts_components_Gallery__["a" /* Gallery */](__WEBPACK_IMPORTED_MODULE_0_jquery___default()(element));
            });
            $container.find('.widget_categories select').select2();
            this.handleCarousels();
            this.handleSlideshows();
            __WEBPACK_IMPORTED_MODULE_1_imagesloaded__($container, this.initStickyWidget.bind(this));
            this.eventHandlers($container);
        }
    }, {
        key: 'destroyCarousels',
        value: function destroyCarousels() {
            this.carousels.forEach(function (carousel) {
                carousel.destroy();
            });
            this.carousels = [];
        }
    }, {
        key: 'destroySlideshows',
        value: function destroySlideshows() {
            this.slideshows.forEach(function (slideshow) {
                slideshow.destroy();
            });
            this.slideshows = [];
        }
    }, {
        key: 'handleCarousels',
        value: function handleCarousels() {
            var _this2 = this;

            this.getFeaturedPostsCarousels().forEach(function (carousel) {
                var $carousel = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(carousel);
                _this2.carousels.push(new __WEBPACK_IMPORTED_MODULE_8__components_base_ts_components_Carousel__["a" /* Carousel */]($carousel, $carousel.data()));
            });
        }
    }, {
        key: 'handleSlideshows',
        value: function handleSlideshows() {
            var _this3 = this;

            var blendedSelector = '.blend-with-header';
            var slideshowWidgetSelector = '.widget_featured_posts_slideshow';
            var headerBlendedClass = 'site-header--inverted';
            var $slideshow = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.featured-posts-slideshow');
            var $siteHeader = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.site-header');
            var $blended = $slideshow.filter(blendedSelector).first();
            if ($blended.length) {
                if (__WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].above('lap')) {
                    var $widget = $blended.closest(slideshowWidgetSelector);
                    var $placeholder = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="js-slideshow-placeholder">');
                    $widget.data('placeholder', $placeholder);
                    $placeholder.insertAfter($widget);
                    $widget.appendTo($siteHeader);
                    $siteHeader.addClass(headerBlendedClass);
                } else {
                    $siteHeader.find(slideshowWidgetSelector).each(function (i, obj) {
                        var $widget = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                        var $placeholder = $widget.data('placeholder');
                        $placeholder.replaceWith($widget);
                    });
                    $siteHeader.removeClass(headerBlendedClass);
                }
            }
            $slideshow.each(function (i, obj) {
                var $element = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                _this3.slideshows.push(new __WEBPACK_IMPORTED_MODULE_9__components_base_ts_components_Slideshow__["a" /* Slideshow */]($element.find('.c-hero__slider'), $element.data()));
            });
        }
    }, {
        key: 'positionSidebar',
        value: function positionSidebar() {
            if (this.$body.is('.entry-image--portrait')) {
                var $sidebar = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.widget-area--post');
                var $container = $sidebar.parent();
                var containerOffset = void 0;
                var sidebarHeight = void 0;
                var sidebarOffset = void 0;
                var sidebarBottom = void 0;
                if (!$container.length || !$sidebar.length) {
                    return;
                }
                // remove possible properties set on prior calls of this function
                $container.css({
                    minHeight: '',
                    position: ''
                });
                $sidebar.css({
                    bottom: '',
                    position: '',
                    right: '',
                    top: ''
                });
                if (__WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].below('pad')) {
                    return;
                }
                containerOffset = $container.offset();
                sidebarHeight = $sidebar.outerHeight();
                sidebarOffset = $sidebar.offset();
                sidebarBottom = $container.outerHeight() > sidebarHeight ? 0 : '';
                $container.css({
                    minHeight: sidebarHeight + sidebarOffset.top - containerOffset.top,
                    position: 'relative'
                });
                $sidebar.css({
                    bottom: sidebarBottom,
                    position: 'absolute',
                    right: 0,
                    top: sidebarOffset.top - containerOffset.top
                });
            }
        }
    }, {
        key: 'getFeaturedPostsCarousels',
        value: function getFeaturedPostsCarousels() {
            return [].concat(_toConsumableArray(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.featured-posts-carousel')));
        }
    }, {
        key: 'handleCustomizerChanges',
        value: function handleCustomizerChanges(element) {
            this.addIsLoadedListener();
            if (element.hasClass('widget_featured_posts_slideshow')) {
                this.handleSlideshowsReload();
            }
            if (element.find('.featured-posts-carousel').length) {
                this.handleCarouselsReload();
            }
            if (element['selector'] === '') {
                this.handleSlideshowsReload();
            }
        }
    }, {
        key: 'handleCarouselsReload',
        value: function handleCarouselsReload() {
            this.destroyCarousels();
            this.handleCarousels();
        }
    }, {
        key: 'handleSlideshowsReload',
        value: function handleSlideshowsReload() {
            this.destroySlideshows();
            this.handleSlideshows();
        }
    }, {
        key: 'initStickyWidget',
        value: function initStickyWidget() {
            var _this4 = this;

            var sidebars = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.widget-area--side');
            __WEBPACK_IMPORTED_MODULE_1_imagesloaded__(sidebars, function () {
                _this4.positionSidebar();
                sidebars.each(function (index, sidebar) {
                    var lastWidget = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(sidebar).find('.widget').last();
                    if (lastWidget.length === 0) {
                        return;
                    }
                    lastWidget.wrap('<div class="sticky-sidebar"></div>').wrap('<div class="sticky-sidebar__inner"></div>');
                    var headerHeight = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.u-site-header-sticky .site-header-sticky').outerHeight() || 0;
                    var adminBarHeight = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('#wpadminbar').outerHeight() || 0;
                    var margin = parseInt(lastWidget.find('sticky-sidebar').css('marginTop'), 10) || 56;
                    var offset = headerHeight + adminBarHeight + margin;
                    _this4.sidebars.push(new __WEBPACK_IMPORTED_MODULE_3_sticky_sidebar__["a" /* default */](__WEBPACK_IMPORTED_MODULE_0_jquery___default()(sidebar).find('.sticky-sidebar__inner')[0], {
                        bottomSpacing: margin,
                        containerSelector: '.sticky-sidebar',
                        innerWrapperSelector: '.widget',
                        topSpacing: offset
                    }));
                });
            });
        }
    }, {
        key: 'changeHeaderDropcapOpacity',
        value: function changeHeaderDropcapOpacity() {
            if (__WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].above('small')) {
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.header-dropcap, .post:not(.has-post-thumbnail) .c-card__letter').each(function (index, element) {
                    __WEBPACK_IMPORTED_MODULE_0_jquery___default()(element).css('opacity', 1);
                });
            }
        }
    }, {
        key: 'adjustLayout',
        value: function adjustLayout() {
            this.applyMasonryOnGallery();
            this.changeHeaderDropcapOpacity();
            this.wrapTitle();
            this.scaleCardSeparators();
            // If the branding happens to be in the Left Zone (no Top Menu set), move it in the middle zone
            var $headerNav = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.header.nav');
            if ($headerNav.parent().hasClass('c-navbar__zone--left')) {
                $headerNav.appendTo('.c-navbar__zone--middle');
            }
            // If the Top Menu is not present, ensure that items in the left zone are aligned to the right
            if (__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.menu--secondary').length === 0) {
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-navbar__zone--left').addClass('u-justify-end');
            }
            this.wrapContentImages();
            setTimeout(this.modifyHeaderDropcap.bind(this), 100);
            // this.modifyHeaderDropcap();
        }
    }, {
        key: 'applyMasonryOnGallery',
        value: function applyMasonryOnGallery() {
            var $gallery = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(this.masonrySelector);
            $gallery.each(function (i, obj) {
                var $obj = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                $obj.children().addClass('post--loaded');
                __WEBPACK_IMPORTED_MODULE_1_imagesloaded__($obj, function () {
                    new __WEBPACK_IMPORTED_MODULE_2_masonry_layout__($obj.get(0), { transitionDuration: 0 });
                });
            });
        }
    }, {
        key: 'wrapContentImages',
        value: function wrapContentImages() {
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.entry-content').find('figure').filter('.aligncenter, .alignnone').each(function (index, element) {
                var $figure = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(element);
                var $image = $figure.find('img');
                var figureWidth = $figure.outerWidth();
                var imageWidth = $image.outerWidth();
                if (imageWidth < figureWidth) {
                    $figure.wrap('<p>');
                }
            });
        }
    }, {
        key: 'modifyHeaderDropcap',
        value: function modifyHeaderDropcap() {
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.header-dropcap, .c-card__letter').each(function (index, element) {
                //__WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].fitText(__WEBPACK_IMPORTED_MODULE_0_jquery___default()(element));
            });
        }
    }, {
        key: 'initCarousels',
        value: function initCarousels() {
            var _this5 = this;

            if (this.featuredCarousel.length > 0) {
                return;
            }
            if (__WEBPACK_IMPORTED_MODULE_6__components_base_ts_services_Helper__["a" /* Helper */].above('pad')) {
                return;
            }
            this.getXCardsCarousels().forEach(function (element) {
                _this5.initXCardsCarousel(__WEBPACK_IMPORTED_MODULE_0_jquery___default()(element));
            });
            this.getSlideshows().forEach(function (element) {
                _this5.initSlideshowCarousel(__WEBPACK_IMPORTED_MODULE_0_jquery___default()(element));
            });
        }
    }, {
        key: 'initSlideshowCarousel',
        value: function initSlideshowCarousel($element) {
            var $slides = $element.find('.c-hero__slide');
            var $elementClone = $element.clone().empty().removeAttr('id').addClass('featured-posts-cards--mobile');
            var newHTML = '';
            $slides.each(function (i, obj) {
                var $slide = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(obj);
                var $image = $slide.find('.c-hero__image').first();
                var $meta = $slide.find('.c-meta');
                var title = $slide.find('.c-hero__title-mask h2').text();
                var $excerpt = $slide.find('.c-hero__excerpt').html();
                var link = $slide.find('.c-hero__link').attr('href');
                var $cardImage = $image.clone().removeClass('c-hero__image');
                var $cardFrame = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card__frame">');
                var $cardLetter = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card__letter">' + title.charAt(0) + '</div>');
                var $cardAside = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card__aside c-card__thumbnail-background"></div>');
                var $cardMeta = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card__meta">').append($meta);
                var $cardTitle = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card__title"><span>' + title + '</span></div>');
                var $cardExcerpt = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card__excerpt">').append($excerpt);
                var $cardLink = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<a class="c-card__link" href="' + link + '"></a>');
                var $card = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card"></div>');
                var $cardContent = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="c-card__content"></div>');
                $cardFrame.append($cardImage, $cardLetter);
                $cardAside.append($cardFrame);
                $cardContent.append($cardMeta, $cardTitle, $cardExcerpt);
                $card.append($cardAside, $cardContent, $cardLink);
                newHTML += $card.wrap('<article>').parent().prop('outerHTML');
            });
            $elementClone.html(newHTML).insertAfter($element);
            this.featuredCarousel.push(new __WEBPACK_IMPORTED_MODULE_8__components_base_ts_components_Carousel__["a" /* Carousel */]($elementClone, { show_pagination: '' }));
        }
    }, {
        key: 'initXCardsCarousel',
        value: function initXCardsCarousel($element) {
            var $articles = [].concat(_toConsumableArray($element.find('.posts-wrapper--main').find('article').clone()), _toConsumableArray($element.find('.posts-wrapper--left').find('article').clone()), _toConsumableArray($element.find('.posts-wrapper--right').find('article').clone()));
            var $elementClone = $element.clone().empty().append($articles).addClass('featured-posts-cards--mobile');
            $element.addClass('featured-posts-cards--desktop');
            $element.parent().append($elementClone);
            this.featuredCarousel.push(new __WEBPACK_IMPORTED_MODULE_8__components_base_ts_components_Carousel__["a" /* Carousel */]($elementClone, { show_pagination: '' }));
        }
    }, {
        key: 'getSlideshows',
        value: function getSlideshows() {
            return [].concat(_toConsumableArray(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.widget_featured_posts_slideshow')));
        }
    }, {
        key: 'getXCardsCarousels',
        value: function getXCardsCarousels() {
            return [].concat(_toConsumableArray(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.featured-posts-5cards')), _toConsumableArray(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.featured-posts-6cards')));
        }
    }]);

    return Julia;
}(__WEBPACK_IMPORTED_MODULE_7__components_base_ts_BaseTheme__["a" /* BaseTheme */]);

/***/ }),
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return BaseTheme; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__services_Helper__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_window_service__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__services_global_service__ = __webpack_require__(6);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }





var BaseTheme = function () {
    function BaseTheme() {
        _classCallCheck(this, BaseTheme);

        this.$body = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('body');
        this.$window = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(window);
        this.$html = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('html');
        this.ev = __WEBPACK_IMPORTED_MODULE_0_jquery___default()({});
        this.frameRendered = false;
        this.subscriptionActive = true;
        this.$html.toggleClass('is-IE', __WEBPACK_IMPORTED_MODULE_1__services_Helper__["a" /* Helper */].getIEversion() && __WEBPACK_IMPORTED_MODULE_1__services_Helper__["a" /* Helper */].getIEversion() < 12);
        this.bindEvents();
        this.renderLoop();
    }

    _createClass(BaseTheme, [{
        key: 'bindEvents',
        value: function bindEvents() {
            __WEBPACK_IMPORTED_MODULE_3__services_global_service__["a" /* GlobalService */].onReady().take(1).subscribe(this.onReadyAction.bind(this));
            __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].onLoad().take(1).subscribe(this.onLoadAction.bind(this));
            __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].onResize().debounce(500).subscribe(this.onResizeAction.bind(this));
            __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].onScroll().subscribe(this.onScrollAction.bind(this));
            // Leave comments area visible by default and
            // show it only if the URL links to a comment
            if (window.location.href.indexOf('#comment') === -1) {
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.trigger-comments').removeAttr('checked');
            }
            this.$window.on('beforeunload', this.fadeOut.bind(this));
            this.ev.on('render', this.update.bind(this));
        }
    }, {
        key: 'onScrollAction',
        value: function onScrollAction() {
            this.frameRendered = false;
        }
    }, {
        key: 'onReadyAction',
        value: function onReadyAction() {
            this.$html.addClass('is-ready');
        }
    }, {
        key: 'onLoadAction',
        value: function onLoadAction() {
            this.$html.addClass('is-loaded');
            this.fadeIn();
        }
    }, {
        key: 'onResizeAction',
        value: function onResizeAction() {}
    }, {
        key: 'destroy',
        value: function destroy() {
            this.subscriptionActive = false;
        }
    }, {
        key: 'renderLoop',
        value: function renderLoop() {
            var _this = this;

            if (this.frameRendered === false) {
                this.ev.trigger('render');
            }
            requestAnimationFrame(function () {
                _this.renderLoop();
                _this.frameRendered = true;
                _this.ev.trigger('afterRender');
            });
        }
    }, {
        key: 'update',
        value: function update() {
            this.backToTop();
        }
    }, {
        key: 'backToTop',
        value: function backToTop() {
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.back-to-top').toggleClass('is-visible', __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].getScrollY() >= __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].getHeight());
        }
    }, {
        key: 'eventHandlers',
        value: function eventHandlers($container) {
            $container.find('.back-to-top').on('click', function (e) {
                e.preventDefault();
                __WEBPACK_IMPORTED_MODULE_1__services_Helper__["a" /* Helper */].smoothScrollTo(0, 1000);
            });
        }
    }, {
        key: 'fadeOut',
        value: function fadeOut() {
            this.$html.removeClass('fade-in').addClass('fade-out');
        }
    }, {
        key: 'fadeIn',
        value: function fadeIn() {
            this.$html.removeClass('fade-out no-transitions').addClass('fade-in');
        }
    }]);

    return BaseTheme;
}();

/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Slideshow; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_slick_carousel__ = __webpack_require__(14);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_slick_carousel___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_slick_carousel__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__Carousel__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_gsap__ = __webpack_require__(27);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_gsap___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_gsap__);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _get = function get(object, property, receiver) { if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }





var Slideshow = function (_Carousel) {
    _inherits(Slideshow, _Carousel);

    function Slideshow(element) {
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

        _classCallCheck(this, Slideshow);

        return _possibleConstructorReturn(this, (Slideshow.__proto__ || Object.getPrototypeOf(Slideshow)).call(this, element, options));
    }

    _createClass(Slideshow, [{
        key: 'destroy',
        value: function destroy() {
            _get(Slideshow.prototype.__proto__ || Object.getPrototypeOf(Slideshow.prototype), 'destroy', this).call(this);
            this.element.off('beforeChange');
        }
    }, {
        key: 'bindEvents',
        value: function bindEvents() {
            this.element.on('beforeChange', this.onBeforeSlideChange.bind(this));
            this.slickOptions = Object.assign({}, this.slickOptions, {
                fade: true,
                infinite: true,
                speed: 1000
            });
            this.element.slick(this.slickOptions);
        }
    }, {
        key: 'onResize',
        value: function onResize() {
            console.warn('slideshow:resize');
            this.destroy();
            this.extendOptions();
            this.bindEvents();
        }
    }, {
        key: 'onBeforeSlideChange',
        value: function onBeforeSlideChange(event, slick, currentSlide, nextSlide) {
            var $currentSlide = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(slick.$slides[currentSlide]);
            var $nextSlide = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(slick.$slides[nextSlide]);
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()(slick.$slides).css('zIndex', 800);
            this.transition($currentSlide, $nextSlide, this.getDirection(slick, currentSlide, nextSlide));
        }
    }, {
        key: 'transition',
        value: function transition($current, $next) {
            var sign = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1;

            var timeline = new __WEBPACK_IMPORTED_MODULE_3_gsap__["TimelineMax"]({ paused: true });
            var duration = this.slickOptions.speed / 1000;
            var slideWidth = $current.outerWidth();
            var move = 300;
            timeline.fromTo($next, duration, { x: sign * slideWidth }, { x: 0, ease: __WEBPACK_IMPORTED_MODULE_3_gsap__["Quart"].easeInOut }, 0);
            timeline.fromTo($next.find('.c-hero__background'), duration, { x: -sign * (slideWidth - move) }, { x: 0, ease: __WEBPACK_IMPORTED_MODULE_3_gsap__["Quart"].easeInOut }, 0);
            timeline.fromTo($next.find('.c-hero__content'), duration, { x: -sign * slideWidth }, { x: 0, ease: __WEBPACK_IMPORTED_MODULE_3_gsap__["Quart"].easeInOut }, 0);
            timeline.fromTo($current, duration, { x: 0 }, { x: -sign * slideWidth, ease: __WEBPACK_IMPORTED_MODULE_3_gsap__["Quart"].easeInOut }, 0);
            timeline.fromTo($current.find('.c-hero__background'), duration, { x: 0 }, { x: sign * (slideWidth - move), ease: __WEBPACK_IMPORTED_MODULE_3_gsap__["Quart"].easeInOut }, 0);
            timeline.fromTo($current.find('.c-hero__content'), duration, { x: 0 }, { x: sign * slideWidth, ease: __WEBPACK_IMPORTED_MODULE_3_gsap__["Quart"].easeInOut }, 0);
            timeline.play();
        }
    }, {
        key: 'getDirection',
        value: function getDirection(slick, currentSlide, nextSlide) {
            if (nextSlide > currentSlide) {
                return 1;
            }
            return -1;
        }
    }]);

    return Slideshow;
}(__WEBPACK_IMPORTED_MODULE_2__Carousel__["a" /* Carousel */]);

/***/ }),
/* 27 */,
/* 28 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return StickyHeader; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_imagesloaded__ = __webpack_require__(9);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_imagesloaded___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_imagesloaded__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_jquery_hoverintent__ = __webpack_require__(29);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_jquery_hoverintent___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_jquery_hoverintent__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__base_ts_models_DefaultComponent__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__base_ts_components_ProgressBar__ = __webpack_require__(30);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__base_ts_services_window_service__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__base_ts_services_Helper__ = __webpack_require__(2);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }








var StickyHeader = function (_BaseComponent) {
    _inherits(StickyHeader, _BaseComponent);

    function StickyHeader() {
        _classCallCheck(this, StickyHeader);

        var _this = _possibleConstructorReturn(this, (StickyHeader.__proto__ || Object.getPrototypeOf(StickyHeader)).call(this));

        _this.$body = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('body');
        _this.$document = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(document);
        _this.$mainMenu = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.menu--primary');
        _this.$mainMenuItems = _this.$mainMenu.find('li');
        _this.$readingBar = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.js-reading-bar');
        _this.$stickyHeader = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.js-site-header-sticky');
        _this.$menuToggle = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('#menu-toggle');
        _this.isStickyHeaderEnabled = _this.$body.hasClass('u-site-header-sticky');
        _this.isSingular = _this.$body.hasClass('single');
        _this.isMobileHeaderInitialised = false;
        _this.isDesktopHeaderInitialised = false;
        _this.areMobileBindingsDone = false;
        _this.stickyHeaderShown = false;
        _this.hideReadingBar = _this.$readingBar !== null;
        _this.currentScrollPosition = 0;
        _this.initialMenuOffset = 0;
        _this.subscriptionActive = true;
        _this.preventOneSelector = 'a.prevent-one';
        __WEBPACK_IMPORTED_MODULE_1_imagesloaded__(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-navbar .c-logo'), function () {
            _this.bindEvents();
            _this.eventHandlers();
            _this.appendSearchTrigger();
            _this.updateOnResize();
            _this.refresh(__WEBPACK_IMPORTED_MODULE_5__base_ts_services_window_service__["a" /* WindowService */].getScrollY());
        });
        return _this;
    }

    _createClass(StickyHeader, [{
        key: 'destroy',
        value: function destroy() {
            this.subscriptionActive = false;
        }
    }, {
        key: 'bindEvents',
        value: function bindEvents() {
            var _this2 = this;

            if (this.$mainMenu.length === 1) {
                this.$document.on('click', '.js-sticky-menu-trigger', this.onClickStickyMenu.bind(this));
            }
            this.$menuToggle.on('change', this.onMenuToggleChange.bind(this));
            this.$mainMenuItems.hoverIntent({
                out: function out(e) {
                    return _this2.toggleSubMenu(e, false);
                },
                over: function over(e) {
                    return _this2.toggleSubMenu(e, true);
                },
                timeout: 300
            });
            __WEBPACK_IMPORTED_MODULE_5__base_ts_services_window_service__["a" /* WindowService */].onScroll().takeWhile(function () {
                return _this2.subscriptionActive;
            }).map(function () {
                return __WEBPACK_IMPORTED_MODULE_5__base_ts_services_window_service__["a" /* WindowService */].getScrollY();
            }).subscribe(function (scrollPosition) {
                _this2.refresh(scrollPosition);
            });
            __WEBPACK_IMPORTED_MODULE_5__base_ts_services_window_service__["a" /* WindowService */].onResize().takeWhile(function () {
                return _this2.subscriptionActive;
            }).subscribe(function () {
                _this2.updateOnResize();
            });
        }
    }, {
        key: 'eventHandlers',
        value: function eventHandlers() {
            if (__WEBPACK_IMPORTED_MODULE_6__base_ts_services_Helper__["a" /* Helper */].below('lap') && !this.areMobileBindingsDone) {
                this.$document.on('click', this.preventOneSelector, this.onMobileMenuExpand.bind(this));
                this.areMobileBindingsDone = true;
            }
            if (__WEBPACK_IMPORTED_MODULE_6__base_ts_services_Helper__["a" /* Helper */].above('lap') && this.areMobileBindingsDone) {
                this.$document.off('click', this.preventOneSelector, this.onMobileMenuExpand.bind(this));
                this.areMobileBindingsDone = false;
            }
        }
    }, {
        key: 'onMobileMenuExpand',
        value: function onMobileMenuExpand(e) {
            e.preventDefault();
            e.stopPropagation();
            var $button = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(e.currentTarget);
            var activeClass = 'active';
            var hoverClass = 'hover';
            if ($button.hasClass(activeClass)) {
                window.location.href = $button.attr('href');
                return;
            }
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()(this.preventOneSelector).removeClass(activeClass);
            $button.addClass(activeClass);
            // When a parent menu item is activated,
            // close other menu items on the same level
            $button.parent().siblings().removeClass(hoverClass);
            // Open the sub menu of this parent item
            $button.parent().addClass(hoverClass);
        }
    }, {
        key: 'onMenuToggleChange',
        value: function onMenuToggleChange(e) {
            var _this3 = this;

            var isMenuOpen = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(e.currentTarget).prop('checked');
            this.$body.toggleClass('nav--is-open', isMenuOpen);
            if (!isMenuOpen) {
                setTimeout(function () {
                    // Close the open submenus in the mobile menu overlay
                    _this3.$mainMenuItems.removeClass('hover');
                    _this3.$mainMenuItems.find('a').removeClass('active');
                }, 300);
            }
        }
    }, {
        key: 'toggleSubMenu',
        value: function toggleSubMenu(e, toggle) {
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()(e.currentTarget).toggleClass('hover', toggle);
        }
    }, {
        key: 'refresh',
        value: function refresh() {
            var scrollPosition = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;

            this.shouldUpdate = this.$body.is('.u-site-header-sticky');
            if (__WEBPACK_IMPORTED_MODULE_6__base_ts_services_Helper__["a" /* Helper */].below('lap')) {
                this.shouldUpdate = false;
            }
            this.updateOnScroll(scrollPosition);
        }
    }, {
        key: 'prepareDesktopMenuMarkup',
        value: function prepareDesktopMenuMarkup() {
            if (this.isDesktopHeaderInitialised) {
                return;
            }
            var htmlTop = parseInt(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('html').css('marginTop'), 10);
            this.$stickyHeader.css('top', htmlTop);
            // Figure out where is the offset of the Main Menu.
            // If there is no Main Menu set, show the reading bar
            // after passing the branding.
            if (this.$mainMenu.length === 1) {
                this.initialMenuOffset = this.$mainMenu.offset().top - htmlTop;
            } else {
                var $branding = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-branding');
                this.initialMenuOffset = $branding.offset().top + $branding.outerHeight();
            }
            // Fallback to the other, secondary menu (top left one).
            if (this.$mainMenu.length === 0) {
                this.$mainMenu = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.menu--secondary');
            }
            // If there is a menu, either the "true" main one or the fallback one,
            // clone it and append it to the reading bar.
            if (this.$mainMenu.length === 1) {
                this.$mainMenu = this.$mainMenu.clone(true, true).appendTo(this.$stickyHeader.find('.c-navbar'));
            }
            this.$stickyHeader.find('.c-navbar').css('height', this.$stickyHeader.height());
            // this.$readingBar = null;
            // this.$progressBar = null;
            this.prepareSingleHeader();
            this.refresh();
            this.isDesktopHeaderInitialised = true;
        }
    }, {
        key: 'prepareMobileMenuMarkup',
        value: function prepareMobileMenuMarkup() {
            // If if has not been done yet, prepare the mark-up for the mobile navigation
            if (!this.isMobileHeaderInitialised) {
                // Append the branding
                var $branding = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-branding');
                var $navbarZone = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-navbar__zone--right');
                $branding.clone().addClass('c-branding--mobile').appendTo('.c-navbar');
                $branding.find('img').removeClass('is--loading');
                // Create the mobile site header
                var $siteHeaderMobile = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('<div class="site-header-mobile u-container-sides-spacing"></div>').appendTo('.c-navbar');
                // Append the social menu
                var $socialMenu = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-navbar__zone--left .jetpack-social-navigation').clone();
                var $searchTrigger = $socialMenu.find('.js-search-trigger').parent().clone();
                $navbarZone.append($socialMenu);
                $navbarZone.find('.js-search-trigger').parent().remove();
                $siteHeaderMobile.append($socialMenu.empty().append($searchTrigger));
                // Handle sub menus:
                // Make sure there are no open menu items
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.menu-item-has-children').removeClass('hover');
                // Add a class so we know the items to handle
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.menu-item-has-children > a').each(function (index, element) {
                    __WEBPACK_IMPORTED_MODULE_0_jquery___default()(element).addClass('prevent-one');
                });
                // Replace the label text and make it visible
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-navbar__label-text ').html(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.js-menu-mobile-label').html()).removeClass('screen-reader-text');
                this.isMobileHeaderInitialised = true;
            }
        }
    }, {
        key: 'prepareSingleHeader',
        value: function prepareSingleHeader() {
            if (!this.isSingular || !this.isStickyHeaderEnabled) {
                return;
            }
            __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-reading-bar__wrapper-social').find('.share-end').remove();
            var entryHeader = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.entry-header');
            var entryContent = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.single-main').find('.entry-content');
            var entryHeaderHeight = entryHeader.outerHeight() || 0;
            var entryContentHeight = entryContent.outerHeight() || 0;
            var articleHeight = entryHeaderHeight + entryContentHeight;
            if (this.$body.hasClass('entry-image--landscape')) {
                articleHeight = articleHeight + __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.entry-thumbnail').outerHeight();
            }
            this.ProgressBar = new __WEBPACK_IMPORTED_MODULE_4__base_ts_components_ProgressBar__["a" /* ProgressBar */]({
                canShow: this.isSingular,
                max: entryHeader.offset().top + articleHeight - this.initialMenuOffset,
                offset: this.initialMenuOffset
            });
            if (this.$mainMenu.length !== 1) {
                __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.js-sticky-menu-trigger').remove();
            }
        }
    }, {
        key: 'updateOnScroll',
        value: function updateOnScroll() {
            var _this4 = this;

            var scrollPosition = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;

            if (!this.shouldUpdate) {
                return;
            }
            var showSticky = scrollPosition > this.initialMenuOffset;
            var hideReadingBar = scrollPosition < this.currentScrollPosition && this.$mainMenu.length === 1;
            if (this.$readingBar !== null && hideReadingBar !== this.hideReadingBar) {
                clearTimeout(this.overflowTimeout);
                if (!hideReadingBar) {
                    if (this.$readingBar.length) {
                        this.$stickyHeader.css('overflow', 'hidden');
                    }
                } else {
                    this.overflowTimeout = setTimeout(function () {
                        _this4.$stickyHeader.css('overflow', '');
                    }, 350);
                }
                this.$stickyHeader.toggleClass('reading-bar--hide', hideReadingBar);
                this.hideReadingBar = hideReadingBar;
            }
            if (this.ProgressBar && null !== this.$readingBar) {
                this.$readingBar.toggleClass('show-next-title', this.ProgressBar.isCloseToEnd());
            }
            if (showSticky !== this.stickyHeaderShown) {
                this.$stickyHeader.toggleClass('site-header-sticky--show', showSticky);
                this.stickyHeaderShown = showSticky;
            }
            this.currentScrollPosition = scrollPosition;
        }
    }, {
        key: 'updateOnResize',
        value: function updateOnResize() {
            this.eventHandlers();
            if (__WEBPACK_IMPORTED_MODULE_6__base_ts_services_Helper__["a" /* Helper */].below('lap')) {
                this.prepareMobileMenuMarkup();
            } else {
                this.prepareDesktopMenuMarkup();
            }
        }
    }, {
        key: 'onClickStickyMenu',
        value: function onClickStickyMenu() {
            var _this5 = this;

            this.$stickyHeader.addClass('reading-bar--hide');
            setTimeout(function () {
                _this5.$stickyHeader.css('overflow', '');
            }, 350);
        }
    }, {
        key: 'appendSearchTrigger',
        value: function appendSearchTrigger() {
            var $headerSocialNavigation = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-navbar__zone--left .jetpack-social-navigation');
            this.$searchTrigger = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.js-search-trigger').removeClass('u-hidden');
            // Append the search trigger either to the social navigation
            if ($headerSocialNavigation.length === 1) {
                this.$searchTrigger.clone().wrap('<li class="menu-item"></li>').parent().appendTo($headerSocialNavigation.find('.menu'));
            } else {
                // Or directly to zone left if there is no social navigation
                this.$searchTrigger.clone().appendTo(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-navbar__zone--left'));
            }
            this.$searchTrigger.clone().appendTo(__WEBPACK_IMPORTED_MODULE_0_jquery___default()('.site-header-sticky .c-navbar'));
            this.$searchTrigger.remove();
        }
    }]);

    return StickyHeader;
}(__WEBPACK_IMPORTED_MODULE_3__base_ts_models_DefaultComponent__["a" /* BaseComponent */]);

/***/ }),
/* 29 */,
/* 30 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ProgressBar; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__models_DefaultComponent__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_window_service__ = __webpack_require__(3);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }




var ProgressBar = function (_BaseComponent) {
    _inherits(ProgressBar, _BaseComponent);

    function ProgressBar(options) {
        _classCallCheck(this, ProgressBar);

        var _this = _possibleConstructorReturn(this, (ProgressBar.__proto__ || Object.getPrototypeOf(ProgressBar)).call(this));

        _this.$progressBar = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.js-reading-progress');
        _this.subscriptionActive = true;
        _this.scrollPosition = 0;
        _this.max = 0;
        _this.setOptions(options);
        _this.init();
        _this.bindEvents();
        return _this;
    }

    _createClass(ProgressBar, [{
        key: 'init',
        value: function init() {
            this.max = this.options.max > __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].getHeight() ? this.options.max - __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].getHeight() : this.options.max;
            this.$progressBar.attr('max', this.max);
        }
    }, {
        key: 'destroy',
        value: function destroy() {
            this.subscriptionActive = false;
        }
    }, {
        key: 'bindEvents',
        value: function bindEvents() {
            var _this2 = this;

            __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].onScroll().takeWhile(function () {
                return _this2.subscriptionActive;
            }).subscribe(function () {
                _this2.onScroll();
            });
        }
    }, {
        key: 'change',
        value: function change(value) {
            this.$progressBar.attr('value', value);
        }
    }, {
        key: 'setOptions',
        value: function setOptions(options) {
            this.options = Object.assign({}, this.options, options);
        }
    }, {
        key: 'isCloseToEnd',
        value: function isCloseToEnd() {
            return this.max <= this.scrollPosition - this.options.offset;
        }
    }, {
        key: 'onScroll',
        value: function onScroll() {
            this.scrollPosition = __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].getScrollY();
            if (this.options.canShow && this.scrollPosition > this.options.offset) {
                this.change(this.scrollPosition - this.options.offset);
            }
        }
    }]);

    return ProgressBar;
}(__WEBPACK_IMPORTED_MODULE_1__models_DefaultComponent__["a" /* BaseComponent */]);

/***/ }),
/* 31 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return SearchOverlay; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rx_dom__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rx_dom___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_rx_dom__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__models_DefaultComponent__ = __webpack_require__(1);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }




var activeClass = 'show-search-overlay';
var openClass = '.js-search-trigger';
var closeClass = '.js-search-close';
var escKeyCode = 27;
var SearchOverlay = function (_BaseComponent) {
    _inherits(SearchOverlay, _BaseComponent);

    function SearchOverlay() {
        _classCallCheck(this, SearchOverlay);

        var _this = _possibleConstructorReturn(this, (SearchOverlay.__proto__ || Object.getPrototypeOf(SearchOverlay)).call(this));

        _this.$body = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('body');
        _this.$document = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(document);
        _this.$searchField = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('.c-search-overlay').find('.search-field');
        _this.subscriptionActive = true;
        _this.keyupSubscriptionActive = true;
        _this.bindEvents();
        return _this;
    }

    _createClass(SearchOverlay, [{
        key: 'destroy',
        value: function destroy() {
            this.subscriptionActive = false;
            this.keyupSubscriptionActive = false;
            this.$document.off('click.SearchOverlay');
        }
    }, {
        key: 'bindEvents',
        value: function bindEvents() {
            var _this2 = this;

            this.$document.on('click.SearchOverlay', openClass, this.open.bind(this));
            this.closeSub = __WEBPACK_IMPORTED_MODULE_1_rx_dom__["DOM"].click(document.querySelector(closeClass));
            this.keyupSub = __WEBPACK_IMPORTED_MODULE_1_rx_dom__["DOM"].keyup(document.querySelector('body'));
            this.closeSub.takeWhile(function () {
                return _this2.subscriptionActive;
            }).subscribe(this.close.bind(this));
        }
    }, {
        key: 'createKeyupSubscription',
        value: function createKeyupSubscription() {
            var _this3 = this;

            this.keyupSubscriptionActive = true;
            this.keyupSub.takeWhile(function () {
                return _this3.keyupSubscriptionActive;
            }).subscribe(this.closeOnEsc.bind(this));
        }
    }, {
        key: 'open',
        value: function open() {
            this.$searchField.focus();
            this.$body.addClass(activeClass);
            this.createKeyupSubscription();
        }
    }, {
        key: 'close',
        value: function close() {
            this.$body.removeClass(activeClass);
            this.$searchField.blur();
            this.keyupSubscriptionActive = false;
        }
    }, {
        key: 'closeOnEsc',
        value: function closeOnEsc(e) {
            if (e.keyCode === escKeyCode) {
                this.close();
            }
        }
    }]);

    return SearchOverlay;
}(__WEBPACK_IMPORTED_MODULE_2__models_DefaultComponent__["a" /* BaseComponent */]);

/***/ }),
/* 32 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Recipe; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__models_DefaultComponent__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_Helper__ = __webpack_require__(2);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }




var jetpackRecipeClass = 'jetpack-recipe';
var jetpackRecipePrintClass = 'jetpack-recipe-print';
var jetpackRecipeContentClass = 'jetpack-recipe-content';
var jetpackRecipeIngredientsClass = 'jetpack-recipe-ingredients';
var Recipe = function (_BaseComponent) {
    _inherits(Recipe, _BaseComponent);

    function Recipe() {
        _classCallCheck(this, Recipe);

        var _this = _possibleConstructorReturn(this, (Recipe.__proto__ || Object.getPrototypeOf(Recipe)).call(this));

        _this.$body = __WEBPACK_IMPORTED_MODULE_0_jquery___default()('body');
        _this.bindEvents();
        return _this;
    }

    _createClass(Recipe, [{
        key: 'destroy',
        value: function destroy() {}
    }, {
        key: 'bindEvents',
        value: function bindEvents() {}
    }, {
        key: 'positionPrintBtn',
        value: function positionPrintBtn() {
            var $container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.$body;

            $container.find('.' + jetpackRecipeClass).each(function (index, element) {
                var $recipe = __WEBPACK_IMPORTED_MODULE_0_jquery___default()(element);
                var $print = $recipe.find('.' + jetpackRecipePrintClass);
                var $recipeContent = $recipe.find('.' + jetpackRecipeContentClass);
                var $ingredients = $recipe.find('.' + jetpackRecipeIngredientsClass);
                $print.find('a').clone(true).appendTo($recipeContent).wrap('<div class="' + jetpackRecipePrintClass + '"></div>');
                if ($ingredients.length) {
                    $recipeContent.find('.' + jetpackRecipePrintClass).addClass('jetpack-has-ingredients');
                }
                $print.remove();
            });
        }
    }, {
        key: 'wrapRecipeElements',
        value: function wrapRecipeElements() {
            var $container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.$body;

            $container.find('.jetpack-recipe-image').wrap('<div class="jetpack-recipe-image-container"></div>');
            $container.find('.' + jetpackRecipeIngredientsClass + ' ul > li').each(function (index, element) {
                __WEBPACK_IMPORTED_MODULE_2__services_Helper__["a" /* Helper */].markFirstWord(__WEBPACK_IMPORTED_MODULE_0_jquery___default()(element));
            });
        }
    }]);

    return Recipe;
}(__WEBPACK_IMPORTED_MODULE_1__models_DefaultComponent__["a" /* BaseComponent */]);

/***/ }),
/* 33 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Gallery; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_masonry_layout__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_masonry_layout___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_masonry_layout__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__models_DefaultComponent__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_window_service__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__services_global_service__ = __webpack_require__(6);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }





var Gallery = function (_BaseComponent) {
    _inherits(Gallery, _BaseComponent);

    function Gallery(element) {
        _classCallCheck(this, Gallery);

        var _this = _possibleConstructorReturn(this, (Gallery.__proto__ || Object.getPrototypeOf(Gallery)).call(this));

        _this.subscriptionActive = true;
        _this.masonryGallerySelector = '.c-gallery--packed, .c-gallery--masonry';
        _this.element = element;
        _this.layout();
        __WEBPACK_IMPORTED_MODULE_2__services_window_service__["a" /* WindowService */].onResize().debounce(300).takeWhile(function () {
            return _this.subscriptionActive;
        }).subscribe(function () {
            _this.layout();
        });
        __WEBPACK_IMPORTED_MODULE_3__services_global_service__["a" /* GlobalService */].onCustomizerRender().debounce(300).takeWhile(function () {
            return _this.subscriptionActive;
        }).subscribe(function () {
            _this.layout();
        });
        __WEBPACK_IMPORTED_MODULE_3__services_global_service__["a" /* GlobalService */].onCustomizerChange().debounce(300).takeWhile(function () {
            return _this.subscriptionActive;
        }).subscribe(function () {
            _this.layout();
        });
        return _this;
    }

    _createClass(Gallery, [{
        key: 'bindEvents',
        value: function bindEvents() {}
    }, {
        key: 'destroy',
        value: function destroy() {
            this.subscriptionActive = false;
        }
    }, {
        key: 'layout',
        value: function layout() {
            var $items = this.element.children();
            var minColumnWidth = void 0;
            if (!$items.length || !this.element.is(this.masonryGallerySelector)) {
                return;
            }
            minColumnWidth = this.element.children().get(0).getBoundingClientRect().width;
            $items.each(function (index, element) {
                var width = element.getBoundingClientRect().width;
                minColumnWidth = width < minColumnWidth ? width : minColumnWidth;
            });
            if (this.masonry) {
                this.masonry.destroy();
            }
            this.masonry = new __WEBPACK_IMPORTED_MODULE_0_masonry_layout__(this.element.get(0), {
                columnWidth: minColumnWidth,
                transitionDuration: 0
            });
        }
    }]);

    return Gallery;
}(__WEBPACK_IMPORTED_MODULE_1__models_DefaultComponent__["a" /* BaseComponent */]);

/***/ })
],[15]);
//# sourceMappingURL=app.bundle.js.map