/* ===================================================
 * czr-post-formats.js v1.0.1
 * ===================================================
 * (c) 2015 Nicolas Guillaume, Nice, France
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * =================================================== */
( function($) {
   "use strict";

   $( function() {

      if ( ! CZRPostFormatsParams )
         return;
      if ( ! CZRPostFormatsParams.postFormatSections )
         return;

      // temporary workaround to make sure gutenberg elements have been rendered, see https://github.com/presscustomizr/customizr/issues/1576
      setTimeout( function() {
          var _wpPostFormatsInputSelectorClassical    = '#post-formats-select input[name="post_format"]',
              _wpPostFormatsInputSelectorGutenberg    = '.editor-post-format select',
              _gutenbergEditorSelector                = '#editor.block-editor__container',
              _isClassical                            = $(_wpPostFormatsInputSelectorClassical).length > 0,
              _isGutenberg                            = $(_gutenbergEditorSelector).length > 0;

          if ( !( _isClassical || _isGutenberg ) ) {
             return;
          }

          var _onChangePostFromatSelector             = _isClassical ? _wpPostFormatsInputSelectorClassical + ':radio'   : _wpPostFormatsInputSelectorGutenberg,
              _postFormatsMap                         = _.object( _.chain( CZRPostFormatsParams.postFormatSections )
                                                             .map( function( _section ) {
                                                                var _post_format       = _section.replace( '_section', '' ),
                                                                    _mbsectionSelector = '#' + _section + 'id';
                                                                //create a pair [ audio , #audio_sectionid ]
                                                                return [ _post_format, _mbsectionSelector ];
                                                             })
                                                             //remove duplicates
                                                             .compact()
                                                             //values the chain
                                                             .value()
                                                       );//transform the list in an object like { audio: #audio_sectionid, video: #video_sectionid }
          if ( _postFormatsMap ) {
             init();
          }


          function init() {
             //initial Visibility
             setVisibilities( CZRPostFormatsParams.currentPostFormat );
             //bind change
             // Hide/show post format meta box when option changed
             $('body').on( 'change', _onChangePostFromatSelector, function(evt) {
                setVisibilities( $(this).val() );
             });
          }

          function setVisibilities( _val ) {
             //hide all
             $( _.values( _postFormatsMap ).join() ).hide();
             //show selected
             $( _.pluck( [_postFormatsMap], _val ).join() ).show();
          }
      }, 300 );
   });
})( jQuery );