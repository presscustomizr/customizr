//DOM READY :
//1) FIRE SPECIFIC INPUT PLUGINS
//2) ADD SOME COOL STUFFS
//3) SPECIFIC CONTROLS ACTIONS
(function (wp, $) {
  $( function($) {
    var api = wp.customize || api;
    /* GRID */
    var _build_setId = function ( name ) {
      return -1 == name.indexOf( 'tc_theme_options') ? [ 'tc_theme_options[' , name  , ']' ].join('') : name;
    };
    var _grid_design_controls = [
      'tc_grid_in_blog',
      'tc_grid_in_archive',
      'tc_grid_in_search',
      'tc_grid_thumb_height',
      'tc_grid_shadow',
      'tc_grid_bottom_border',
      'tc_grid_icons',
      'tc_grid_num_words'
    ];

    var _build_control_id = function( _control ) {
      return [ '#' , 'customize-control-tc_theme_options-', _control ].join('');
    };

    var _get_grid_design_controls = function() {
      return $( _grid_design_controls.map( function( _control ) {
        return _build_control_id( _control );
      }).join(',') );
    };

    //hide design controls on load
    $( _get_grid_design_controls() ).addClass('tc-grid-design').hide();

    $('.tc-grid-toggle-controls').click( function() {
      $( _get_grid_design_controls() ).slideToggle('fast');
      $(this).toggleClass('open');
    } );

    /* RECENTER CURRENT SECTIONS */
    $('.accordion-section').not('.control-panel').click( function () {
      _recenter_current_section($(this));
    });

    function _recenter_current_section( section ) {
      var $siblings               = section.siblings( '.open' );
      //check if clicked element is above or below sibling with offset.top
      if ( 0 !== $siblings.length &&  $siblings.offset().top < 0 ) {
        $('.wp-full-overlay-sidebar-content').animate({
              scrollTop:  - $('#customize-theme-controls').offset().top - $siblings.height() + section.offset().top + $('.wp-full-overlay-sidebar-content').offset().top
        }, 700);
      }
    }//end of fn

    /* ADD GOOGLE IN TITLE */
    $g_logo = $('<img>' , {class : 'tc-title-google-logo' , src : 'http://www.google.com/images/logos/google_logo_41.png' , height : 20 });
    $('#accordion-section-fonts_sec').prepend($g_logo);


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
    $('select[data-customize-setting-link]').not('.select2')
      .each( function() {
        $(this).selecter({
        //triggers a change event on the view, passing the newly selected value + index as parameters.
        // callback : function(value, index) {
        //   self.triggerSettingChange( window.event || {} , value, index); // first param is a null event.
        // }
        });
    });

    //Multipicker
    //http://ivaynberg.github.io/select2/#documentation
    $('select.tc_multiple_picker').select2({
      closeOnSelect: false,
      formatSelection: tcEscapeMarkup
    });
    function tcEscapeMarkup(obj) {
      //trim dashes
      return obj.text.replace(/\u2013|\u2014/g, "");
    }

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

  });//end of $( function($) ) dom ready

})( wp, jQuery);
