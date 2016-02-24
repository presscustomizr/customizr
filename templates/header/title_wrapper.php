<div class="<?php echo $title_wrapper_model -> class; ?>">
  <<?php echo $title_wrapper_model -> tag; ?>>
    <a class="<?php echo $title_wrapper_model -> link_class;?>" href="<?php echo $title_wrapper_model -> link_url; ?>" title="<?php echo $title_wrapper_model -> link_title; ?>" >
      <?php do_action('__title_wrapper__'); ?>
    </a>
  </<?php echo $title_wrapper_model -> tag; ?>>
</div>
