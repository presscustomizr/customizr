/*global jQuery */
/*!
* This is an adapted version of fit text
* 1) a user defined caption font ratio has been added
* 2) the resizer takes into account not only the element width, but the specified parent's height. => this solves the problem of fonts not properly resized on landscape mobile devices, or slider too short (user can set the slider's height)
* @return void()
* FitText.js 1.2
*
* Copyright 2011, Dave Rupert http://daverupert.com
* Released under the WTFPL license
* http://sam.zoy.org/wtfpl/
*
* Date: Thu May 05 14:23:00 2011 -0600
*/

(function( $ ){

  $.fn.czrFitText = function( kompressor, options ) {

    // Setup options
    var compressor = kompressor || 1,
        settings = $.extend({
          'minFontSize'            : Number.NEGATIVE_INFINITY,
          'maxFontSize'            : Number.POSITIVE_INFINITY,
          'fontRatio'              : 1,
          'refParentSel'           : '.fittext-p',
          'parentCompressorFactor' : 8,
        }, options);

    return this.each(function(){

      // Store the object
      var $this      = $(this),
          $refParent = $this.closest(settings.refParentSel),
          _font_size;

      // Resizer() resizes items based on the object width divided by the compressor * 10
      var resizer = function () {

        _font_size = Math.max(
            Math.min(
              $this.width() / (compressor*10),
              ( $refParent.length >= 1 ) ? $refParent.height() / (compressor*settings.parentCompressorFactor) : $this.width() / (compressor*10),
              parseFloat(settings.maxFontSize)
            ),
            parseFloat(settings.minFontSize)
        );

        _font_size = Math.max( _font_size * settings.fontRatio, parseFloat( settings.minFontSize ) );

        $this.css('font-size', _font_size  );
        $this.css('line-height', ( _font_size  * 1.45 ) + 'px');
      };

      // Call once to set.
      resizer();

      // Call on resize. Opera debounces their resize by default.
      $(window).on('resize.czrFittext orientationchange.czrFittext', resizer);

    });

  };

})( jQuery );