var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp) {
  var _methods = {

    //INIT
    init : function() {
      var self = this;

      // cache jQuery el
      this.$_sliders = $( 'div[id*="customizr-slider"]' );

      //@todo EVENT
      //Recenter the slider arrows on resize
      czrapp.$_window.resize( function(){
        self.centerSliderArrows();
      });
    },



    fireSliders : function(name, delay, hover) {
      //Slider with localized script variables
      var _name   = name || TCParams.SliderName,
          _delay  = delay || TCParams.SliderDelay;
          _hover  = hover || TCParams.SliderHover;

      if ( 0 === _name.length )
        return;

      if ( 0 !== _delay.length && ! _hover ) {
        this.$_sliders.czrCarousel({
            interval: _delay,
            pause: "false"
        });
      } else if ( 0 !== _delay.length ) {
        this.$_sliders.czrCarousel({
            interval: _delay
        });
      } else {
        this.$_sliders.czrCarousel();
      }
    },

    manageHoverClass : function() {
      //add a class to the slider on hover => used to display the navigation arrow
      this.$_sliders.hover( function() {
          $(this).addClass('tc-slid-hover');
        },
        function() {
          $(this).removeClass('tc-slid-hover');
        }
      );
    },

    //SLIDER ARROWS
    centerSliderArrows : function() {
      if ( 0 === this.$_sliders.length )
          return;
      this.$_sliders.each( function() {
          var _slider_height = $( '.carousel-inner' , $(this) ).height();
          $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
      });
    },


    //Slider swipe support with hammer.js
    addSwipeSupport : function() {
      if ( 'function' != typeof($.fn.hammer) || 0 === this.$_sliders.length )
        return;

      //prevent propagation event from sensible children
      this.$_sliders.on('touchstart touchmove', 'input, button, textarea, select, a:not(".tc-slide-link")', function(ev) {
          ev.stopPropagation();
      });

      var _is_rtl = czrapp.$_body.hasClass('rtl');
      this.$_sliders.each( function() {
          $(this).hammer().on('swipeleft', function() {
              $(this).czrCarousel( ! _is_rtl ? 'next' : 'prev' );
          });
          $(this).hammer().on('swiperight', function(){
              $(this).czrCarousel( ! _is_rtl ? 'prev' : 'next' );
          });
      });
    },

    //Has to be fire on load after all other methods
    //@todo understand why...
    sliderTriggerSimpleLoad : function() {
      this.triggerSimpleLoad( this.$_sliders.find('.carousel-inner img') );
    }
  };//methods {}

  czrapp.methods.Czr_Slider = {};
  $.extend( czrapp.methods.Czr_Slider , _methods );

})(jQuery, czrapp);