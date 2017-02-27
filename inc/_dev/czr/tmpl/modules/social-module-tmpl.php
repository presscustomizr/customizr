<?php
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_social_tmpls' , 1 );

function czr_fn_print_social_tmpls() {
  $css_attr = CZR_customize::$instance -> css_attr;
  ?>

  <script type="text/html" id="tmpl-czr-module-social-pre-add-view-content">
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="select">
      <div class="customize-control-title"><?php _e('Select an icon', 'customizr'); ?></div>
      <div class="czr-input">
        <select data-type="social-icon"></select>
      </div>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="text">
      <div class="customize-control-title"><?php _e('Social link url', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-link" type="text" value="" placeholder="<?php _e('http://...,mailto:...,...', 'customizr'); ?>"></input>
      </div>
      <span class="czr-notice"><?php _e('Enter the full url of your social profile (must be valid url).', 'customizr'); ?>
      </span>
    </div>
  </script>

  <script type="text/html" id="tmpl-czr-module-social-mod-opt">
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="number" data-transport="postMessage">
      <div class="customize-control-title"><?php _e('Size in px', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-size" type="number" step="1" min="5" value="{{ data['social-size'] }}" />
      </div>
    </div>
  </script>

  <script type="text/html" id="tmpl-czr-module-social-item-content">
    <!-- <div class="czr-sub-set">
      <div class="customize-control-title"><?php _e('Id', 'customizr'); ?></div>
      <div class="czr-input">
        <span data-type="id">{{ data.id }}</span>
      </div>
    </div> -->
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="select">
      <div class="customize-control-title"><?php _e('Social icon', 'customizr'); ?></div>
      <div class="czr-input">
        <select data-type="social-icon"></select>
        <!-- <input type="text" value="{{ data['social-icon'] }}"></input> -->
      </div>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="text">
      <div class="customize-control-title"><?php _e('Social link', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-link" type="text" value="{{ data['social-link'] }}" placeholder="<?php _e('http://...,mailto:...,...', 'customizr'); ?>"></input>
      </div>
      <span class="czr-notice"><?php _e('Enter the full url of your social profile (must be valid url).', 'customizr'); ?></span>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="text">
      <div class="customize-control-title"><?php _e('Title', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="title" type="text" value="{{ data.title }}" placeholder="<?php _e('Enter a title', 'customizr'); ?>"></input>
      </div>
      <span class="czr-notice"><?php _e('This is the text displayed on mouse over.', 'customizr'); ?></span>
    </div>

    <div class="<?php echo $css_attr['sub_set_wrapper']; ?> width-100" data-input-type="color" data-transport="postMessage">
      <div class="customize-control-title width-100"><?php _e('Icon color', 'customizr'); ?> <i><?php _e('default:', 'hueman'); ?> rgba(255,255,255,0.7)</i></div>
      <div class="czr-input">
        <input data-type="social-color" type="text" value="{{ data['social-color'] }}"></input>
      </div>
      <span class="czr-notice"><?php _e('Set a unique color for your icon.', 'customizr'); ?></span>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="check">
      <# //the previous hueman option system was storing this option in an array
        data['social-target'] = _.isArray( data['social-target'] ) ? data['social-target'][0] : data['social-target'];
        var _checked = ( false != data['social-target'] ) ? "checked=checked" : '';
      #>
      <div class="customize-control-title"><?php _e('Link target', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-target" type="checkbox" {{ _checked }}></input>
      </div>
      <span class="czr-notice"><?php _e('Check this option to open the link in a another tab of the browser.', 'customizr'); ?></span>
    </div>

  </script>
  <?php
}
?>