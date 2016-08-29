var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {

    init : function() {

    },
    //VARIOUS HOVERACTION
    variousHoverActions : function() {
      /* Grid */
      $( '.grid-container__alternate' ).on( 'mouseenter mouseleave', '.entry-image__container', _toggleParentHover );
      $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid-item', _toggleThisHover );
      /* end Grid */

      /* Widget li */
      czrapp.$_body.on( 'mouseenter mouseleave', '.widget li', _toggleThisOn );

      function _toggleParentHover( evt ) {
        _toggleElementClassOnHover( $(this).closest('article'), 'hover', evt );
      };

      function _toggleThisHover( evt ) {
        _toggleElementClassOnHover( $(this), 'hover', evt );
      }

      function _toggleThisOn( evt ) {
        _toggleElementClassOnHover( $(this), 'on', evt );
      }

      function _toggleElementClassOnHover( $_el, _class, _evt ) {
        if ( 'mouseenter' == _evt.type && ! $_el.hasClass( _class ) )
          $_el.addClass( _class );
        else if ( 'mouseleave' == _evt.type && $_el.hasClass( _class ) )
          $_el.removeClass( _class );
      }

    },
    //FORM FOCUS ACTION
    formFocusAction : function() {
      var _inputs    = ['input', 'textarea'],
        _focus_class = 'in-focus';

      czrapp.$_body.on( 'focusin focusout', '.form-group input, .form-group textarea', _toggleThisFocusClass );
      
      function _toggleThisFocusClass( evt ) {
        var $_el     = $(this),
            $_parent = $_el.closest( '.form-group' );
        if ( $_el.val() || ( evt && 'focusin' == evt.type ) )
          $_parent.addClass( _focus_class );
        else
          $_parent.removeClass( _focus_class );
      };

    },
    //test comment reply
   commentReplyTest : function() {
     czrapp.$_body.on('click', '.comment-reply-link', function( evt ){
        evt.preventDefault();
        var $_this_parent = $(this).closest('.comment-content');
         $.each( $( '#comments .comment-content.open') , function() {
           if ( ! $(this).is($_this_parent) )
             $(this).removeClass("open");
         });
        $_this_parent.toggleClass('open');
    });
   },    
    //SMOOTH SCROLL
    smoothScroll: function() {
      if ( CZRParams.SmoothScroll && CZRParams.SmoothScroll.Enabled )
        smoothScroll( CZRParams.SmoothScroll.Options );
    }
  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );  
})(jQuery, czrapp);
