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
    <?php
    do_action( '__before_regular_heading_title' );
    ?>
    <?php

    if ( get_the_title() ) :

    ?>
    <h1 class="entry-title"><?php the_title() ?></h1>
    <?php

    endif;

    if ( czr_fn_is_registered_or_possible('edit_button') && (bool) $edit_post_link = get_edit_post_link() ) {
        czr_fn_edit_button( array( 'link'  => $edit_post_link ) );
    }

    // This hook is used to render the following elements(ordered by priorities) :
    // singular thumbnail
    do_action( '__after_regular_heading_title' );
    ?>
    <div class="header-bottom">
      <div class="post-info">
        <?php

          if ( $has_meta = czr_fn_is_registered_or_possible('post_metas') ) :
          $author = czr_fn_get_property( 'author', 'post_metas' );
        ?>
          <span class="entry-meta">
        <?php
            if ( !empty($author) ) {
              echo $author;
            }
            $date = czr_fn_get_property( 'publication_date', 'post_metas', array( false, null, true ) );

            // czr_fn_get_property( 'publication_date', 'post_metas', array( false, null, true )
            // means:
            // $permalink = false => we don't want the date linking to this very post
            // $before    = null  => we don't want to print anything special before, the default "Published&nbsp;" will be used
            // $only_text = true  => we don't want a link at all. This is because generally that meta links to an archive, and by default attachments are not displayed in archives.
            // So that clicking on that meta we would end up on an 404.
            if ( !empty($date) ) {
              if ( !empty($author) ) : ?><span class="v-separator">|</span><?php endif; echo $date;
            }
            $up_date = czr_fn_get_property( 'update_date', 'post_metas', array( false, null, true ) );
            if ( !empty($up_date) )  {
              if ( !empty($date) ) : ?><span class="v-separator">-</span><?php
              elseif( !empty($author) ) : ?><span class="v-separator">|</span><?php
              endif;

              echo $up_date;

            }
            $attachment_image_info = czr_fn_get_property( 'attachment_image_info', 'post_metas' );
            if ( !empty($attachment_image_info) ) :
              if ( !empty($date) || !empty($up_date) || !empty($author) ) :
                ?><span class="v-separator">-</span><?php ;
              endif;
              echo $attachment_image_info;
            endif;

        ?>
          </span>
        <?php endif ?>
      </div>
    </div>
  </div>
</header>