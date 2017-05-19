<?php
/**
 * The template for displaying the header of a single post
 * In loop
 *
 * @package Customizr
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-header-inner">
    <?php // This hook is used to render the following elements(ordered by priorities) :
    // singular thumbnail
    do_action( '__before_regular_heading_title' );
    ?>
    <?php if ( czr_fn_has('post_metas') && $cat = czr_fn_get( 'cat_list', 'post_metas' ) ) : ?>
        <div class="tax__container post-info entry-meta">
          <?php echo $cat ?>
        </div>
    <?php endif;

    if ( get_the_title() ) :

    ?>
    <h1 class="entry-title"><?php the_title() ?></h1>
    <?php

    endif;

    if ( czr_fn_has('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
        czr_fn_edit_button( array( 'link'  => $edit_post_link ) );

    // This hook is used to render the following elements(ordered by priorities) :
    // singular thumbnail
    do_action( '__after_regular_heading_title' );
    ?>
    <div class="header-bottom">
      <div class="post-info">
        <?php

          $comment_info  = czr_fn_comment_info( array( 'echo' => false ) );

          if ( $has_meta = czr_fn_has('post_metas') ) :

        ?>
          <span class="entry-meta">
        <?php
            if ( $author = czr_fn_get( 'author', 'post_metas' ) )
              echo $author;

            if ( $date = czr_fn_get( 'publication_date', 'post_metas') )
              if ( $author ) : ?><span class="v-separator">|</span><?php endif; echo $date;

            if ( $up_date = czr_fn_get( 'update_date', 'post_metas') )  {
              if ( $date ) : ?><span class="v-separator">-</span><?php
              elseif( $author ) : ?><span class="v-separator">|</span><?php
              endif;

              echo $up_date;


            }

            if ( $comment_info )
              echo '<span class="v-separator">|</span>';
        ?>
          </span>
        <?php endif ?>
        <?php echo $comment_info; ?>
      </div>
    </div>
  </div>
</header>