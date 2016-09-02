var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {

    init : function() {

    },
    //VARIOUS HOVERACTION
    variousHoverActions : function() {
      /* Grid */
      $( '.grid-container__alternate' ).on( 'mouseenter mouseleave', '.entry-image__container, article.format-image .tc-content, article.format-gallery .tc-content', _toggleArticleParentHover )
      $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid-item', _toggleThisHover );
      czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item', _toggleThisHover );

      /* end Grid */

      /* Widget li */
      czrapp.$_body.on( 'mouseenter mouseleave', '.widget li', _toggleThisOn );

      function _toggleArticleParentHover( evt ) {
        _toggleElementClassOnHover( $(this).closest('article'), 'hover', evt );
      };

      function _toggleThisHover( evt ) {
        _toggleElementClassOnHover( $(this), 'hover', evt );
      }

      function _toggleThisOn( evt ) {
        _toggleElementClassOnHover( $(this), 'on', evt );
      }

      function _toggleElementClassOnHover( $_el, _class, _evt ) {
        if ( 'mouseenter' == _evt.type )
          $_el.addClass( _class );
        else if ( 'mouseleave' == _evt.type )
          $_el.removeClass( _class );
      }

    },
    //FORM FOCUS ACTION
    formFocusAction : function() {
      var _input_types    = ['input', 'textarea'],
        _parent_selector  = '.form-group'      
        _focus_class      = 'in-focus',
        _inputs           = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join();

      czrapp.$_body.on( 'focusin focusout', _inputs, _toggleThisFocusClass );
      
      function _toggleThisFocusClass( evt ) {
        var $_el     = $(this),
            $_parent = $_el.closest( _parent_selector );

        if ( $_el.val() || ( evt && 'focusin' == evt.type ) ) {
          $_parent.addClass( _focus_class );
        } else {
          $_parent.removeClass( _focus_class );
        }
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
   variousHeaderActions : function() {
      /* ham navbar */
      czrapp.$_body.on( 'click', '.ham__navbar-toggler', function() {
        $(this).toggleClass('collapsed');
        czrapp.$_body.toggleClass('opened'); 
      });
      
      $(".nav__content").mCustomScrollbar({
        theme:"minimal"
      });  

      /* header search button */
      czrapp.$_tcHeader.on( 'click', '.desktop_search__link', function()  {
        czrapp.$_body.toggleClass('full-search-opened');
      });
      czrapp.$_body.on( 'click', '.search-close_btn', function()  {
        czrapp.$_body.removeClass('full-search-opened');
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
