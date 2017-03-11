<?php
/**
* The template for displaying the post titles
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
    <div class="header-content-inner">
    <?php if ( czr_fn_has('post_metas') && $cat = czr_fn_get( 'cat_list', 'post_metas', array( 'limit' => '10' ) ) ) : ?>
        <div class="entry-meta">
          <div class="tax__container">
            <?php echo $cat ?>
          </div>
        </div>
    <?php endif; ?>
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

        $pub_date     = czr_fn_has('post_metas') ? czr_fn_get( 'publication_date', 'post_metas' ) : false;
        $comment_info = czr_fn_has( 'comment_info' );
        $author       = czr_fn_has('post_metas') ? czr_fn_get( 'author', 'post_metas' ) : false;

        if ( $pub_date || $comment_info || $author ) :
    ?>
      <div class="header-content-bottom">
        <div class="post-info">
          <?php
            if ( $pub_date ) :
          ?>
            <div class="date-info">
              <?php echo $pub_date; ?>
            </div>
          <?php
            endif;

            if ( $comment_info ) :
              if ( $pub_date ):
            ?>
                <span class="v-separator">|</span>
            <?php
              endif
            ?>
              <div class="comment-info">
                <?php czr_fn_render_template( 'modules/comment_info' ) ?>
              </div>
          <?php
            endif;

            if ( $author ) :
              if ( $pub_date || $comment_info ):
              ?>
                <span class="v-separator">|</span>
              <?php
                endif
              ?>
              <div class="author-info">
                <?php echo $author ?>
              </div>
          <?php endif ?>
        </div>
      </div>
    <?php endif ?>
    </div>
  </div>
</header>