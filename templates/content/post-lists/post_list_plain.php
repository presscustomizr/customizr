<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<?php if ( czr_fn_get( 'is_loop_start' ) ) : ?>
<div class="post-list-plain <?php czr_fn_echo('element_class') ?>"  <?php czr_fn_echo('element_attributes') ?>>
<?php endif ?>
  <article <?php czr_fn_echo( 'article_selectors' ) ?> >
    <div class="sections-wrapper <?php czr_fn_echo( 'sections_wrapper_class' ) ?>">
    <?php
      if ( czr_fn_has('media') ) { czr_fn_render_template('content/post-lists/post_list_media', 'post_list_media'); }
    ?>
      <section class="tc-content entry-content__holder">
        <?php if ( czr_fn_has('post_list_header') ) czr_fn_render_template('content/post-lists/headings/post_list_header-no_metas', 'post_list_header') ?>
        <div class="entry-content__wrapper row <?php czr_fn_echo('inner_wrapper_class') ?>">
          <?php if ( czr_fn_has('post_metas') && $cat = czr_fn_get( 'cat_list', 'post_metas' ) ) : ?>

            <div class="entry-meta col-md-3 col-xs-12 small caps">
              <?php echo $cat ?>
            </div>

          <?php  endif; ?>
          <div class="tc-content-inner <?php czr_fn_echo('content_inner_col') ?> <?php czr_fn_echo( 'content_class' ) ?>">
            <?php
              czr_fn_echo( 'the_post_list_content', 'post_list_content', array(
                __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ),
                wp_link_pages( array(
                  'before'        => '<div class="post-pagination row"><div class="col-md-12">',
                  'after'         => '</div></div>',
                  'link_before'   => '<span>',
                  'link_after'    => '</span>',
                  'echo'          => false
                  )
                )
              ) );
            ?>
            <div class="entry-meta">
              <?php if ( czr_fn_has('post_metas') && $tag = czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
              <div class="post-tags">
                <ul class="tags">
                  <?php echo $tag ?>
                </ul>
              </div>
              <?php endif; ?>
              <div class="post-share">
                <!-- fake need to have social links somewhere -->
                <ul class="socials">
                  <li><a href="http://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                  <li><a href="http://linkedin.com/"><i class="fa fa-linkedin"></i></a></li>
                  <li><a href="http://twitter.com/"><i class="fa fa-twitter"></i></a></li>
                  <li><a href="http://plus.google.com/"><i class="fa fa-instagram"></i></a></li>
                  <li><a href="http://plus.google.com/"><i class="fa fa-pinterest"></i></a></li>
                  <li><a href="http://plus.google.com/"><i class="fa fa-google-plus"></i> </a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <?php if ( czr_fn_has('post_list_footer') ) czr_fn_render_template('content/post-lists/footers/post_list_footer_author', 'post_list_footer' ); ?>
      </section>
    </div>
  </article>
<?php if ( czr_fn_get( 'is_loop_end' ) ) : ?>
</div>
<?php endif ?>