<?php
/**
 * The template for displaying the inner content in a post list element
 *
 * In WP loop
 *
 */
?>
<div class="tc-content-inner <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?> >
  <?php
      if ( czr_fn_get_property( 'content_template' ) ) {
          //render the $content_template;
          czr_fn_render_template( czr_fn_get_property( 'content_template' ), czr_fn_get_property( 'content_args' ) );
      }
      else { ?>
          <div class="czr-wp-the-content">
              <?php czr_fn_echo( 'content' ); ?>
          </div>
      <?php }
      czr_fn_link_pages();
  ?>
</div>