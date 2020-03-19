/****************************************************************
* FORMER HARD CODED SCRIPTS MADE ENQUEUABLE WITH LOCALIZED PARAMS
*****************************************************************/
(function($) {
    //czrapp.localized = TCParams
    var _doWhenCzrappIsReady = function() {
          var pluginCompatParams = ( czrapp.localized && czrapp.localized.pluginCompats ) ? czrapp.localized.pluginCompats : {},
              frontHelpNoticeParams = ( czrapp.localized && czrapp.localized.frontHelpNoticeParams ) ? czrapp.localized.frontHelpNoticeParams : {};

          //PARALLAX SLIDER
          $( function( $ ) {
                if ( czrapp.localized.isParallaxOn ) {
                      $( '.czr-parallax-slider' ).czrParallax( { parallaxRatio : czrapp.localized.parallaxRatio || 0.55 } );
                }
          });

          //PLUGIN COMPAT
          //Optimize Press
          if ( pluginCompatParams.optimizepress_compat && pluginCompatParams.optimizepress_compat.remove_fancybox_loading ) {
                  var opjq = opjq || 'undefined';
                  if ( ! _.isUndefined( opjq ) ) {
                      opjq(document).ready( function() {
                          opjq('#fancybox-loading').remove();
                      } );
                }
          }
    };
    // see wp-content/themes/customizr/assets/front/js/_front_js_fmk/_main_xfire_0.part.js
    // feb 2020 => implemented for https://github.com/presscustomizr/pro-bundle/issues/162
    if ( window.czrapp && czrapp.ready && 'resolved' == czrapp.ready.state() ) {
        _doWhenCzrappIsReady();
    } else {
        document.addEventListener('czrapp-is-ready', function() {
            _doWhenCzrappIsReady();
        });
    }
})(jQuery);