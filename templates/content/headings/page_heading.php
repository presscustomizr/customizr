<?php
/**
* The template for displaying the page titles
*/
/*
* TODO: what to show? featured image, header image video .. ????
*/
?>
<header class="row page__header image__header entry-header" <?php czr_fn_echo('element_attributes') ?>>
<?php
    $_the_thumb = czr_fn_get_thumbnail_model( 'full' );
    if ( ! empty ( $_the_thumb['tc_thumb']) ) :
?>
  <div class="entry-media__holder <?php echo esc_attr( czr_fn_get_opt( 'tc_center_img' ) ) ? 'js-media-centering' : 'no-js-media-centering' ?>">
      <?php echo $_the_thumb[ 'tc_thumb' ] ?>
  </div>
<?php
    endif
?>
  <div class="container header-content">
    <div class="header-content-inner col-xs-12">
      <h1 class="entry-title"><?php the_title() ?></h1>
      <?php
        if ( czr_fn_has('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
          czr_fn_render_template(
            'modules/edit_button',
            array( 'model_args' => array(
                'edit_button_class' => 'inverse',
                'edit_button_link'  => $edit_post_link
              )
            )
          );
      ?>
    </div>
    <?php if ( czr_fn_has( 'comment_info' ) ) : ?>
      <div class="header-content-bottom">
        <div class="post-info col-xs-12">
          <div class="comment-info">
            <?php czr_fn_render_template( 'modules/comment_info' ) ?>
          </div>
        </div>
      </div>
    <?php endif ?>
  </div>
</header>