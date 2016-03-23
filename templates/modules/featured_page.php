<?php if ( tc_get( 'is_first_of_row' ) ) : ?>
  <div class="row widget-area">
<?php endif ?>
    <div class="widget-front">
      <div class="span<?php tc_echo( 'span_value' ) ?> fp-<?php tc_echo( 'fp_id' ) ?>">
      <?php if ( tc_get( 'fp_img' ) ) : /* FP IMAGE */?>
        <div class="thumb-wrapper <?php tc_echo( 'thumb_wrapper_class' ) ?>">
          <a class="round-div" href="<?php tc_echo( 'featured_page_link' ) ?>" title="<?php tc_echo( 'featured_page_title' ) ?>"></a>
          <?php tc_echo( 'fp_img' ) ?>
        </div>
      <?php endif /* END FP IMAGE*/ ?>
      <?php /* FP TITLE */ ?>
        <h2><?php tc_echo( 'featured_page_title' ) ?>
            <?php if ( tc_get( 'edit_enabled' ) ): /* WE REALLY SHOULD USE A FUNCTION (or a model/template, so firing it with do_action('__edit_button') ) WHICH PRINTS A BUTTON GIVEN SOME PARAMS */?>
              <span class="edit-link btn btn-inverse btn-mini">
                <a class="post-edit-link" href="<?php echo get_edit_post_link( tc_get( 'featured_page_id' ) ) ?>" title="<?php tc_echo( 'featured_page_title' ) ?>" target="_blank"><?php _e( 'Edit' , 'customizr' ) ?></a>
              </span>
            <?php endif ?>
        </h2>    
      <?php /* END FP TITLE */ ?>
      <?php /* FP TEXT */ ?>
        <p class="fp-text-<?php tc_echo( 'fp_id' ) ?>"><?php tc_echo( 'text' ) ?></p>
      <?php /* END FP TEXT*/ ?>
      <?php if ( tc_get( 'fp_button_text' ) ): /* FP BUTTON TEXT */ ?>
        <a class="<?php tc_echo( 'fp_button_class' ) ?>" href="<?php tc_echo( 'featured_page_link' ) ?>" title="<?php tc_echo( 'featured_page_title' ) ?>" ><?php tc_echo( 'fp_button_text' ) ?></a>
      <?php endif;/* END FP BUTTON TEXT*/ ?>
      </div><!--/.fp-->
    </div><!--/.widget-front-->
<?php if ( tc_get( 'is_last_of_row' ) ) : ?>
  </div>
<?php endif;

