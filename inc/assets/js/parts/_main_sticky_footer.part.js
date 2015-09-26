var czrapp = czrapp || {};
/************************************************
* STICKY FOOTER SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {
    init : function() {
      this.$_push   = $('#tc-push-footer');
      this._class   = 'sticky-footer-enabled';
      this.$_page   = $('#tc-page-wrap');
      
      if ( 1 != TCParams.stickyHeader ) {//sticky header fires a resize
        var self = this;
        setTimeout( function() {
                self._apply_sticky_footer(); }, 50 
        );
      }
    },

    /***********************************************
    * DOM EVENT LISTENERS AND HANDLERS
    ***********************************************/
    stickyFooterEventListener : function() {
      var self = this;

      // maybe apply sticky footer on window resize
      czrapp.$_window.on( 'tc-resize', function() {
        self.stickyFooterEventHandler('resize');
      });

      // maybe apply sticky footer on golden ratio applied
      czrapp.$_window.on( 'golden-ratio-applied', function() {
        self.stickyFooterEventHandler('refresh');
      });

      /* can be useful without exposing methods make it react to this event which could be externally fired, used in the preview atm */
      czrapp.$_body.on( 'refresh-sticky-footer', function() {
        self.stickyFooterEventHandler('refresh');
      });

    },
    
    stickyFooterEventHandler : function( evt ) {
      var self = this;

      if ( ! this._is_sticky_footer_enabled() )
        return;

      switch ( evt ) {
        case 'resize':
          //to avoid the creation of a function inside a loop
          //but still allow the access to "this"
          var func = function() { return self._apply_sticky_footer() ;};
          for ( var i = 0; i<5; i++ ) /* I've seen something like that in twentyfifteen */
            setTimeout( func, 50 * i);
        break;
        case 'refresh':
          this._apply_sticky_footer();
        break;
      }
    },
    /* We apply the "sticky" footer by setting the height of the push div, and adding the proper class to show it */
    _apply_sticky_footer : function() {

      var  _f_height     = this._get_full_height(),
           _w_height     = czrapp.$_window.height(),
           _push_height  = _w_height - _f_height,
           _event        = false;
      
      if ( _push_height > 0 ) {
        this.$_push.css('height', _push_height).addClass(this._class);
        _event = 'sticky-footer-on';
      }else if ( this.$_push.hasClass(this._class) ) {
        this.$_push.removeClass(this._class);
        _event = 'sticky-footer-off';
      }

      /* Fire an event which something might listen to */
      if ( _event )
        czrapp.$_body.trigger(_event);    
    },
    
    //STICKY HEADER SUB CLASS HELPER (private like)
    /*
    * return @bool: whether apply or not the sticky-footer
    */
    _is_sticky_footer_enabled : function() {
      return czrapp.$_body.hasClass('tc-sticky-footer');
    },


    //STICKY HEADER SUB CLASS HELPER (private like)
    /* 
    * return @int: the potential height value of the page
    */
    _get_full_height : function() {
      var _full_height = this.$_page.outerHeight(true) + this.$_page.offset().top,
          _push_height = 'block' == this.$_push.css('display') ? this.$_push.outerHeight() : 0;
            
      return _full_height - _push_height;
    }
  };//_methods{}

  czrapp.methods.Czr_StickyFooter = {};
  $.extend( czrapp.methods.Czr_StickyFooter , _methods );

})(jQuery, czrapp);
