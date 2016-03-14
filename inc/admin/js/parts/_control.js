/**
 * Theme Customizer enhancements for a better user experience.
 * @package Customizr
 * @since Customizr 1.0
 */
(function (wp, $, _) {
  var api = wp.customize,
      $_nav_section_container,
      translatedStrings = TCControlParams.translatedStrings || {};

  api.bind( 'ready' , function() {
    _setControlVisibilities();
  } );


  //FIX FOR CONTROL VISIBILITY LOST ON PREVIEW REFRESH #1
  //This solves the problem of control visiblity settings being lost on preview refresh since WP 4.3
  //this overrides the wp method only for the control instances
  //it check if there's been a customizations
  //=> args.unchanged is true for all cases, for example when api.previewer.loading and the preview send 'ready'created during the frame synchronisation
  api.Control.prototype.onChangeActive = function ( active, args ) {
    if ( args.unchanged )
      return;
    if ( this.container[0] && ! $.contains( document, this.container[0] ) ) {
      // jQuery.fn.slideUp is not hiding an element if it is not in the DOM
      this.container.toggle( active );
      if ( args.completeCallback ) {
        args.completeCallback();
      }
    } else if ( active ) {
      this.container.slideDown( args.duration, args.completeCallback );
    } else {
      this.container.slideUp( args.duration, args.completeCallback );
    }
  };


  //FIX FOR SECTION CONTENT HIDDEN BY THE FOOTER
  //Problem fixed : since WP4.5, the footer of the customizer includes the device switcher
  //but there's aso the rating link there.
  //Therefore, in sections higher than the viewport, some content might be hidden
  //This is fixed on each section expanded event
  api.bind('ready', function() {
    //wp.customize.Section is not available before wp 4.1
    if ( 'function' != typeof (api.Section) )
      return;
    _.map( api.settings.sections, function( section, id ) {

      var _section = api.section(id);
      _section.expanded.callbacks.add( function( _expanded ) {
          if ( ! _expanded )
            return;
          var $container = _section.container.closest( '.wp-full-overlay-sidebar-content' ),
                $content = _section.container.find( '.accordion-section-content' );
            //content resizing to the container height
            _resizeContentHeight = function() {
              $content.css( 'height', $container.innerHeight() );
          };
          _resizeContentHeight();
          //this is set to off in the original expand callback if 'expanded' is false
          $( window ).on( 'resize.customizer-section', _.debounce( _resizeContentHeight, 110 ) );
        }
      );//add
    });//_.map
  });


  /* Multiple Picker */
  /**
   * @constructor
   * @augments wp.customize.Control
   * @augments wp.customize.Class
   */
  api.TCMultiplePickerControl = api.Control.extend({
    ready: function() {
      var control  = this,
          _select  = this.container.find('select');

      //handle case when all choices become unselected
      _select.on('change', function(e){
        if ( 0 === $(this).find("option:selected").length )
          control.setting.set([]);
      });
    }
  });
  $.extend( api.controlConstructor, {
    tc_multiple_picker : api.TCMultiplePickerControl
  });



  /* IMAGE UPLOADER CONTROL IN THE CUSTOMIZER */
  //CroppedImageControl is not available before wp 4.3
  if ( ('function' == typeof wp.media.controller.Cropper ) && ( 'function' == typeof api.CroppedImageControl ) ) {
    /* TCCustomizeImage Cropper */
    /**
    * Custom version of:
    * wp.media.controller.CustomizeImageCropper (wp-includes/js/media-views.js)
    *
    * In order to use image destination sizes different than the suggested ones
    *
    * A state for cropping an image.
    *
    * @class
    * @augments wp.media.controller.Cropper
    * @augments wp.media.controller.State
    * @augments Backbone.Model
    */
    wp.media.controller.TCCustomizeImageCropper = wp.media.controller.Cropper.extend({
      doCrop: function( attachment ) {
        var cropDetails = attachment.get( 'cropDetails' ),
            control = this.get( 'control' );

        cropDetails.dst_width  = control.params.dst_width;
        cropDetails.dst_height = control.params.dst_height;

        return wp.ajax.post( 'crop-image', {
            wp_customize: 'on',
            nonce: attachment.get( 'nonces' ).edit,
            id: attachment.get( 'id' ),
            context: control.id,
            cropDetails: cropDetails
        } );
      }
    });

    /* TCCroppedImageControl */
    /**
    * @constructor
    * @augments wp.customize.CroppedImageControl
    * @augments wp.customize.Class
    */
    api.TCCroppedImageControl = api.CroppedImageControl.extend({
      /**
      * Create a media modal select frame, and store it so the instance can be reused when needed.
      * TC: We don't want to crop svg (cropping fails), gif (animated gifs become static )
      * @Override
      * We need to override this in order to use our ImageCropper custom extension of wp.media.controller.Cropper
      *
      * See api.CroppedImageControl:initFrame() ( wp-admin/js/customize-controls.js )
      */
      initFrame: function() {

        var l10n = _wpMediaViewsL10n;

        this.frame = wp.media({
            button: {
                text: l10n.select,
                close: false
            },
            states: [
                new wp.media.controller.Library({
                    title: this.params.button_labels.frame_title,
                    library: wp.media.query({ type: 'image' }),
                    multiple: false,
                    date: false,
                    priority: 20,
                    suggestedWidth: this.params.width,
                    suggestedHeight: this.params.height
                }),
                new wp.media.controller.TCCustomizeImageCropper({
                    imgSelectOptions: this.calculateImageSelectOptions,
                    control: this
                })
            ]
        });

        this.frame.on( 'select', this.onSelect, this );
        this.frame.on( 'cropped', this.onCropped, this );
        this.frame.on( 'skippedcrop', this.onSkippedCrop, this );
      },

      /**
      * After an image is selected in the media modal, switch to the cropper
      * state if the image isn't the right size.
      *
      * TC: We don't want to crop svg (cropping fails), gif (animated gifs become static )
      * @Override
      * See api.CroppedImageControl:onSelect() ( wp-admin/js/customize-controls.js )
      */
      onSelect: function() {
        var attachment = this.frame.state().get( 'selection' ).first().toJSON();
        if ( ! ( attachment.mime && attachment.mime.indexOf("image") > -1 ) ){
          //Todo: better error handling, show some message?
          this.frame.trigger( 'content:error' );
          return;
        }
        if ( ( _.contains( ['image/svg+xml', 'image/gif'], attachment.mime ) ) || //do not crop gifs or svgs
                this.params.width === attachment.width && this.params.height === attachment.height && ! this.params.flex_width && ! this.params.flex_height ) {
            this.setImageFromAttachment( attachment );
            this.frame.close();
        } else {
            this.frame.setState( 'cropper' );
        }
      },
    });//end Controller

    $.extend( api.controlConstructor, {
      tc_cropped_image : api.TCCroppedImageControl
    });
  }//endif




  /**
   * @constructor
   * @augments wp.customize.Control
   * @augments wp.customize.Class
   */
  api.TCUploadControl = api.Control.extend({
    ready: function() {
      var control = this;

      this.params.removed = this.params.removed || '';

      this.success = $.proxy( this.success, this );

      this.uploader = $.extend({
        container: this.container,
        browser:   this.container.find('.tc-upload'),
        //dropzone:  this.container.find('.upload-dropzone'),
        success:   this.success,
        plupload:  {},
        params:    {}
      }, this.uploader || {} );

      if ( control.params.extensions ) {
        control.uploader.plupload.filters = [{
          title:      api.l10n.allowedFiles,
          extensions: control.params.extensions
        }];
      }

      if ( control.params.context )
        control.uploader.params['post_data[context]'] = this.params.context;

      if ( api.settings.theme.stylesheet )
        control.uploader.params['post_data[theme]'] = api.settings.theme.stylesheet;

      this.uploader = new wp.Uploader( this.uploader );

      this.remover = this.container.find('.remove');
      this.remover.on( 'click keydown', function( event ) {
        if ( event.type === 'keydown' &&  13 !== event.which ) // enter
          return;
        control.setting.set( control.params.removed );
        event.preventDefault();
      });

      this.removerVisibility = $.proxy( this.removerVisibility, this );
      this.setting.bind( this.removerVisibility );
      this.removerVisibility( this.setting.get() );
    },
    success: function( attachment ) {
      this.setting.set( attachment.get('id') );
    },
    removerVisibility: function( to ) {
      this.remover.toggle( to != this.params.removed );
    }
  });//api.Control.extend()


  $.extend( api.controlConstructor, {
    tc_upload : api.TCUploadControl
  });




  //bind all actions to wp.customize ready event
  //map each setting with its dependencies
  var _setControlVisibilities = function() {
    _.map( _controlDependencies , function( opts , setId ) {
      _prepare_visibilities( setId, opts );
    });

    //additional dependencies
    _handle_grid_dependencies();
    _header_layout_dependency();

    //favicon note on load and on change(since wp 4.3)
    _handleFaviconNote();

    //nav section visibilities
    //=> backward compat if api.section not defined
    if ( 'function' == typeof api.section ) {
      $_nav_section_container = api.section('nav').container;
      //on nav section open
      api.section('nav').expanded.callbacks.add( function() {
        _hideAllmenusActions( api('tc_theme_options[tc_hide_all_menus]').get() );
      });//add()
    } else {
      $_nav_section_container = $('li#accordion-section-nav');
      //on nav section open
      $_nav_section_container.on( 'click keydown', '.accordion-section-title', function(event) {
        //special treatment for click events
        if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
          return;
        }
        event.preventDefault(); // Keep this AFTER the key filter above)

        _hideAllmenusActions( api('tc_theme_options[tc_hide_all_menus]').get() );
      });//on()
    }//else

    //specific callback for the tc_hide_all_menus setting
    api('tc_theme_options[tc_hide_all_menus]').callbacks.add( _hideAllmenusActions );
  };


  /*
  * Main control dependencies object
  */
  var _controlDependencies = {
    //we have to show restrict blog/home posts when
    //1. show page on front and a page of posts is selected
    //2, show posts on front
    'page_for_posts' : {
       controls: [
         'tc_blog_restrict_by_cat',
       ],
       callback : function (to) {
         return '0' !== to;
       },
    },
    'show_on_front' : {
      controls: [
        'tc_blog_restrict_by_cat',
        'tc_show_post_navigation_home'
      ],
      callback : function (to, targetSetId) {
        if ( 'posts' == to )
          return true;
        if ( 'page' == to && 'tc_blog_restrict_by_cat' == targetSetId ) //show cat picker also if a page for posts is set
          return '0' !== api( _build_setId('page_for_posts') ).get() ;
        return false;
      },

    },
    'tc_logo_upload' : {
      controls: [
          'tc_logo_resize'
      ],
      callback : function( to ) {
        return _.isNumber( to );
      }
    },
    'tc_show_featured_pages': {
      controls: TCControlParams.FPControls,
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_front_slider': {
      controls: [
        'tc_slider_width',
        'tc_slider_delay',
        'tc_slider_default_height',
        'tc_slider_default_height_apply_all',
        'tc_slider_change_default_img_size',
        'tc_posts_slider_number',
        'tc_posts_slider_stickies',
        'tc_posts_slider_title',
        'tc_posts_slider_text',
        'tc_posts_slider_link',
        'tc_posts_slider_button_text',
        'tc_posts_slider_restrict_by_cat' //tc-pro-bundle
      ],
      callback: function (to, targetSetId) {
        //posts slider options must be hidden when the posts slider not choosen
        if ( targetSetId.indexOf('tc_posts_slider_') > -1 )
          return 'tc_posts_slider' == to;

        //if user select the post slider option, append a notice in the label element
        //and hide the notice when no sliders have been created yet
        var $_front_slider_container = api.control( _build_setId('tc_front_slider') ).container,
            $_label = $( 'label' , $_front_slider_container ),
            $_empty_sliders_notice = $( 'div.tc-notice', $_front_slider_container);

        if ( 'tc_posts_slider' == to ) {
          if ( 0 !== $_label.length && ! $('.tc-notice' , $_label ).length ) {
            var $_notice = $('<span>', { class: 'tc-notice', html : translatedStrings.postSliderNote || '' } );
            $_label.append( $_notice );
          } else {
            $('.tc-notice' , $_label ).show();
          }
          //hide no sliders created notice
          if ( 0 !== $_empty_sliders_notice.length )
            $_empty_sliders_notice.hide();
        } else {
          if ( 0 !== $( '.tc-notice' , $_label ).length )
            $( '.tc-notice' , $_label ).hide();
          if ( 0 !== $_empty_sliders_notice.length )
            $_empty_sliders_notice.show();
        }
        return '0' !== to;
      }//callback
    },
    'tc_slider_default_height' : {
      controls: [
        'tc_slider_default_height_apply_all',
        'tc_slider_change_default_img_size'
      ],
      callback: function (to, targetSetId) {
        //slider height options must be hidden is height = default height (500px), unchanged by user
        var _defaultHeight = TCControlParams.defaultSliderHeight || 500;
        return _defaultHeight != to;
      }
    },
    'tc_posts_slider_link' : {
      controls: [
        'tc_posts_slider_button_text'
      ],
      callback: function (to) {
        return to.indexOf('cta') > -1;
      },
      //display dependant if master setting value == value
      cross: {
        tc_posts_slider_button_text : { master : 'tc_front_slider' , callback : function (to) { return 'tc_posts_slider' == to; } },
      }
    },
    'tc_post_list_grid' : {
      show: {
        controls: [
          'tc_grid_columns',
          'tc_grid_expand_featured',
          'tc_grid_in_blog',
          'tc_grid_in_archive',
          'tc_grid_in_search',
          'tc_grid_thumb_height',
          'tc_grid_bottom_border',
          'tc_grid_shadow',
          'tc_grid_icons',
          'tc_grid_num_words'

        ],
        callback: function (to) {
          return 'grid' == to;
        }
      }
    },
    'tc_post_list_thumb_shape' : {
      controls: [
        'tc_post_list_thumb_height'
      ],
      callback: function (to) {
        return to.indexOf('rectangular') > -1;
      }
    },
    'tc_post_list_thumb_position' : {
      controls: [
        'tc_post_list_thumb_alternate'
      ],
      callback: function (to) {
        return _.contains( [ 'left', 'right'], to );
      }
    },
    'tc_post_list_show_thumb' : {
      controls: [
        'tc_post_list_use_attachment_as_thumb',
        'tc_post_list_default_thumb',
        'tc_post_list_thumb_shape',
        'tc_post_list_thumb_alternate',
        'tc_post_list_thumb_position',
        'tc_post_list_thumb_height',
        'tc_grid_thumb_height'
      ],
      callback: function (to) {
        return '1' == to;
      },
      //display dependant if master setting value == value
      cross: {
        tc_post_list_thumb_height : { master : 'tc_post_list_thumb_shape' , callback : function (to) { return to.indexOf('rectangular') > -1; } },
        tc_post_list_thumb_alternate: { master: 'tc_post_list_thumb_position', callback: function (to) { return _.contains( [ 'left', 'right'], to ); } }
      }
    },
    'tc_breadcrumb' : {
      controls: [
        'tc_show_breadcrumb_home',
        'tc_show_breadcrumb_in_pages',
        'tc_show_breadcrumb_in_single_posts',
        'tc_show_breadcrumb_in_post_lists'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_show_title_icon' : {
      controls: [
        'tc_show_page_title_icon',
        'tc_show_post_title_icon',
        'tc_show_archive_title_icon',
        'tc_show_post_list_title_icon',
        'tc_show_sidebar_widget_icon',
        'tc_show_footer_widget_icon'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_show_post_metas' : {
      controls: [
        'tc_show_post_metas_home',
        'tc_post_metas_design',
        'tc_show_post_metas_single_post',
        'tc_show_post_metas_post_lists',
        'tc_show_post_metas_categories',
        'tc_show_post_metas_tags',
        'tc_show_post_metas_publication_date',
        'tc_show_post_metas_update_date',
        'tc_post_metas_update_notice_text',
        'tc_post_metas_update_notice_interval',
        'tc_show_post_metas_author'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_show_post_metas_update_date' : {
      controls: [
        'tc_post_metas_update_date_format',
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_post_metas_update_notice_in_title' : {
      controls: [
        'tc_post_metas_update_notice_text',
        'tc_post_metas_update_notice_format',
        'tc_post_metas_update_notice_interval'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_post_list_length' : {
      controls: [
        'tc_post_list_excerpt_length',
      ],
      callback: function (to) {
        return 'excerpt' == to;
      }
    },
    'tc_sticky_show_title_logo' : {
      controls: [
        'tc_sticky_logo_upload',
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_sticky_header' : {
      controls: [
        'tc_sticky_show_tagline',
        'tc_sticky_show_title_logo',
        'tc_sticky_shrink_title_logo',
        'tc_sticky_show_menu',
        'tc_sticky_transparent_on_scroll',
        'tc_sticky_logo_upload',
        'tc_woocommerce_header_cart_sticky'
      ],
      callback: function (to, targetSetId) {
        return '1' == to;
      },
      cross: {
        tc_woocommerce_header_cart_sticky : { master : 'tc_woocommerce_header_cart' , callback : function (to, tID, changedSetId ) { 
          return to &&  //api.control.active is available since wp 4.0 as the php active_callback
            //so let's skip this for older wp versions
            ( 'function' == typeof api.control.active ? api.control( _build_setId( changedSetId ) ).active() : true );
        } }
      }
    },
    'tc_comment_bubble_color_type' : {
      controls: [
        'tc_comment_bubble_color',
      ],
      callback: function (to) {
        return 'custom' == to;
      }
    },
    'tc_comment_show_bubble' : {
      controls: [
        'tc_comment_bubble_shape',
        'tc_comment_bubble_color_type',
        'tc_comment_bubble_color'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_enable_dropcap' : {
      controls: [
        'tc_dropcap_minwords',
        'tc_dropcap_design',
        'tc_post_dropcap',
        'tc_page_dropcap'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_enable_gallery' : {
      controls: [
        'tc_gallery_fancybox',
        'tc_gallery_style',
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_skin_random' : { /* hack */
      controls: [
        'tc_skin',
      ],
      callback: function (to) {
        var $_skin_select = $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]');

        $_skin_select.prop('disabled', '1' == to ? 'disabled' : '' );
        return true;
      }
    },
    'tc_show_post_navigation' : {
      controls: [
        'tc_show_post_navigation_page',
        'tc_show_post_navigation_home',
        'tc_show_post_navigation_single',
        'tc_show_post_navigation_archive'
      ],
      callback: function (to) {
        return '1' == to;
      },
      //display dependant if master setting value == value
      cross: {
        tc_show_post_navigation_home : { master : 'show_on_front' , callback : function (to) { return 'posts' == to; } },
      }
    },
    'tc_display_second_menu' : {
      show : {
        controls: [
          'nav_menu_locations[secondary]',
          'tc_second_menu_position',
          'tc_second_menu_resp_setting',
          'tc_menu_type',
          'tc_menu_submenu_fade_effect',
          'tc_menu_submenu_item_move_effect'
        ],
        //the menu style must be aside for secondary menu controls
        callback: function (to, targetSetId, changedSetId) {
          //second menu speicifics
          if ( _.contains( ['nav_menu_locations[secondary]', 'tc_second_menu_resp_setting'], targetSetId ) )
            return '1' == to && 'aside' == api( _build_setId( 'tc_menu_style' )).get();
          //effects common to regular menu and second horizontal menu
          if ( _.contains( ['tc_menu_submenu_fade_effect', 'tc_menu_submenu_item_move_effect'], targetSetId ) )
            return ( '1' == to && 'aside' == api( _build_setId( 'tc_menu_style' )).get() ) || ('1' != to && 'aside' != api( _build_setId( 'tc_menu_style' )).get() );
          return '1' == to;
        }
      }
      // hide : {
      //   controls: [
      //     'tc_display_menu_label'
      //   ],
      //   callback: function (to) {
      //     return 'aside' != to;
      //   }
      // }
    },
    'tc_menu_style' : {
      show : {
        controls: [
          'tc_menu_type',
          'tc_menu_submenu_fade_effect',
          'tc_menu_submenu_item_move_effect',
          'tc_menu_resp_dropdown_limit_to_viewport',
          'tc_display_menu_label',
          'tc_display_second_menu',
          'tc_second_menu_position',
          'nav_menu_locations[secondary]',
          'tc_second_menu_resp_setting',
          'tc_mc_effect'
        ],
        //if the second menu is activated, only the tc_menu_resp_dropdown_limit_to_viewport is hidden
        //otherwise all of them are hidden
        callback: function (to, targetSetId, changedSetId) {
          //CASE 1 : regular menu choosen
          if ( 'aside' != to ) {
            if ( _.contains([
                'tc_display_menu_label',
                'tc_display_second_menu',
                'nav_menu_locations[secondary]',
                'tc_second_menu_position',
                'tc_second_menu_resp_setting',
                'tc_mc_effect'] , targetSetId ) ) {
              return false;
            } else {
              return true;
            }
          }
          //CASE 2 : side menu choosen
          else {
            if ( _.contains([
              'tc_menu_type',
              'tc_menu_submenu_fade_effect',
              'tc_menu_submenu_item_move_effect',
              'nav_menu_locations[secondary]',
              'tc_second_menu_position',
              'tc_second_menu_resp_setting'],
              targetSetId ) ) {
                return true === api( _build_setId('tc_display_second_menu') ).get();
            }
            else if ( 'tc_menu_resp_dropdown_limit_to_viewport' == targetSetId ){
              return false;
            }
            return true;
          }
        }
      }
    },
    'tc_woocommerce_header_cart' : {
      controls: [
        'tc_woocommerce_header_cart_sticky'
      ],
      callback: function (to, tID , changedSetId) {
        return to &&  //api.control.active is available since wp 4.0 as the php active_callback
        //so let's skip this for older wp versions
        ( 'function' == typeof api.control.active ? api.control( _build_setId( changedSetId ) ).active() : true );
      },
      //display dependant if master setting value == value
      cross: {
        tc_woocommerce_header_cart_sticky : { master : 'tc_sticky_header' , callback : function (to) { 
            return to; 
        } },
      }
    }
  };


  /*
  * @return string
  * simple helper to build the setting id name
  */
  var _build_setId = function ( name ) {
    //first check if the current setting id is a customizr one (can be WP built in like nav_menu_locations[{$location}])
    //=> all customizer theme settings starts by "tc_" by convention
    if ( -1 == name.indexOf( 'tc_' ) )
      return name;
    return -1 == name.indexOf( 'tc_theme_options') ? [ 'tc_theme_options[' , name  , ']' ].join('') : name;
  };


  /*
  * find the setId key in the _controlDependencies object
  * get the controls, merge show and hide if needed
  * return an []
  */
  var _get_dependants = function( setId ) {
    if ( ! _controlDependencies[setId] )
      return [];
    var _dependants = _controlDependencies[setId];

    if ( _dependants.show && _dependants.hide )
      return _.union(_dependants.show.controls , _dependants.hide.controls);
    if ( _dependants.show && ! _dependants.hide )
      return _dependants.show.controls;
    if ( ! _dependants.show && _dependants.hide )
      return _dependants.hide.controls;

    return _dependants.controls;
  };

  /*
  * @return string hide or show. default is hide
  */
  var _get_visibility_action = function ( setId , depSetId ) {
    if ( ! _controlDependencies[setId] )
      return 'both';
    var _dependants = _controlDependencies[setId];
    if ( _dependants.show && -1 != _.indexOf( _dependants.show.controls, depSetId ) )
      return 'show';
    if ( _dependants.hide && -1 != _.indexOf( _dependants.hide.controls, depSetId ) )
      return 'hide';
    return 'both';
  };


  var _get_visibility_cb = function( setId , _action ) {
    if ( ! _controlDependencies[setId] )
      return;
    var _dependants = _controlDependencies[setId];
    if ( ! _dependants[_action] )
      return _dependants.callback;
    return (_dependants[_action]).callback;
  };


  var _check_cross_dependant = function( setId, depSetId ) {
    if ( ! _controlDependencies[setId] )
      return true;
    var _dependants = _controlDependencies[setId];
    if ( ! _dependants.cross || ! _dependants.cross[depSetId] )
      return true;
    var _cross  = _dependants.cross[depSetId],
        _id     = _cross.master,
        _cb     = _cross.callback;

    _id = _build_setId(_id);
    //if _cb returns true => show
    return _cb( api.instance(_id).get() );
  };

  /*
  * @return void
  * show or hide setting according to the dependency + callback pair
  */
  var _prepare_visibilities = function( setId, o ) {
    api( _build_setId(setId) , function (setting) {
      var _params = {
        setting   : setting,
        setId : setId,
        controls  : _get_dependants(setId),
      };
      _.map( _params.controls , function( depSetId ) {
        _set_single_dependant_control_visibility( depSetId , _params);
      } );
    });
  };


  /*
  *
  */
  var _set_single_dependant_control_visibility = function( depSetId , _params ) {
    api.control( _build_setId(depSetId) , function (control) {
      var _visibility = function (to) {
        var _action   = _get_visibility_action( _params.setId , depSetId ),
            _callback = _get_visibility_cb( _params.setId , _action ),
            _bool     = false;

        if ( 'show' == _action && _callback(to, depSetId, _params.setId ) )
          _bool = true;
        if ( 'hide' == _action && _callback(to, depSetId, _params.setId ) )
          _bool = false;
        if ( 'both' == _action )
          _bool = _callback(to, depSetId, _params.setId );

        //check if there are any cross dependencies to look at
        //_check_cross_dependant return true if there are no cross dependencies.
        //if cross dependency :
        //1) return true if we must show, false if not.
        _bool = _check_cross_dependant( _params.setId, depSetId ) && _bool;
        control.container.toggle( _bool );
      };//_visibility()



      _visibility( _params.setting.get() );
      _params.setting.bind( _visibility );
    });
  };


  /*
  * Specific Grid action : handles the visibility of the "MORE GRID DESIGN OPTIONS" link
  * @to do => find a way to include several callbacks in the _controlDependencies object => include the one below
  */
  var _handle_grid_dependencies = function() {
    //apply visibility on ready
    var _is_grid_enabled = api('tc_theme_options[tc_post_list_grid]') && 'grid' == api('tc_theme_options[tc_post_list_grid]').get();
    $('.tc-grid-toggle-controls').toggle( _is_grid_enabled );

    //bind visibility on setting changes
    api.instance('tc_theme_options[tc_post_list_grid]').bind( function(to) {
      $('.tc-grid-toggle-controls').toggle( 'grid' == to );

      if ( 'grid' == to )
        $('.tc-grid-toggle-controls').trigger('click').toggleClass('open');
    } );
  };


  /**
  * Dependency between the header layout and the menu position, when the menu style is Side Menu
  */
  var _header_layout_dependency = function() {
    //when user switch layout, make sure the menu is correctly aligned by default.
    api('tc_theme_options[tc_header_layout]').callbacks.add( function(to) {
      api('tc_theme_options[tc_menu_position]').set( 'right' == to ? 'pull-menu-left' : 'pull-menu-right' );
      //refresh the selecter
      api.control('tc_theme_options[tc_menu_position]').container.find('select').selecter('destroy').selecter({});
    } );

    //when user changes the menu syle (side or regular), refresh the menu position according to the header layout
    api('tc_theme_options[tc_menu_style]').callbacks.add( function(to) {
      var _header_layout = api('tc_theme_options[tc_header_layout]').get();
      api('tc_theme_options[tc_menu_position]').set( 'left' == _header_layout ? 'pull-menu-right' : 'pull-menu-left' );
      //refresh the selecter
      api.control('tc_theme_options[tc_menu_position]').container.find('select').selecter('destroy').selecter({});
    } );
  };


  //change the 'nav' section controls opacity based on the booleand value of a setting (tc_theme_options[tc_hide_all_menus])
  var _hideAllmenusActions = function(to, from, setId) {
    setId = setId ||'tc_theme_options[tc_hide_all_menus]';
    var $_controls = $_nav_section_container.find('li.customize-control').not( api.control(setId).container );
    $_controls.each( function() {
      if ( $(this).is(':visible') )
        $(this).fadeTo( 500 , true === to ? 0.5 : 1); //.fadeTo() duration, opacity, callback
    });//$.each()
  };


  /**
  * Fired on api ready
  * May change the site_icon description on load
  * May add a callback to site_icon
  * @return void()
  */
  var _handleFaviconNote = function() {
    //do nothing if (||)
    //1) WP version < 4.3 where site icon has been introduced
    //2) User had not defined a Customizr favicon
    //3) User has already set WP site icon
    if ( ! api.has('site_icon') || 0 === + api( _build_setId('tc_fav_upload') ).get() || + api('site_icon').get() > 0 )
      return;

    var _oldDes     = api.control('site_icon').params.description;
        _newDes     = ['<strong>' , translatedStrings.faviconNote || '' , '</strong><br/><br/>' ].join('') + _oldDes;

    //on api ready
    _printFaviconNote(_newDes );

    //on site icon change
    api('site_icon').callbacks.add( function(to) {
      if ( +to > 0 ) {
        //reset the description to default
        api.control('site_icon').container.find('.description').text(_oldDes);
        //reset the previous customizr favicon setting
        api( _build_setId('tc_fav_upload') ).set("");
      }
      else {
        _printFaviconNote(_newDes );
      }
    });
  };

  //Add a note to the WP control description if user has already defined a favicon with Customizr
  var _printFaviconNote = function( _newDes ) {
    api.control('site_icon').container.find('.description').html(_newDes);
  };

})( wp, jQuery, _);
