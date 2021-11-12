// @global CZRParams
// march 2020 : wp_footer js code to be minified
// introduced for https://github.com/presscustomizr/nimble-builder/issues/626
( function() {
      var _loading = false;
          _load = function() {
              if ( _loading )
                return;
              jQuery( function() {
                  var _headTag = document.getElementsByTagName('head')[0];
                  var _script = document.createElement("script");
                  _script.setAttribute('src', CZRParams.mainScriptUrl );
                  _script.setAttribute('id', 'tc-scripts' );
                  _script.setAttribute('defer', 'defer');
                  if ( _headTag ) {
                      _headTag.appendChild(_script);
                  }
                  _loading = true;
              });
          };
      // recursively try to load jquery every 200ms during 6s ( max 30 times )
      var _doWhenJqueryIsReady = function( attempts ) {
          attempts = attempts || 0;
          if ( typeof undefined !== typeof window.jQuery ) {
              _load();
          } else if ( attempts < 30 ) {
              setTimeout( function() {
                  attempts++;
                  _doWhenJqueryIsReady( attempts );
              }, 200 );
          } else {
              console.log('Customizr problem : jQuery.js was not detected on your website');
          }
      };
      // if jQuery has already be printed, let's listen to the load event
      var jquery_script_el = document.querySelectorAll('[src*="wp-includes/js/jquery/jquery.js"]');

      if ( jquery_script_el[0] ) {
          jquery_script_el[0].addEventListener('load', function() {
              _doWhenJqueryIsReady();
          });
      }

      try {_doWhenJqueryIsReady();} catch(er) {
        console.log('tc-init error', er );
      }
})();