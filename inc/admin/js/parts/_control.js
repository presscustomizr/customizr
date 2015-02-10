/**
 * Theme Customizer enhancements for a better user experience.
 * @package Customizr
 * @since Customizr 1.0
 */
(function (wp, $) {
  var api = wp.customize;

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
  });


  $.extend( api.controlConstructor, {
    tc_upload : api.TCUploadControl
  });



  /*
  * Main control dependencies object
  */
  var _controlDependencies = {
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
        'tc_slider_change_default_img_size'
      ],
      callback: function (to) {
        return '0' !== to;
      }
    },
    'tc_post_list_design' : {
      show: {
        controls: [
          'tc_post_list_design_columns',
          'tc_post_list_design_expand_featured',
          'tc_post_list_design_in_blog',
          'tc_post_list_design_in_archive',
          'tc_post_list_design_in_search',
          'tc_post_list_design_thumb_height'
        ],
        callback: function (to) {
          return 'design' == to;
        }
      },
      hide : {
        controls: [
          'tc_post_list_thumb_shape',
          'tc_post_list_thumb_position',
          'tc_post_list_thumb_alternate',
          'tc_post_list_thumb_height'
        ],
        callback: function (to) {
          return 'design' == to;
        }
      }
    },
    'tc_post_list_show_thumb' : {
      controls: [
        'tc_post_list_use_attachment_as_thumb',
        'tc_post_list_default_thumb',
        'tc_post_list_thumb_shape',
        'tc_post_list_thumb_alternate',
        'tc_post_list_thumb_position',
        'tc_post_list_thumb_height'
      ],
      callback: function (to) {
        return '1' == to;
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
        'tc_sticky_logo_upload'
      ],
      callback: function (to) {
        return '1' == to;
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
    }
  };

  /*
  * @return string
  * simple helper to build the setting id name
  */
  var _make_settingId = function ( name ) {
    return [ 'tc_theme_options[' , name  , ']' ].join('');
  };


  /*
  * find the settingId key in the _controlDependencies object
  * get the controls, merge show and hide if needed
  * return an []
  */
  var _get_dependants = function( settingId ) {
    if ( ! _controlDependencies[settingId] )
      return [];
    var _dependants = _controlDependencies[settingId];

    if ( _dependants.show && _dependants.hide )
      return _.union(_dependants.show.controls , _dependants.hide.controls);
    if ( _dependants.show && ! _dependants.hide )
      return _dependants.show.controls;
    if ( ! _dependants.show && _dependants.hide )
      return _dependants.hide.controls;

    return _dependants.controls;
  };

  /*
  * @return void
  * show or hide setting according to the dependency + callback pair
  */
  var _prepare_visibilities = function( settingId, o ) {
    console.log('JOIE', _make_settingId(settingId) , o);
    api( _make_settingId(settingId) , function (setting) {
      var _params = {
        setting   : setting,
        settingId : _make_settingId(settingId),
        controls  : _get_dependants(settingId),
      };

      console.log('CONTROLS' , _params.controls);
      _.map( _params.controls , function( dependantSettingId ) {
        _set_single_dependant_control_visibility( _make_settingId(dependantSettingId) , _params);
      } );
    });
  };


  /*
  * _params = {
        settingId : settingId,
        controls  : {},
        callback  : '',
        action    : 'hide',
      };
   */
  var _set_single_dependant_control_visibility = function( dependantSettingId , _params ) {
    console.log( 'IN _set_control_visibility' , _params.settingId, dependantSettingId );

    api.control( dependantSettingId , function (control) {
      //console.log( 'IN SET CONTROL SINGLE : _make_settingId(dependantSettingId )' , dependantSettingId , ' settingId',  _params.settingId);
      //console.log( 'SHOW ? ', _show, 'settingId' , _params.settingId, 'callback(to)', api.instance(_params.settingId).get() );
      //console.log( 'CALLBACK RESULT' , _params.callback( api.instance( _params.settingId).get() ) );

      var _visibility = function (to) {
        //visible if 'show' == _params.action && _params.callback(to)
        //novisible if 'hide' == _params.action && _params.callback(to)
        console.log('CURRENT CHANGED SETTING' , _params.setting );

        //var _action = _get_action( dependantSettingId
        // var _bool = false;
        // if ( 'show' == _params.action && _params.callback(to) )
        //   _bool = true;
        // if ( 'hide' == _params.action && _params.callback(to) )
        //   _bool = false;
        // console.log( 'to : ', to , '_params.action : ' , _params.action , '_params.callback(to)' , _params.callback(to));
        // console.log( 'SHOW ', dependantSettingId , ' ? : ', _bool );
        control.container.toggle( true );
      };

      _visibility( _params.setting.get() );
      _params.setting.bind( _visibility );
    });
  };

  // var setAdditionalCustomizrControls = function() {
  //   console.log( api( _make_settingId('tc_post_list_show_thumb')).get() );

  //   _set_visibility( _make_settingId('tc_post_list_design'),
  //     {
  //     controls:
  //         1 == api( _make_settingId('tc_post_list_show_thumb')).get() ? [ _make_settingId('tc_post_list_thumb_alternate') ] : [],
  //       callback: function (to) {
  //         return 'design' != to;
  //       }
  //     }
  //   );
  // };//end of setCustomizrControls();


  /* POST LIST DESIGN */
  //Set dependencies with
  //- thumbnails positions
  //- thumbnail shapes
  var postListDesignDependencies = function( _changed_val ) {
    var _dependants = [
      { id : 'tc_post_list_thumb_shape' },
      { id : 'tc_post_list_thumb_position' , val_to : 'top' },
      { id : 'tc_post_list_thumb_alternate' },
      { id : 'tc_post_list_thumb_height' }
      ];

    var _update_dependants = function( dep ) {
      var _control_id = _make_settingId( dep.id );

      if ( dep.val_to )
        api.control(_control_id).setting.set(dep.val_to);

      if ( 'design' == _changed_val ) {
        //destroy and re built if select type
        $( 'select' , api.control(_control_id).selector ).selecter("destroy").selecter();
        $( api.control(_control_id).selector ).hide();
      } else {
        $( api.control(_control_id).selector ).show();
      }
    };

    _.map( _dependants , _update_dependants );

    //trigger change to fire the hidden/shown control rules
    _.map( _dependants , function() {

    } );
  };


  //bind to wp.customize ready event
  //map each setting with its dependencies
  api.bind( 'ready' , function() {
    _.map( _controlDependencies , function( opts , settingId ) {
      if ( 'tc_post_list_design' == settingId )
        _prepare_visibilities( settingId, opts );
    });

    //setAdditionalCustomizrControls();
    //postListDesignDependencies();
    //api( _make_settingId('tc_post_list_design')).bind('change' , postListDesignDependencies );
  } );

})( wp, jQuery);