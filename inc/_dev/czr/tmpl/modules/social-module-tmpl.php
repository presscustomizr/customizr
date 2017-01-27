<?php
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_social_pre_add_view_template' , 1 );
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_social_item_content_template' , 1 );

function czr_fn_print_social_pre_add_view_template() {
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
  <?php
}





function czr_fn_print_social_item_content_template() {
  $css_attr = CZR_customize::$instance -> css_attr;
    //the following template is a "sub view"
    //it's rendered :
    //1) on customizer start, depending on what is fetched from the db
    //2) dynamically when designing from the customizer
    //data looks like : { id : 'sidebar-one', title : 'A Title One' }
  ?>

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

    <div class="<?php echo $css_attr['sub_set_wrapper']; ?> width-100" data-input-type="color">
      <div class="customize-control-title"><?php _e('Icon color', 'customizr'); ?></div>
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