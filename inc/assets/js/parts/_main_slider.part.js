var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp) {
  var _methods = {

    //INIT
    init : function() {
      var self = this;
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
        $("#customizr-slider").carousel({
            interval: _delay,
            pause: "false"
        });
      } else if ( 0 !== _delay.length ) {
        $("#customizr-slider").carousel({
            interval: _delay
        });
      } else {
        $("#customizr-slider").carousel();
      }
    },

    manageHoverClass : function() {
      //add a class to the slider on hover => used to display the navigation arrow
      $(".carousel").hover( function() {
          $(this).addClass('tc-slid-hover');
        },
        function() {
          $(this).removeClass('tc-slid-hover');
        }
      );
    },

    //SLIDER ARROWS
    centerSliderArrows : function() {
      if ( 0 === $('.carousel').length )
          return;
      $('.carousel').each( function() {
          var _slider_height = $( '.carousel-inner' , $(this) ).height();
          $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
      });
    },


    //Slider swipe support with hammer.js
    addSwipeSupport : function() {
      if ( 'function' != typeof($.fn.hammer) )
        return;
      //prevent propagation event from sensible children
      $(".carousel input, .carousel button, .carousel textarea, .carousel select, .carousel a").on("touchstart touchmove", function(ev) {
          ev.stopPropagation();
      });

      var _is_rtl = $('body').hasClass('rtl');
      $('.carousel' ).each( function() {
          $(this).hammer().on('swipeleft tap', function() {
              $(this).carousel( ! _is_rtl ? 'next' : 'prev' );
          });
          $(this).hammer().on('swiperight', function(){
              $(this).carousel( ! _is_rtl ? 'prev' : 'next' );
          });
      });
    },

    //Has to be fire on load after all other methods
    //@todo understand why...
    sliderTriggerSimpleLoad : function() {
      this.triggerSimpleLoad( $( '.carousel .carousel-inner').find('img') );
    }
  };//methods {}

  czrapp.methods.Czr_Slider = {};
  $.extend( czrapp.methods.Czr_Slider , _methods );

})(jQuery, czrapp);