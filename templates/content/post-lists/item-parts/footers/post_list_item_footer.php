<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
    <div class="post-tags entry-meta">
      <ul class="tags">
        <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
      </ul>
    </div>
  <?php endif; ?>
    <div class="post-info clearfix entry-meta">

      <div class="row flex-row">
        <div class="col col-auto">

        <?php
          if ( $author = czr_fn_get( 'author', 'post_metas' ) )
            echo $author;
        ?>
        </div>
        <div class="col col-auto">
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
    </div>
</footer>