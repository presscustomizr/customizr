<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer post-info" <?php czr_fn_echo('element_attributes') ?>>
    <div class="entry-meta row flex-row align-items-center">
      <div class="col-12 col-md-auto">
      <?php
        if ( $author = czr_fn_get( 'author', 'post_metas' ) )
          czr_fn_render_template( 'content/post-lists/item-parts/authors/author_info_small' )
      ?>
      </div>
      <div class="col-12 col-md-auto">
        <div class="row">
        <?php
          if ( $date = czr_fn_get( 'publication_date', 'post_metas', array( 'permalink' => true ) ) )
            echo '<div class="col col-auto">' . $date . '</div>';

          if ( $up_date = czr_fn_get( 'update_date', 'post_metas', array( 'permalink' => true ) ) )
            echo '<div class="col col-auto">' . $up_date . '</div>';

        ?>
        </div>
      </div>
    </div>
</footer>