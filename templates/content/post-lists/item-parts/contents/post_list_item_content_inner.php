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
      if ( czr_fn_get( 'content_template' ) ) {

          //render the $content_template;
          czr_fn_render_template( czr_fn_get( 'content_template' ), czr_fn_get( 'content_args' ) );

      }
      else
          czr_fn_echo( 'content' );

      czr_fn_link_pages();
  ?>
</div>