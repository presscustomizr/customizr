var czrapp = czrapp || {};
//@global CZRParams
/************************************************
* LET'S DANCE
*************************************************/
( function ( czrapp ) {
      //adds the server params to the app now
      czrapp.localized = CZRParams || {};

      //THE DEFAULT MAP
      //Other methods can be hooked. @see czrapp.customMap
      var appMap = {
                base : {
                      ctor : czrapp.Base,
                      ready : [
                            'cacheProp'
                      ]
                },
                browserDetect : {
                      ctor : czrapp.Base.extend( czrapp.methods.BrowserDetect ),
                      ready : [ 'addBrowserClassToBody' ]
                },
                jqPlugins : {
                      ctor : czrapp.Base.extend( czrapp.methods.JQPlugins ),
                      ready : [
                            'centerImagesWithDelay',
                            'centerInfinity',
                            'imgSmartLoad',
                            //'dropCaps',
                            //'extLinks',
                            'lightBox',
                            'parallax'
                      ]
                },
                slider : {
                      ctor : czrapp.Base.extend( czrapp.methods.Slider ),
                      ready : [
                            'initOnCzrReady',//<= fires all carousels : main, galleries, related posts + center images
                      ]
                },
                dropdowns : {
                      ctor  : czrapp.Base.extend( czrapp.methods.Dropdowns ),
                      ready : [
                            'initOnCzrReady',
                            'dropdownMenuOnHover',
                            'dropdownOpenGoToLinkOnClick',
                            'dropdownPlacement',//snake
                            'dropdownOnClickVerticalNav'
                      ]
                },
                userXP : {
                      ctor : czrapp.Base.extend( czrapp.methods.UserXP ),
                      ready : [
                            'setupUIListeners',//<= setup various observable values like this.isScrolling, this.scrollPosition, ...

                            'stickifyHeader',
                            'gutenbergAlignfull',

                            'outline',

                            'variousHoverActions',
                            'formFocusAction',
                            'variousHeaderActions',
                            'headerSearchToLife',

                            'smoothScroll',

                            'attachmentsFadeEffect',

                            'onEscapeKeyPressed',

                            'featuredPagesAlignment',
                            'bttArrow',
                            'backToTop',

                            'anchorSmoothScroll',

                            'mayBePrintFrontNote',
                            'mayBeLoadFontAwesome',

                            'maybePreloadGoogleFonts'
                      ]
                },
                /*stickyHeader : {
                      ctor : czrapp.Base.extend( czrapp.methods.StickyHeader ),
                      ready : [
                            'initOnDomReady',
                            'stickyHeaderEventListener',
                            'triggerStickyHeaderLoad'
                      ]
                },*/
                stickyFooter : {
                      ctor : czrapp.Base.extend( czrapp.methods.StickyFooter ),
                      ready : [
                            'initOnDomReady',
                            'stickyFooterEventListener'
                      ]
                },
                sideNav : {
                      ctor : czrapp.Base.extend( czrapp.methods.SideNav ),
                      ready : [
                            'initOnDomReady'
                      ]
                }
      };//map

      //set the observable value
      //listened to by _instantianteAndFireOnDomReady = function( newMap, previousMap, isInitial )
      czrapp.appMap( appMap , true );//true for isInitial map

})( czrapp );