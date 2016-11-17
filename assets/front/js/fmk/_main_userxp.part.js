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
              czrapp.$_body.removeClass('no-hover')
            }, 150);
        });
      }
    },

    //VARIOUS HOVERACTION
    variousHoverActions : function() {
      if ( czrapp.$_body.hasClass( 'tc-is-mobile' ) )
        return;

      /* Grid */
      $( '.grid-container__alternate, .grid-container__square-mini, .grid-container__plain' ).on( 'mouseenter mouseleave', '.entry-media__holder, article.format-image.no-excerpt .tc-content, article.format-gallery .tc-content', _toggleArticleParentHover )
      $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid__item', _toggleArticleParentHover );
      czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item, .widget-front', _toggleThisHover );

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
      var _input_types     = [
            'input[type="url"]',
            'input[type="email"]',
            'input[type="text"]',
            'input[type="password"]',
            'textarea'
          ],
        _focusable_class   = 'czr-focus',
        _parent_selector   = '.'+_focusable_class,
        _focus_class       = 'in-focus',
        _czr_form_class    = 'czr-form',
        _inputs            = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
        $_focusable_inputs = $( _input_types.join() );
        _maybe_fire        = $_focusable_inputs.length > 0;

      //This is needed to add a class to the input parent (label parent) so that
      //we can limit absolute positioning + translations only to relevant ones ( defined in _input_types )
      //consider the exclude?!
      if ( _maybe_fire ) {
        $_focusable_inputs.each( function() {
          $(this).parent().addClass(_focusable_class)
                 .closest('form').addClass(_czr_form_class);
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
    },

    headingsActions_test : function() {
      //User request animation frame
      var _page_header_inner   = $('.header-content-inner'),
          _header_push         = $('.topnav_navbars__wrapper'),
          _offset, __push;

      if ( ! _page_header_inner.length )
        return;

      czrapp.$_window.on( 'resize', function(){
        /*
        * todo: swap topnav_navbars_wrapper with sticky-placeholder when needed.
        */
        _offset = _page_header_inner.offset().top - _header_push.offset().top - _header_push.height();

        __push = _offset < 0 ? -1 * _offset : _page_header_inner.css('marginTop');

        _page_header_inner.css('marginTop', __push );
      });

    },

    featuredPages_test : function() {
      var $_featured_pages = $('.featured .widget-front'),
          doingAnimation   = false;

      if ( $_featured_pages.length < 2 )
        return;

      var   _fp_offsets = [];
            _offsets  = new Array(2),
            _maxs     = new Array(2);

      for (var i = 0; i < 2; i++) {
        _offsets[i] = new Array(2);
      }

      maybeSetElementsPosition();
      czrapp.$_window.on('resize', maybeSetElementsPosition );

      function maybeSetElementsPosition() {
        setTimeout( function() {
          if ( ! doingAnimation ) {
            doingAnimation = true;
            window.requestAnimationFrame(function() {

              setElementsPosition();
              doingAnimation = false;
            });
          }
        }, 50 );
      }

      function setElementsPosition() {
        var $_fp_offset = '',
            _elements = [ '[class*=fp-text]', '.fp-button' ];

        $.each( _elements, function(_element_index, _class ) {
          $.each( $_featured_pages, function( _fp_index, _fp ) {
            var $_el    = $(_fp).find(_class),
                _offset = 0;

            //reset top
            $_el.css( 'top', '' );
            //reset fp height
            $(_fp).css( 'height', '' );

            if ( $_el.length > 0 )
              _offset = $_el.offset().top;

            _offsets[_element_index][_fp_index] = _offset;
            _fp_offsets[_fp_index] = parseFloat($(_fp).offset().top);

          });

          /*
          * Break all when featured pages one on top of each other
          */
          if ( 1 != $.unique(_fp_offsets).length )
            return false;

          _maxs[_element_index] = Math.max.apply(Math, _offsets[_element_index] );


          $.each( $_featured_pages, function( _fp_index, _fp ) {
            var $_el    = $(_fp).find(_class),
                _offset;


            if ( $_el.length > 0 ){
              //console.log ( $_el.css('top') );
              /* var _this_top = parseFloat($_el.css('top')); */
              _offset = +_maxs[_element_index] - _offsets[_element_index][_fp_index];

              $_el.css( 'top', _offset ).css('position', 'relative');
              $(_fp).css( 'height',  $(_fp).height() + _offset );
            }
          });

        });
      }
    }
  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );
})(jQuery, czrapp);
