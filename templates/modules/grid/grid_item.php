<?php
/**
 * The template for displaying the post list grid item (expanded or not)
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<section class="tc-grid-post" <?php czr_fn_echo('element_attributes') ?>>
  <figure class="tc-grid-figure <?php czr_fn_echo( 'figure_class' ) ?>">
    <?php

    if ( czr_fn_get( 'icon_enabled' ) ):

    ?>
      <div class="tc-grid-icon format-icon" <?php czr_fn_echo( 'icon_attributes' ) ?>></div>
    <?php

    endif

    ?>
    <?php czr_fn_echo( 'thumb_img' ) ?>
    <?php if ( czr_fn_has( 'comment_bubble' ) ) czr_fn_render_template( 'modules/comment_bubble', 'comment_bubble' ) ?>
    <figcaption class="tc-grid-excerpt">
      <div class="entry-summary">
        <div class="tc-g-cont"><?php the_excerpt() ?></div>
        <?php

        /* The expanded grid item has the title inside the caption */
        if( czr_fn_get( 'has_title_in_caption' ) ):

        ?>

        <h2 class="entry-title">
          <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php echo esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php czr_fn_echo( 'title' ) ?></a>
          <?php if ( czr_fn_get( 'has_recently_updated' ) && czr_fn_has( 'recently_updated' ) ) czr_fn_render_template( 'modules/recently_updated', 'recently_updated' ) ?>
        </h2>

        <?php

        /* end expanded title */
        endif

        ?>
      </div>
      <a class="tc-grid-bg-link" href="<?php the_permalink() ?>" title="<?php esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
      <?php

      /* additional effect for not expanded grid items with no img */
      if( czr_fn_get( 'has_fade_expt' ) /* ! ( czr_fn_get( 'is_expanded' ) || czr_fn_get( 'thumb_img' ) ) */ ):

      ?>
      <span class="tc-grid-fade_expt"></span>
      <?php

      endif

      ?>
    </figcaption>
    <?php

    /* Edit link in the figure for the expanded item */
      if( czr_fn_get( 'has_edit_in_caption' ) )
        if ( czr_fn_has( 'edit_button' ) ) czr_fn_render_template( 'modules/edit_button', 'edit_button' );
    ?>
  </figure>
<?php

  /* Header in the bottom for not expanded */
  if( ! czr_fn_get( 'is_expanded' ) ) :

?>
  <header class="entry-header">
  <?php

   if ( ! czr_fn_get( 'has_title_in_caption' ) && tc_post_has_title() ) :

  ?>
    <h2 class="entry-title">
      <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php echo esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php czr_fn_echo( 'title' ) ?></a>
  <?php if ( ! czr_fn_get( 'has_edit_in_caption' ) && czr_fn_has( 'edit_button' ) ) czr_fn_render_template( 'modules/edit_button', 'edit_button' ) ?>
  <?php if ( czr_fn_has( 'recently_updated' ) ) czr_fn_render_template( 'modules/recently_updated', 'recently_updated' ) ?>
    </h2>
  <?php

   endif;

   /* Post metas */
   if ( czr_fn_has('post_metas_button') ) { czr_fn_render_template( 'content/post-metas/post_metas', 'post_metas_button'); }
   elseif ( czr_fn_has('post_metas_text') ) { czr_fn_render_template('content/post-metas/post_metas_text', 'post_metas_text'); }
   elseif ( czr_fn_has('post_metas_attachment') ) { czr_fn_render_template('content/post-metas/attachment_post_metas', 'post_metas_attachment'); }

  ?>
  </header>
<?php

  endif

?>
</section>
