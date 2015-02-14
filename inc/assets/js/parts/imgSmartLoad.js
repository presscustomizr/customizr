/* ===================================================
 * imgSmartLoad.js v1.0.0
 * ===================================================
 * (c) 2015 Nicolas Guillaume, Nice, France
 * CenterImages plugin may be freely distributed under the terms of the GNU GPL v2.0 or later license.
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Replace img src placeholder by the real src on scroll window event
 *
 * =================================================== */
;(function($) {
    var $w = $(window),
        th = 200,
        _attr = "data-src",
        $_images = $('img[data-src]'),
        _load_all_images_on_first_scroll = false,
        _inViewPort,
        timer,
        increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer

    $_images.bind('scrollin', {}, function() {
        _load_img(this);
    });
    $w.scroll( _better_scroll_event_handler );
    $w.resize(_event_handler);
    _event_handler();

    function _better_scroll_event_handler(evt) {
      //use a timer
      if ( timer) {
          increment++;
          window.clearTimeout(timer);
      }

      timer = window.setTimeout(function() {
        _event_handler(evt);
      }, increment > 5 ? 50 : 0 );
    }

    function _event_handler(evt) {
        _inViewPort = $_images.filter(function() {
            var $e = $(this),
                wt = $w.scrollTop(),
                wb = wt + $w.height(),
                et = $e.offset().top,
                eb = et + $e.height();
            return eb >= wt - th && et <= wb + th;
        });
        if ( evt && 'scroll' == evt.type && _load_all_images_on_first_scroll )
          $_images = $_images.trigger('scrollin');
        else
          $_images = $_images.not(_inViewPort.trigger('scrollin'));
    }

    function _load_img(img) {
        var $img = $(img),
            src = $img.attr(_attr);
        $img.unbind('scrollin').hide().removeAttr(_attr);
        img.src = src;
        $img.fadeIn();
    }
    return this;
})(jQuery);