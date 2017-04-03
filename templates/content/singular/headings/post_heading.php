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
//TEST
/*
    $_the_thumb = czr_fn_get_thumbnail_model( 'full' );
    if ( ! empty ( $_the_thumb['tc_thumb']) ) :
?>
  <div class="entry-media__holder <?php echo esc_attr( czr_fn_get_opt( 'tc_center_img' ) ) ? 'js-centering' : 'css-centering' ?>">
      <?php echo $_the_thumb[ 'tc_thumb' ] ?>
  </div>
<?php
    endif*/
?>
<?php
  czr_fn_render_template( 'content/common/media',
              array(
                //'reset_to_defaults' => false,
                'model_args' => array(
                  'media_type'               => 'wp_thumb'
                )
              )
  );
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
        if ( (bool) $edit_post_link = get_edit_post_link() ) {
          czr_fn_edit_button( array( 'class' => 'inverse', 'link'  => $edit_post_link ) );
        }

        $pub_date     = czr_fn_has('post_metas') ? czr_fn_get( 'publication_date', 'post_metas' ) : false;
        $author       = czr_fn_has('post_metas') ? czr_fn_get( 'author', 'post_metas' ) : false;


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


            czr_fn_comment_info( $before = '<div class="comment-info">', $after = '</div>' );

            if ( $author ) : ?>

              <div class="author-info">
                <?php echo $author ?>
              </div>
          <?php endif ?>
        </div>

      </div>
    </div>
  </div>
</header>