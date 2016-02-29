<div class="<?php echo $widget_area_wrapper_model -> wrapper_class ?>">
  <div id="<?php echo $widget_area_wrapper_model -> inner_id ?>" class="<?php echo $widget_area_wrapper_model -> inner_class ?>" role="complementary">
  	<?php do_action("__widget_area{$widget_area_wrapper_model -> action_hook_suffix}__") ?>
  </div>
</div>  
