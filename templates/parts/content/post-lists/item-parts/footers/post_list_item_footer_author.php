<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer post-info" <?php czr_fn_echo('element_attributes') ?>><?php
  if ( czr_fn_is_registered_or_possible('post_metas') ) :

    $author  = czr_fn_get_property( 'author', 'post_metas' );
    $date    = czr_fn_get_property( 'publication_date', 'post_metas', array( 'permalink' => true ) );
    $up_date = czr_fn_get_property( 'update_date', 'post_metas', array( 'permalink' => !$date ) );

    if ( $author || $date || $up_date ) :
  ?>
    <div class="entry-meta row flex-row align-items-center">
      <?php if ( $author ) : ?>
        <div class="col-12 col-md-auto">
          <?php czr_fn_render_template( 'content/post-lists/item-parts/authors/author_info_small' ) ?>
        </div>
      <?php endif;


      ?>
      <?php if ( $date || $up_date ) : ?>
        <div class="col-12 col-md-auto">
          <div class="row">
          <?php
            if ( $date )
              echo '<div class="col col-auto">' . $date . '</div>';

            if ( $up_date )
              echo '<div class="col col-auto">' . $up_date . '</div>';

          ?>
          </div>
        </div>
      <?php endif; /* $date || $up_date */ ?>
    </div>
    <?php
    endif;//( $author || $date || $up_date )
  endif; //post_metas are possible
?></footer>
