<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer" <?php czr_fn_echo('element_attributes') ?>><?php
  if ( czr_fn_is_registered_or_possible('post_metas') ) :

    $tags    = czr_fn_get_property( 'tag_list', 'post_metas' );
    $author  = czr_fn_get_property( 'author', 'post_metas' );
    $date    = czr_fn_get_property( 'publication_date', 'post_metas', array( 'permalink' => true ) );
    $up_date = czr_fn_get_property( 'update_date', 'post_metas', array( 'permalink' => !$date ) );

    if ( !empty($tags) || !empty($date) || !empty($up_date) || !empty($author) ) :
      if ( !empty($tags) ) :
  ?>
      <div class="post-tags entry-meta">
        <ul class="tags">
          <?php echo $tags; ?>
        </ul>
      </div>
    <?php endif; //tags
      if ( !empty($date) || !empty($up_date) || !empty($author) ): ?>
        <div class="post-info clearfix entry-meta">

          <div class="row flex-row">
            <?php
            if ( !empty($author) ) {
              echo '<div class="col col-auto">' . $author . '</div>';
            }

            if ( !empty($date) || !empty($up_date) ) :
            ?>
              <div class="col col-auto">
                <div class="row">
                  <?php
                    if ( !empty($date) ) {
                      echo '<div class="col col-auto">' . $date . '</div>';
                    }

                    if ( !empty($up_date) ) {
                      echo '<div class="col col-auto">' . $up_date . '</div>';
                    }
                  ?>
                </div>
              </div>
            <?php endif; // $date || $up_date ?>
          </div>
        </div>
      <?php endif; // $author || $date || $up_date ?>
    <?php endif; // $tags || $date || $up_date || $author ?>
  <?php endif; //post_metas possibile ?>
</footer>