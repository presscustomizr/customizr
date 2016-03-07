<section class="<?php call_user_func( $post_list_element_model -> class_cb ) ?>">
  <?php do_action( "before_{$post_list_element_model -> type}" ); ?>
  <?php call_user_func( $post_list_element_model -> content_cb ) ?>
  <?php do_action( "after_{$post_list_element_model -> type}" ); ?>
</section>
