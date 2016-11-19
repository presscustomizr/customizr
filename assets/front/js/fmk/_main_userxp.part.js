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
          _header_push         = $('.header-absolute .topnav_navbars__wrapper'),
          _offset, doingAnimation;

      if ( ! _page_header_inner.length || ! _header_push.length )
        return;

      _maybeHandleResize();
      czrapp.$_window.on('resize', _maybeHandleResize );

      function _maybeHandleResize(){
        if ( ! doingAnimation ) {
          //do nothing if is scrolling
          if ( czrapp.$_body.hasClass('sticky-enabled') )
            return;

          doingAnimation = true;
          window.requestAnimationFrame( function() {

            //reset offset
            if ( 'absolute' != _header_push.css('position') )
              _offset = '';
            else
              _offset =  parseFloat( _header_push.outerHeight() );

            _page_header_inner.css('paddingTop', _offset );
            //We should handle the font sizing I think
            doingAnimation = false;
          });
        }
      };

    },
    /* Find a way to make this smaller but still effective */
    featuredPages_test : function() {

      var $_featured_pages  = $('.featured .widget-front'),
          _n_featured_pages = $_featured_pages.length,
          doingAnimation    = false,
          _lastWinWidth     = '';


      if ( _n_featured_pages < 2 )
        return;

      var $_fp_elements     = new Array( _n_featured_pages ),
          _n_elements       = new Array( _n_featured_pages );

      //Grab all subelements having class starting with fp-
      //Requires all fps having same html structure...
      $.each( $_featured_pages, function( _fp_index, _fp ) {
        $_fp_elements[_fp_index]  = $(_fp).find( '[class^=fp-]' );
        _n_elements[_fp_index]    = $_fp_elements[_fp_index].length;
      });

      _n_elements = Math.max.apply(Math, _n_elements );

      if ( ! _n_elements )
        return;

      var _offsets    = new Array( _n_elements ),
          _maxs       = new Array( _n_elements );

      /*
      * Build the _offsets matrix
      * Row => element (order given by _elements array)
      * Col => fp
      */
      for (var i = 0; i < _n_elements; i++)
        _offsets[i] = new Array( _n_featured_pages);


      //fire
      maybeSetElementsPosition();
      //bind
      czrapp.$_window.on('resize', maybeSetElementsPosition );

      function maybeSetElementsPosition() {

        if ( ! doingAnimation ) {
          var _winWidth = czrapp.$_window.width();
          /*
          * we're not interested in win height resizing
          */
          if ( _winWidth == _lastWinWidth )
            return;

          _lastWinWidth = _winWidth;

          doingAnimation = true;

          window.requestAnimationFrame(function() {
            setElementsPosition();
            doingAnimation = false;
          });

        }
      }


      function setElementsPosition() {
        /*
        * this array will store the
        */
        var _fp_offsets = [];

        for ( _element_index = 0; _element_index < _n_elements; _element_index++ ) {

          for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
            //Reset and grab the the top offset for each element
            var $_el    = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                _offset = 0,
                $_fp    = $($_featured_pages[_fp_index]);

            if ( $_el.length > 0 ) {
              //reset maybe added paddingTop
              $_el.css( 'paddingTop', '' );
              //retrieve the top position
              _offset = $_el.offset().top;

            }
            _offsets[_element_index][_fp_index] = _offset;

            /*
            * Build the array of fp offset once (first loop on elements)
            */
            if ( _fp_offsets.length < _n_featured_pages )
              _fp_offsets[_fp_index] = parseFloat( $_fp.offset().top);
          }//endfor


          /*
          * Break this only loop when featured pages are one on top of each other
          * featured pages top offset differs
          * We continue over other elements as we need to reset other marginTop
          */
          if ( 1 != _.uniq(_fp_offsets).length )
            continue;

          /*
          * for each type of element store the max offset value
          */
          _maxs[_element_index] = Math.max.apply(Math, _offsets[_element_index] );

          /*
          * apply the needed offset for each featured page element
          */
          for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
            var $_el    = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                _offset;

            if ( $_el.length > 0 ) {
              _offset = +_maxs[_element_index] - _offsets[_element_index][_fp_index];
              if ( _offset )
                $_el.css( 'paddingTop', parseFloat($_el.css('paddingTop')) + _offset );
            }
          }//endfor
        }//endfor
      }//endfunction
    }//endmethod

  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );
})(jQuery, czrapp);
