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
    <div class="header-content-inner">
      <h1 class="entry-title"><?php the_title() ?></h1>
    <?php
      if ( (bool) $edit_post_link = get_edit_post_link() ) {
          czr_fn_edit_button( array( 'class' => 'inverse', 'link'  => $edit_post_link ) );
      }
      czr_fn_comment_info( $before = '<div class="header-content-bottom"><div class="post-info"><div class="comment-info">', $after = '</div></div></div>' );
    ?>
  </div>

  </div>
</header>