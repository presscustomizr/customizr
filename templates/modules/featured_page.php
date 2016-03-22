<?php if ( $featured_page_model -> is_first_of_row ) : ?>
  <div class="row widget-area">
<?php endif ?>
    <div class="widget-front">
      <div class="span<?php echo $featured_page_model -> span_value ?> fp-<?php echo $featured_page_model -> fp_id ?>">
      <?php if ( $featured_page_model -> fp_img ) : /* FP IMAGE */?>
        <div class="thumb-wrapper <?php echo $featured_page_model -> thumb_wrapper_class ?>">
          <a class="round-div" href="<?php echo $featured_page_model -> featured_page_link ?>" title="<?php echo $featured_page_model -> featured_page_title ?>"></a>
          <?php echo $featured_page_model -> fp_img ?>
        </div>
      <?php endif /* END FP IMAGE*/ ?>
      <?php /* FP TITLE */ ?>
        <h2><?php echo $featured_page_model -> featured_page_title ?>
            <?php if ( $featured_page_model -> edit_enabled ): /* WE REALLY SHOULD USE A FUNCTION (or a model/template, so firing it with do_action('__edit_button') ) WHICH PRINTS A BUTTON GIVEN SOME PARAMS */?>
              <span class="edit-link btn btn-inverse btn-mini">
                <a class="post-edit-link" href="<?php echo get_edit_post_link( $featured_page_model -> featured_page_id ) ?>" title="<?php echo $featured_page_model -> featured_page_title ?>" target="_blank"><?php _e( 'Edit' , 'customizr' ) ?></a>
              </span>
            <?php endif ?>
        </h2>    
      <?php /* END FP TITLE */ ?>
      <?php /* FP TEXT */ ?>
        <p class="fp-text-<?php echo $featured_page_model -> fp_id ?>"><?php echo $featured_page_model -> text ?></p>
      <?php /* END FP TEXT*/ ?>
      <?php if ( $featured_page_model -> fp_button_text ): /* FP BUTTON TEXT */ ?>
        <a class="<?php echo $featured_page_model -> fp_button_class ?>" href="<?php echo $featured_page_model -> featured_page_link ?>" title="<?php echo $featured_page_model -> featured_page_title ?>" ><?php echo $featured_page_model -> fp_button_text ?></a>
      <?php endif;/* END FP BUTTON TEXT*/ ?>
      </div><!--/.fp-->
    </div><!--/.widget-front-->
<?php if ( $featured_page_model -> is_last_of_row ) : ?>
  </div>
<?php endif;

