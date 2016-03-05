<article id="post-<?php the_ID(); ?>" <?php post_class( $post_list_wrapper_model -> element_class ); ?>>
  <?php do_action( "__post_list_{$post_list_wrapper_model -> place_1}__" ) ?>
  <?php do_action( "__post_list_{$post_list_wrapper_model -> place_2}__" ) ?>
</article>
<hr class="featurette-divider">
