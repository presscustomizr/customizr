/**
 * Contextualizr
 */
(function( exports, $ ){

  var ContextHasBeenUpdated = false,
      api = wp.customize;

  overridePreviewerQuery = function() {
    console.log('API READY : TCControlParams.TCContext' , TCControlParams.TCContext);
    //add the context param to the ajax query on save
    api.previewer.query = function() {
      var dirtyCustomized = {};
      wp.customize.each( function ( value, key ) {
        if ( value._dirty ) {
          dirtyCustomized[ key ] = value();
        }
      } );

      return {
        wp_customize: 'on',
        theme: api.settings.theme.stylesheet,
        customized: JSON.stringify( dirtyCustomized ),
        nonce: this.nonce.preview,
        TCContext: TCControlParams.TCContext.complete || ''
      };
    };
  };

  api.bind( 'ready' , overridePreviewerQuery );

  //DOM READY
  $(function($) {

    // $( document ).on( 'widget-synced' , function( response ) {
    //   console.log('response widget-synced in _contextualizer' , response);
    // } );
    //Replace default wp title
    //$('.preview-notice', '.panel-meta').first()
    $_title = $('.panel-title' , '.panel-meta').first();

    //display a context box right below the main title
    /*function _render_context_block() {
      // Grab the HTML out of our template tag and pre-compile it.
      var main_cta = _.template(
          $( "script#main_cta" ).html()
      );
      $('#customize-info').after( main_cta() );
    }*/


  });//end of DOM READY


  //refresh on load
  //_DoAjaxObjSuffixUpdate();

  function _DoAjaxObjSuffixUpdate(){
    var AjaxUrl         = TCControlParams.AjaxUrl,
        self            = this,
        query = {
            action        : 'tc_update_context',
            TCnonce       : TCControlParams.TCNonce,
            TCContext     : TCControlParams.TCContext
        },
        request = $.post( AjaxUrl, query );

    //console.log('request' , request);
    request.done( function( response ) {
        console.log('response in _DoAjaXObjSuffixUpdate' , response);
        // Check if the user is logged out.
        if ( '0' === response ) {
            return;
        }
        // Check for cheaters.
        if ( '-1' === response ) {
            return;
        }
        _update_suffix(response);
    });
  }


  function _update_suffix(response){
    if ( ContextHasBeenUpdated )
      return;
    ContextHasBeenUpdated = true;
    //updates the hidden obj suffix setting => used to avoid cross customization, @see action hooked on 'customize_save'
    $('#tc-context').val(TCControlParams.TCContext).trigger('change');
  }

})( wp, jQuery );
