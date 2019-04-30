<?php
/**
 * The template for displaying the single featured page
 */

if ( czr_fn_get_property( 'is_first_of_row' ) ) : ?>
<div class="row fp-widget-area" role="complementary" <?php czr_fn_echo('element_attributes') ?>>
<?php endif ?>
  <div class="featured-page col-12 col-md-<?php czr_fn_echo( 'fp_col' ) ?> fp-<?php czr_fn_echo( 'fp_id' ) ?>">
    <div class="widget-front czr-link-mask-p round" <?php czr_fn_echo('element_attributes') ?>>
      <?php if ( czr_fn_get_property( 'fp_img' ) ) : /* FP IMAGE */?>
      <div class="tc-thumbnail czr-thumb-wrapper czr__r-wTCT <?php czr_fn_echo( 'thumb_wrapper_class' ) ?>">
        <a class="czr-link-mask" href="<?php czr_fn_echo( 'featured_page_link' ) /* escaped in the model */?>"></a>
          <?php czr_fn_echo( 'fp_img' ) ?>
      </div>
      <?php endif /* END FP IMAGE*/ ?>
      <?php /* FP TITLE */ ?>
        <h4 class="fp-title"><?php echo strip_tags( czr_fn_get_property( 'featured_page_title' ) ) ?></h4>
      <?php /* END FP TITLE */ ?>
      <?php
      /* FP EDIT BUTTON */
      if ( czr_fn_get_property( 'edit_enabled' ) ) {
        czr_fn_edit_button( array( 'link' => get_edit_post_link( czr_fn_get_property( 'featured_page_id' ) ) ) );
      }
      /* END FP EDIT BUTTON */

      ?>
      <?php /* FP TEXT */ ?>
        <p class="fp-text-<?php czr_fn_echo( 'fp_id' ) ?>"><?php czr_fn_echo( 'text' ) ?></p>
      <?php /* END FP TEXT*/ ?>
      <?php if ( czr_fn_get_property( 'fp_button_text' ) ) {/* FP BUTTON TEXT */
        czr_fn_readmore_button( array(
            'class' => 'fp-button'. czr_fn_get_property( 'fp-button-class' ),
            'link' => czr_fn_get_property( 'featured_page_link' ),
            'esc_url' => false, //already escaped in the model
            'text' => czr_fn_get_property( 'fp_button_text' ),
            'echo' => true
        ) );
      }/* END FP BUTTON TEXT*/ ?>
    </div><!--/.widget-front-->
  </div><!--/.fp-->
<?php if ( czr_fn_get_property( 'is_last_of_row' ) ) : ?>
</div>
<?php endif;