<?php
/**
 * The template for displaying the single featured page
 */

if ( czr_fn_get( 'is_first_of_row' ) ) : ?>
<div class="row widget-area" role="complementary" <?php czr_fn_echo('element_attributes') ?>>
<?php endif ?>
  <div class="featured col-xs-12 col-md-<?php czr_fn_echo( 'fp_col' ) ?> fp-<?php czr_fn_echo( 'fp_id' ) ?>">
    <div class="widget-front grid__item round" <?php czr_fn_echo('element_attributes') ?>>
    <?php if ( czr_fn_get( 'fp_img' ) ) : /* FP IMAGE */?>
      <div class="tc-thumbnail czr-thumb-wrapper <?php czr_fn_echo( 'thumb_wrapper_class' ) ?>">
        <a class="czr-link-mask" href="<?php czr_fn_echo( 'featured_page_link' ) ?>" title="<?php czr_fn_echo( 'featured_page_title' ) ?>">
          <?php czr_fn_echo( 'fp_img' ) ?>
        </a>
      </div>
    <?php endif /* END FP IMAGE*/ ?>
      <div class="tc-content">
        <?php /* FP TITLE */ ?>
          <h4 class="fp-title"><?php czr_fn_echo( 'featured_page_title' ) ?></h4>
        <?php /* END FP TITLE */ ?>
        <?php
        /* FP EDIT BUTTON */
        if ( czr_fn_has( 'edit_button' ) && czr_fn_get( 'edit_enabled' ) )

          czr_fn_render_template(
            'modules/edit_button',
            array(
              'model_args' => array(
                'edit_button_link'  => get_edit_post_link( czr_fn_get( 'featured_page_id' ) )
              )
            )
          );
        /* END FP EDIT BUTTON */

        ?>
        <?php /* FP TEXT */ ?>
          <p class="fp-text-<?php czr_fn_echo( 'fp_id' ) ?>"><?php czr_fn_echo( 'text' ) ?></p>
        <?php /* END FP TEXT*/ ?>
        <?php if ( czr_fn_get( 'fp_button_text' ) ): /* FP BUTTON TEXT */ ?>
          <span class="fp-button">
            <a class="<?php czr_fn_echo( 'fp_button_class' ) ?>" href="<?php czr_fn_echo( 'featured_page_link' ) ?>" title="<?php czr_fn_echo( 'featured_page_title' ) ?>" ><?php czr_fn_echo( 'fp_button_text' ) ?></a>
          </span>
        <?php endif;/* END FP BUTTON TEXT*/ ?>
      </div>
    </div><!--/.widget-front-->
  </div><!--/.fp-->
<?php if ( czr_fn_get( 'is_last_of_row' ) ) : ?>
</div>
<?php endif;