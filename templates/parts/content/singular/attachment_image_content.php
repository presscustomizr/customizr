<?php
/**
 * The template for displaying the single post content
 *
 * In WP loop
 */
global $post;
?>
<article <?php echo czr_fn_get_the_singular_article_selectors() ?> <?php czr_fn_echo( 'element_attributes' ) ?>>
  <?php do_action( '__before_content' ) ?>
  <?php
  /* heading */
  czr_fn_render_template( 'content/singular/headings/regular_attachment_image_heading' );
  ?>
  <nav id="image-navigation" class="attachment-image-navigation display-flex flex-row justify-content-between" role="navigation"">
    <?php
      $prev_dir          = is_rtl() ? 'r' : 'l';
      $next_dir          = is_rtl() ? 'l' : 'r';
    
      $prev_dir          = is_rtl() ? 'right' : 'left';
      $next_dir          = is_rtl() ? 'left' : 'right';

      $tprev_align_class = "text-{$prev_dir}";
      $tnext_align_class = "text-{$next_dir}";

      /* Generate links */
      previous_image_link( 
        $size = false,
        '<span class="meta-nav"><i class="arrow icn-' . $prev_dir . '-open-big"></i><span class="meta-nav-title">' . __( 'Previous', 'customizr' ) . '</span></span>' //title
      );

      next_image_link( 
        $size = false,
        '<span class="meta-nav"><span class="meta-nav-title">' . __( 'Next', 'customizr' ) . '</span><i class="arrow icn-' . $next_dir . '-open-big"></i></span>' //title
      );

        #  <span class="previous-image display-flex"><?php previous_image_link( false, __( '<span>&larr;</span><span>Previous</span>' , 'customizr' ) ); </span>
   # <span class="next-image display-flex"><?php next_image_link( false, __( 'Next &rarr;' , 'customizr' ) ); </span>


    ?>

  </nav><!-- //#image-navigation -->
  <div class="post-entry tc-content-inner">
    <section class="entry-attachment attachment-content <?php czr_fn_echo( 'element_class' ) ?>" >
      <figure class="attachment-image-figure" style="text-align: left;">
        <div class="entry-media__holder" style="display:inline-block;">
          <a href="<?php czr_fn_echo( 'link_url' ) ?>" class="<?php czr_fn_echo( 'attachment_class' ) ?> bg-link" title="<?php the_title_attribute(); ?>" <?php czr_fn_echo( 'link_attributes' ) ?>></a>
          <?php echo wp_get_attachment_image( get_the_ID(), czr_fn_get_property( 'attachment_size' ) ) ?>            
        </div>
        <?php if ( czr_fn_get_property( 'has_caption' )  ) :?>
          <figcaption class="wp-caption-text entry-caption">
            <?php the_excerpt(); ?>
          </figcaption>
        <?php endif; ?> 
      </figure>      
      <?php /* hidden ligthbox gallery with all the attachments referring to the same post parent */
      czr_fn_echo( 'gallery' )
      ?>  
      <div class="entry-content">
        <?php the_content() ?>
      </div> 
      <footer class="post-footer clearfix">
        <?php
          if ( czr_fn_is_registered_or_possible('social_share') ) :
        ?>
          <div class="post-share col-xs-12 col-sm-auto col-sm">
              <!-- fake need to have social links somewhere -->
              <?php czr_fn_render_template( 'modules/common/social_block', array( 'model_id' => 'social_share' ) ) ?>
          </div>
        <?php
          endif
        ?>
      </footer>
    </section><!-- .entry-content -->
  </div><!-- .post-entry -->
  <?php do_action( '__after_content' ) ?>
</article>