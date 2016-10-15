var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {

    init : function() {

    },
    disableHoverOnScroll: function() {
      //While scrolling we don' want to trigger hover actions
      if ( ! czrapp.$_body.hasClass( 'tc-is-mobile' ) ) {
        var timer;

        czrapp.$_window.on('scroll', function() {
            clearTimeout(timer);
            if ( ! czrapp.$_body.hasClass( 'no-hover' ) )
              czrapp.$_body.addClass('no-hover');

            timer = setTimeout(function() {
              console.log('never here?');
              czrapp.$_body.removeClass('no-hover')
            }, 300);
        });
      }
    },

    //VARIOUS HOVERACTION
    variousHoverActions : function() {
      /* Grid */
      $( '.grid-container__alternate, .square-grid__mini, .grid-container__full' ).on( 'mouseenter mouseleave', '.entry-image__container, article.format-image.no-excerpt .tc-content, article.format-gallery .tc-content', _toggleArticleParentHover )
      $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid-post, .tc-grid-post', _toggleArticleParentHover );
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
      var _input_types     = ['input[type="url"]', 'input[type="email"]', 'input[type="text"]', 'textarea'],
        _focusable_class   = 'czr-focus',
        _parent_selector   = '.'+_focusable_class,
        _focus_class       = 'in-focus',
        _inputs            = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
        $_focusable_inputs = $( _input_types.join() );
        _maybe_fire        = $_focusable_inputs.length > 0;

      //This is needed to add a class to the input parent (label parent) so that
      //we can limit absolute positioning + translations only to relevant ones ( defined in _input_types )
      //consider the exclude?!
      if ( _maybe_fire ) {
        $_focusable_inputs.each( function() {
          $(this).parent().addClass(_focusable_class);
        });
      }else
        return;

      czrapp.$_body.on( 'in-focus-load.czr-focus focusin focusout', _inputs, _toggleThisFocusClass );

      function _toggleThisFocusClass( evt ) {
        var $_el     = $(this),
            $_parent = $_el.closest(_parent_selector);

        if ( $_el.val() || ( evt && 'focusin' == evt.type ) ) {
          $_parent.addClass( _focus_class );
        } else {
          $_parent.removeClass( _focus_class );
        }
      };

      //on ready :  think about search forms in search pages
      $(_inputs).trigger( 'in-focus-load.czr-focus' );

      //search form clean on .icn-close click
      czrapp.$_body.on( 'click tap', '.icn-close', function() {
        $(this).closest('form').find('.czr-search-field').val('').focus();
      });
    },

    //test comment reply
    // commentReplyTest : function() {
    //   czrapp.$_body.on('click', '.comment-reply-link', function( evt ){
    //     evt.preventDefault();
    //     var $_this_parent = $(this).closest('.comment-content');
    //      $.each( $( '#comments .comment-content.open') , function() {
    //        if ( ! $(this).is($_this_parent) )
    //          $(this).removeClass("open");
    //      });
    //     $_this_parent.toggleClass('open');
    //   });
    // },
    variousHeaderActions : function() {
      /* ham navbar */
      czrapp.$_body.on( 'click', '.ham__navbar-toggler', function() {
        if ( ! $(this).parent( '.mobile-utils__wrapper' ) )
          $(this).toggleClass('collapsed');
        czrapp.$_body.toggleClass('opened');
      });


      /* TO FIX: doesn't work */
    /*  $( '.hamburger-menu .nav__container > nav' ).mCustomScrollbar({
        theme:"minimal"
      });
    */
      /* header search button */
      czrapp.$_tcHeader.on( 'click', '.desktop_search__link', function(evt) {
        evt.preventDefault();
        czrapp.$_body.toggleClass('full-search-opened');
      });
      czrapp.$_body.on( 'click', '.search-close_btn', function(evt) {
        evt.preventDefault();
        czrapp.$_body.removeClass('full-search-opened');
      });
    },
    //SMOOTH SCROLL
    smoothScroll: function() {
      if ( CZRParams.SmoothScroll && CZRParams.SmoothScroll.Enabled )
        smoothScroll( CZRParams.SmoothScroll.Options );
    },
    pluginsCompatibility: function() {
      /*
      * Super socializer
      * it prints the socializer vertical bar filtering the excerpt
      * so as child of .entry-content__holder.
      * In alternate layouts, when centering sections, the use of the translate property
      * changed the fixed behavior (of the aforementioned bar) to an absoluted behavior
      * with the following core we move the bar outside the section
      * ( different but still problems occurr with the masonry )
      */
      var $_ssbar = $( '.the_champ_vertical_sharing, .the_champ_vertical_counter', '.article-container' );
      if ( $_ssbar.length )
        $_ssbar.detach().prependTo('.article-container');
    }
  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );
})(jQuery, czrapp);
