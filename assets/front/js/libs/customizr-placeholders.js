(function($, czrapp, _ ) {
    //czrapp.localized = CZRParams
    czrapp.ready.then( function() {
          //PLACEHOLDER NOTICES
          //two types of notices here :
          //=> the ones that remove the notice only : thumbnails, smartload, sidenav, secondMenu, mainMenu
          //=> and others that removes notices + an html block ( slider, fp ) or have additional treatments ( widget )

          // a placeholder element looks like this:
          //<aside class="tc-placeholder-wrap col-12" data-nonce_handle="027a33be64" data-nonce_id="czrHelpBlockNonce" data-dismiss_action="dismiss_widget_notice" data-position="footer">
          //we retrieve the data attributes from the element
          var _placeholder_wrapper_selector = '.tc-placeholder-wrap',
              _defaults = { //default params
                            remove_action : null,//for slider and fp
                            dismiss_action : null,
                            remove_selector : '',
                            nonce_handle : '',
                            nonce_id : '',
                            position : null,//for widgets
              },
              //get params from element
              _getData = function( $_el ) {
                  _defaults_keys     = _.keys( _defaults );
                  return _.object( _.chain(_defaults_keys ).map( function( key ) {
                                          var _data = $_el.data( key );
                                          return _data ? [ key, _data ] : '';
                                    })
                                    .compact()
                                    .value()
                  );
              },
              _doAjax = function( _query_ ) {
                    var ajaxUrl = czrapp.localized.adminAjaxUrl, dfd = $.Deferred();
                    $.post( ajaxUrl, _query_ )
                          .done( function( _r ) {
                                // Check if the user is logged out.
                                if ( '0' === _r ||  '-1' === _r )
                                  czrapp.errorLog( 'placeHolder dismiss : ajax error for : ', _query_.action, _r );
                          })
                          .fail( function( _r ) {
                                czrapp.errorLog( 'placeHolder dismiss : ajax error for : ', _query_.action, _r );
                          })
                          .always( function() {
                                dfd.resolve();
                          });
                    return dfd.promise();
              },
              // Attempt to fire an ajax call
              //@string _what_  : 'remove' or 'dismiss'
              //@object _params_
              //@remove_action optional removal action server side. Ex : 'remove_slider'
              _ajaxActionDo = function( _what_, _params_ ) {

                    var _query = {},
                        dfd = $.Deferred();

                    if ( ! _.isObject( _params_ ) ) {
                          czrapp.errorLog( 'placeHolder dismiss : wrong params' );
                          return;
                    }

                    //normalizes
                    _params_ = _.extend( _defaults, _params_ );

                    //set query params
                    _query.action = _params_.dismiss_action;

                    //for slider and fp
                    if ( 'remove' == _what_ && ! _.isNull( _params_.remove_action ) )
                      _query.action = _params_.remove_action;

                    //for widgets
                    if ( ! _.isNull( _params_.position ) )
                      _query.position = _params_.position;

                    _query[ _params_.nonce_id ] = _params_.nonce_handle;

                    //fires and resolve promise
                    _doAjax( _query ).done( function() { dfd.resolve(); });
                    return dfd.promise();
              };


          czrapp.$_body
            .on( 'click', '.tc-inline-remove', function( ev ) {
                ev.preventDefault();
                var $_wrapper = $(this).closest( _placeholder_wrapper_selector );

                if ( $_wrapper.length < 1 ) {
                      return;
                }
                var _data     = _getData( $_wrapper );
                _ajaxActionDo( 'remove', _data ).done( function() {
                      //normalizes
                      _data = _.extend( _defaults, _data );
                      $( _data.remove_selector ).fadeOut('slow');
                });

            })
            .on( 'click', '.tc-dismiss-notice', function( ev ) {
                  ev.preventDefault();
                  var $_wrapper = $(this).closest( _placeholder_wrapper_selector );

                  if ( $_wrapper.length < 1 ) {
                        return;
                  }

                  _ajaxActionDo( 'dismiss', _getData( $_wrapper ) ).done( function() {
                        $_wrapper.slideToggle( 'fast' );
                  });
            } );
    });
})(jQuery, czrapp, _ );