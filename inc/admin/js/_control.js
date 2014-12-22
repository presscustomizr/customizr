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


  $.each({
    'tc_theme_options[tc_show_featured_pages]': {
      controls: TCControlParams.FPControls,
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_front_slider]': {
      controls: [
        'tc_theme_options[tc_slider_width]',
        'tc_theme_options[tc_slider_delay]',
        'tc_theme_options[tc_slider_default_height]',
        'tc_theme_options[tc_slider_default_height_apply_all]',
        'tc_theme_options[tc_slider_change_default_img_size]'
      ],
      callback: function (to) {
        return '0' !== to;
      }
    },
    'tc_theme_options[tc_post_list_show_thumb]' : {
      controls: [
        'tc_theme_options[tc_post_list_use_attachment_as_thumb]',
        'tc_theme_options[tc_post_list_thumb_shape]',
        'tc_theme_options[tc_post_list_thumb_alternate]',
        'tc_theme_options[tc_post_list_thumb_position]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_post_list_thumb_shape]' : {
      controls: [
        'tc_theme_options[tc_post_list_thumb_height]'
      ],
      callback: function (to) {
        return to.indexOf('rectangular') > -1;
      }
    },
    'tc_theme_options[tc_breadcrumb]' : {
      controls: [
        'tc_theme_options[tc_show_breadcrumb_home]',
        'tc_theme_options[tc_show_breadcrumb_in_pages]',
        'tc_theme_options[tc_show_breadcrumb_in_single_posts]',
        'tc_theme_options[tc_show_breadcrumb_in_post_lists]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_show_title_icon]' : {
      controls: [
        'tc_theme_options[tc_show_page_title_icon]',
        'tc_theme_options[tc_show_post_title_icon]',
        'tc_theme_options[tc_show_archive_title_icon]',
        'tc_theme_options[tc_show_post_list_title_icon]',
        'tc_theme_options[tc_show_sidebar_widget_icon]',
        'tc_theme_options[tc_show_footer_widget_icon]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_show_post_metas]' : {
      controls: [
        'tc_theme_options[tc_show_post_metas_home]',
        'tc_theme_options[tc_show_post_metas_single_post]',
        'tc_theme_options[tc_show_post_metas_post_lists]',
        'tc_theme_options[tc_show_post_metas_categories]',
        'tc_theme_options[tc_show_post_metas_tags]',
        'tc_theme_options[tc_show_post_metas_publication_date]',
        'tc_theme_options[tc_show_post_metas_update_date]',
        'tc_theme_options[tc_post_metas_update_notice_text]',
        'tc_theme_options[tc_post_metas_update_notice_interval]',
        'tc_theme_options[tc_show_post_metas_author]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_show_post_metas_update_date]' : {
      controls: [
        'tc_theme_options[tc_post_metas_update_date_format]',
        'tc_theme_options[tc_post_metas_update_notice_in_title]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_post_metas_update_notice_in_title]' : {
      controls: [
        'tc_theme_options[tc_post_metas_update_notice_text]',
        'tc_theme_options[tc_post_metas_update_notice_format]',
        'tc_theme_options[tc_post_metas_update_notice_interval]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_post_list_length]' : {
      controls: [
        'tc_theme_options[tc_post_list_excerpt_length]',
      ],
      callback: function (to) {
        return 'excerpt' == to;
      }
    },
    'tc_theme_options[tc_sticky_show_title_logo]' : {
      controls: [
        'tc_theme_options[tc_sticky_logo_upload]',
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_sticky_header]' : {
      controls: [
        'tc_theme_options[tc_sticky_show_tagline]',
        'tc_theme_options[tc_sticky_show_title_logo]',
        'tc_theme_options[tc_sticky_shrink_title_logo]',
        'tc_theme_options[tc_sticky_show_menu]',
        'tc_theme_options[tc_sticky_transparent_on_scroll]',
        'tc_theme_options[tc_sticky_logo_upload]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_theme_options[tc_comment_bubble_color_type]' : {
      controls: [
        'tc_theme_options[tc_comment_bubble_color]',
      ],
      callback: function (to) {
        return 'custom' == to;
      }
    },
    'tc_theme_options[tc_comment_show_bubble]' : {
      controls: [
        'tc_theme_options[tc_comment_bubble_shape]',
        'tc_theme_options[tc_comment_bubble_color_type]',
        'tc_theme_options[tc_comment_bubble_color]'
      ],
      callback: function (to) {
        return '1' == to;
      }
    }
  }, function (settingId, o) {
    api(settingId, function (setting) {
      $.each(o.controls, function (i, controlId) {
        api.control(controlId, function (control) {
          var visibility = function (to) {
            control.container.toggle(o.callback(to));
          };
          visibility(setting.get());
          setting.bind(visibility);
        });
      });
    });
  });

  //DOM READY SPECIFIC CONTROLS ACTIONS
  $( function($) {
    /* CHECK */
    //init icheck only if not already initiated
    //exclude widget inputs
    $('input[type=checkbox]').not('input[id*="widget"]').each( function() {
      if ( 0 === $(this).closest('div[class^="icheckbox"]').length ) {
        $(this).iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        })
        .on( 'ifChanged', function(e){
            $(e.currentTarget).trigger('change');
        });
      }
    });

    /* SELECT */
    //Exclude skin
    $('select[data-customize-setting-link]').not('select[data-customize-setting-link="tc_theme_options[tc_skin]"]')
      .not('select[data-customize-setting-link="tc_theme_options[tc_fonts]"]')
      .each( function() {
        $(this).selecter({
        //triggers a change event on the view, passing the newly selected value + index as parameters.
        // callback : function(value, index) {
        //   self.triggerSettingChange( window.event || {} , value, index); // first param is a null event.
        // }
        });
    });


    //SKINS
    //http://ivaynberg.github.io/select2/#documentation
    $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]').select2({
        minimumResultsForSearch: -1, //no search box needed
        formatResult: paintSkinOptionElement,
        formatSelection: paintSkinOptionElement,
        escapeMarkup: function(m) { return m; }
    }).on("select2-highlight", function(e) {
      //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
      $(this).select2("val" , e.val, true );
    });
    //Skins handled with select2
    function paintSkinOptionElement(state) {
        if (!state.id) return state.text; // optgroup
        return '<span class="tc-select2-skin-color" style="background:' + $(state.element).data('hex') + '">' + $(state.element).data('hex') + '<span>';
    }

    //FONTS
    $('select[data-customize-setting-link="tc_theme_options[tc_fonts]"]').select2({
        minimumResultsForSearch: -1, //no search box needed
        formatResult: paintFontOptionElement,
        formatSelection: paintFontOptionElement,
        escapeMarkup: function(m) { return m; }
    }).on("select2-highlight", function(e) {
      //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
      $(this).select2("val" , e.val, true );
    });
    function paintFontOptionElement(state) {
        if ( ! state.id && ( -1 != state.text.indexOf('Google') ) )
          return '<img class="tc-google-logo" src="http://www.google.com/images/logos/google_logo_41.png" height="20"/> Font pairs'; // google font optgroup
        else if ( ! state.id )
          return state.text;// optgroup different than google font
        return '<span class="tc-select2-font">' + state.text + '<span>';
    }
    //Fixes the non closing bug for the select2 dropdown
    $('#customize-controls').on('click' , function() { $('select[data-customize-setting-link]').select2("close"); } );

    /* NUMBER */
    $('input[type="number"]').stepper();
  });

})( wp, jQuery );