/**
 * Contextualizr
 */
jQuery(function ($) {
  var ContextHasBeenUpdated = false;

  _DoAjaxObjSuffixUpdate();

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
        //console.log('response' , response);
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
    self.$('#tc-context').val(TCControlParams.TCContext).trigger('change');
  }

});
