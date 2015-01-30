/**
 * Contextualizr
 */
jQuery(function ($) {
  var ContextHasBeenUpdated = false;

  //add the context param to the ajax query on save
  wp.customize.previewer.query = function() {
    var dirtyCustomized = {};
    wp.customize.each( function ( value, key ) {
      if ( value._dirty ) {
        dirtyCustomized[ key ] = value();
      }
    } );

    return {
      wp_customize: 'on',
      theme: wp.customize.settings.theme.stylesheet,
      customized: JSON.stringify( dirtyCustomized ),
      nonce: this.nonce.preview,
      TCContext: TCControlParams.TCContext
    };
  };


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

});
