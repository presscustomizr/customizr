(function ($) {
  var api = wp.customize;

  $.each({
    'tc_theme_options[tc_show_featured_pages]': {
      controls: TCControlParams.FPControls,
      callback: function (to) {
        return '1' == to
      }
    },
    'tc_theme_options[tc_front_slider]': {
      controls: [
        'tc_theme_options[tc_slider_width]',
        'tc_theme_options[tc_slider_delay]'
      ],
      callback: function (to) {
        return '0' !== to
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


})(jQuery);