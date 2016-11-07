<?php
/**
 * The template for displaying the single featured page
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */

if ( czr_fn_get( 'is_first_of_row' ) ) : ?>
<div class="row widget-area" >
<?php endif ?>
    <div class="widget-front" <?php czr_fn_echo('element_attributes') ?>>
      <div class="span<?php czr_fn_echo( 'span_value' ) ?> fp-<?php czr_fn_echo( 'fp_id' ) ?>">
      <?php if ( czr_fn_get( 'fp_img' ) ) : /* FP IMAGE */?>
        <div class="thumb-wrapper <?php czr_fn_echo( 'thumb_wrapper_class' ) ?>">
          <a class="round-div" href="<?php czr_fn_echo( 'featured_page_link' ) ?>" title="<?php czr_fn_echo( 'featured_page_title' ) ?>"></a>
          <?php czr_fn_echo( 'fp_img' ) ?>
        </div>
      <?php endif /* END FP IMAGE*/ ?>
      <?php /* FP TITLE */ ?>
        <h2><?php czr_fn_echo( 'featured_page_title' ) ?>
            <?php if ( czr_fn_get( 'edit_enabled' ) ): ?>
              <span class="edit-link btn btn-inverse btn-mini">
                <a class="post-edit-link" href="<?php echo get_edit_post_link( czr_fn_get( 'featured_page_id' ) ) ?>" title="<?php czr_fn_echo( 'featured_page_title' ) ?>" target="_blank"><?php _e( 'Edit' , 'customizr' ) ?></a>
              </span>
            <?php endif ?>
        </h2>
      <?php /* END FP TITLE */ ?>
      <?php /* FP TEXT */ ?>
        <p class="fp-text-<?php czr_fn_echo( 'fp_id' ) ?>"><?php czr_fn_echo( 'text' ) ?></p>
      <?php /* END FP TEXT*/ ?>
      <?php if ( czr_fn_get( 'fp_button_text' ) ): /* FP BUTTON TEXT */ ?>
        <a class="<?php czr_fn_echo( 'fp_button_class' ) ?>" href="<?php czr_fn_echo( 'featured_page_link' ) ?>" title="<?php czr_fn_echo( 'featured_page_title' ) ?>" ><?php czr_fn_echo( 'fp_button_text' ) ?></a>
      <?php endif;/* END FP BUTTON TEXT*/ ?>
      </div><!--/.fp-->
    </div><!--/.widget-front-->
<?php if ( czr_fn_get( 'is_last_of_row' ) ) : ?>
  </div>
<?php endif;

