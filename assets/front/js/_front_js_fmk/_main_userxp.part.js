var czrapp = czrapp || {};

(function($, czrapp) {
   var _methods =   {

      //outline firefox fix, see https://github.com/presscustomizr/customizr/issues/538
      outline: function() {
         if ( 'function' == typeof( tcOutline ) )
            tcOutline();
      },

      disableHoverOnScroll: function() {
         //While scrolling we don' want to trigger hover actions

         //https://www.thecssninja.com/javascript/pointer-events-60fps
         //pure javascript approach
         var body = document.body,
             timer;

         window.addEventListener( 'scroll', function() {

            clearTimeout(timer);

            if( !body.classList.contains( 'no-hover' ) ) {
               body.classList.add( 'no-hover' );
            }

            timer = setTimeout( function(){
               body.classList.remove('no-hover');
            }, 100);

         }, false );
      },

      //VARIOUS HOVERACTION
      variousHoverActions : function() {
         if ( czrapp.$_body.hasClass( 'czr-is-mobile' ) )
            return;

         /* Grid */
         $( '.grid-container__alternate, .grid-container__square-mini, .grid-container__plain' ).on( 'mouseenter mouseleave', '.entry-media__holder, article.full-image .tc-content', _toggleArticleParentHover );
         $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid__item', _toggleArticleParentHover );
         czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item, .widget-front', _toggleThisHover );

         /* end Grid */

         /* Widget li */
         czrapp.$_body.on( 'mouseenter mouseleave', '.widget li', _toggleThisOn );

         function _toggleArticleParentHover( evt ) {
            _toggleElementClassOnHover( $(this).closest('article'), 'hover', evt );
         }

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
         var _input_types       = [
                  'input[type="url"]',
                  'input[type="email"]',
                  'input[type="text"]',
                  'input[type="password"]',
                  'textarea'
            ],
            _focusable_class    = 'czr-focus',
            _parent_selector    = '.'+_focusable_class,
            _focus_class        = 'in-focus',
            _czr_form_class     = 'czr-form',
            _inputs             = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
            $_focusable_inputs  = $( _input_types.join() );
            _maybe_fire         = $_focusable_inputs.length > 0;

         //This is needed to add a class to the input parent (label parent) so that
         //we can limit absolute positioning + translations only to relevant ones ( defined in _input_types )
         //consider the exclude?!
         if ( _maybe_fire ) {
            $_focusable_inputs.each( function() {
               var $_this = $(this);
               if ( !$_this.attr('placeholder') && ( $_this.closest( '#buddypress' ).length < 1 ) ) {
                  $(this)
                        .addClass('czr-focusable')
                        .parent().addClass(_focusable_class)
                        .closest('form').addClass(_czr_form_class);
               }
            });
         }else
            return;

         czrapp.$_body.on( 'in-focus-load.czr-focus focusin focusout', _inputs, _toggleThisFocusClass );

         function _toggleThisFocusClass( evt ) {
            var $_el       = $(this),
                  $_parent = $_el.closest(_parent_selector);

            if ( $_el.val() || ( evt && 'focusin' == evt.type ) ) {
               $_parent.addClass( _focus_class );
            } else {
               $_parent.removeClass( _focus_class );
            }
         }

         //on ready :   think about search forms in search pages
         $(_inputs).trigger( 'in-focus-load.czr-focus' );

         //search form clean on .icn-close click
         czrapp.$_body.on( 'click tap', '.icn-close', function() {
            $(this).closest('form').find('.czr-search-field').val('').focus();
         });
      },

      variousHeaderActions : function() {
         var _mobile_viewport                   = 992;

         /* header search button */
         czrapp.$_body.on( 'click tap', '.desktop_search__link', function(evt) {
            evt.preventDefault();
            czrapp.$_body.toggleClass('full-search-opened');
         });
         czrapp.$_body.on( 'click tap', '.search-close_btn', function(evt) {
            evt.preventDefault();
            czrapp.$_body.removeClass('full-search-opened');
         });

         //custom scrollbar for woocommerce list
         if ( 'function' == typeof $.fn.mCustomScrollbar ) {
            czrapp.$_body.on( 'shown.czr.czrDropdown', '.primary-nav__woocart', function() {
               var $_to_scroll = $(this).find('.product_list_widget');
               if ( $_to_scroll.length && !$_to_scroll.hasClass('mCustomScrollbar') ) {
                  $_to_scroll.mCustomScrollbar({
                     theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                  });
               }
            });
         }

         //go to opened on click element when mCustomScroll active
         czrapp.$_body.on( 'shown.czr.czrDropdown', '.czr-open-on-click.mCustomScrollbar, .czr-open-on-click .mCustomScrollbar, .mCustomScrollbar .czr-open-on-click', function( evt ) {
            var $_this                  = $( this ),
                  $_customScrollbar = $_this.hasClass('mCustomScrollbar') ? $_this : $_this.closest('.mCustomScrollbar');
            if ( $_customScrollbar.length )
               //http://manos.malihu.gr/jquery-custom-content-scroller/
               $_customScrollbar.mCustomScrollbar( 'scrollTo', $(evt.target) );
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


      /* Find a way to make this smaller but still effective */
      featuredPagesAlignment : function() {

         var $_featured_pages   = $('.featured-page .widget-front'),
               _n_featured_pages = $_featured_pages.length,
               doingAnimation      = false,
               _lastWinWidth       = '';


         if ( _n_featured_pages < 2 )
            return;

         var $_fp_elements       = new Array( _n_featured_pages ),
               _n_elements          = new Array( _n_featured_pages );

         //Grab all subelements having class starting with fp-
         //Requires all fps having same html structure...
         $.each( $_featured_pages, function( _fp_index, _fp ) {
            $_fp_elements[_fp_index]   = $(_fp).find( '[class^=fp-]' );
            _n_elements[_fp_index]      = $_fp_elements[_fp_index].length;
         });

         _n_elements = Math.max.apply(Math, _n_elements );

         if ( ! _n_elements )
            return;

         var _offsets      = new Array( _n_elements ),
               _maxs          = new Array( _n_elements );

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
                  var $_el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                        _offset = 0,
                        $_fp      = $($_featured_pages[_fp_index]);

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
                  var $__el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                        __offset;

                  if ( $__el.length > 0 ) {
                     __offset = +_maxs[_element_index] - _offsets[_element_index][_fp_index];
                     if ( __offset )
                        $__el.css( 'paddingTop', parseFloat($__el.css('paddingTop')) + __offset );
                  }
               }//endfor
            }//endfor
         }//endfunction
      },//endmethod

      //Btt arrow visibility
      bttArrow : function() {
         var doingAnimation = false,
            $_btt_arrow         = $('.czr-btta');

         if ( 0 === $_btt_arrow.length )
            return;

         czrapp.$_window.on( 'scroll', bttArrowVisibility );
         bttArrowVisibility();

         function bttArrowVisibility() {
            if ( ! doingAnimation ) {
               doingAnimation = true;

               window.requestAnimationFrame( function() {
                  if ( czrapp.$_window.scrollTop() > 100 )
                     $_btt_arrow.addClass('show');
                  else
                     $_btt_arrow.removeClass('show');

                  doingAnimation = false;
               });
            }
         }//bttArrowVisibility

      },//bttArrow

      //BACK TO TOP
      backToTop : function() {
         var $_html = $("html, body"),
               _backToTop = function( evt ) {
                  return ( evt.which > 0 || "mousedown" === evt.type || "mousewheel" === evt.type) && $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
               };

         czrapp.$_body.on( 'click touchstart touchend czr-btt', '.czr-btt', function ( evt ) {

            evt.preventDefault();
            evt.stopPropagation();
            $_html.on( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
            $_html.animate({
                  scrollTop: 0
            }, 1e3, function () {
                  $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
            });
         });
      },

      //SMOOTH SCROLL FOR AUTHORIZED LINK SELECTORS
      anchorSmoothScroll : function() {
        if ( ! czrapp.localized.anchorSmoothScroll || 'easeOutExpo' != czrapp.localized.anchorSmoothScroll )
              return;

        var _excl_sels = ( czrapp.localized.anchorSmoothScrollExclude && _.isArray( czrapp.localized.anchorSmoothScrollExclude.simple ) ) ? czrapp.localized.anchorSmoothScrollExclude.simple.join(',') : '',
            self = this,
            $_links = $('a[href^="#"]', '#content').not(_excl_sels);

        //Deep exclusion
        //are ids and classes selectors allowed ?
        //all type of selectors (in the array) must pass the filter test
        _deep_excl = _.isObject( czrapp.localized.anchorSmoothScrollExclude.deep ) ? czrapp.localized.anchorSmoothScrollExclude.deep : null ;
        if ( _deep_excl )
          _links = _.toArray($_links).filter( function ( _el ) {
            return ( 2 == ( ['ids', 'classes'].filter(
                          function( sel_type) {
                              return self.isSelectorAllowed( $(_el), _deep_excl, sel_type);
                          } ) ).length
                  );
          });
        $(_links).click( function () {
            var anchor_id = $(this).attr("href");

            //anchor el exists ?
            if ( ! $(anchor_id).length )
              return;

            if ('#' != anchor_id) {
                $('html, body').animate({
                    scrollTop: $(anchor_id).offset().top
                }, 700, czrapp.localized.anchorSmoothScroll);
            }
            return false;
        });//click
      },
   };//_methods{}

   czrapp.methods.UserXP = {};
   $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);
