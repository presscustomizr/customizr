<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<?php if ( czr_fn_is_loop_start() ) : ?>
<div class="grid-container__plain <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="plain__wrapper row">
<?php endif ?>
    <article <?php czr_fn_echo( 'article_selectors' ) ?> >
      <div class="sections-wrapper grid__item">
        <?php
          if ( $has_post_media = czr_fn_get('has_post_media') )
            czr_fn_render_template(
              'content/post-lists/singles/post_list_single_media',
               array(
                'model_args' => array(
                  'element_class'            => '',
                  'has_post_media'           => $has_post_media,
                  'has_format_icon_media'    => false,
                  'is_full_image'            => false
                )
              )
            );
        ?>
        <section class="tc-content entry-content__holder">
          <?php
            czr_fn_render_template(
              'content/post-lists/singles/headings/post_list_single_header-no_metas',
              array(
                'model_class' => 'content/post-lists/singles/headings/post_list_single_header',
                'model_args'  => array(
                  'entry_header_inner_class' => czr_fn_get( 'entry_header_inner_class' ),
                  'element_class'            => array('row')
                )
              )
            );
          ?>
          <div class="entry-content__wrapper row">
            <?php if ( $cat_list = czr_fn_get( 'cat_list' ) ) : ?>
              <div class="entry-meta tax__container small caps <?php czr_fn_echo( 'cat_list_class' ) ?>">
                <?php echo $cat_list ?>
              </div>

            <?php endif; ?>
            <div class="tc-content-inner-wrapper <?php czr_fn_echo( 'content_inner_class' ) ?>" >
              <?php
              /* Content Inner */
              czr_fn_render_template(
                'content/post-lists/singles/contents/post_list_single_content_inner',
                array(
                  'model_args' => array(
                    'show_full_content' => czr_fn_get( 'show_full_content' ),
                  )
                )
              )
              ?>
              <div class="clearfix entry-meta ">
                <?php  if ( czr_fn_has('post_metas') && (bool) $tag_list = czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
                    <div class="post-tags float-md-left">
                      <ul class="tags">
                        <?php echo $tag_list ?>
                      </ul>
                    </div>
                  <?php endif ?>

                  <!-- fake need to have social links somewhere -->
                <?php if ( czr_fn_has('social_share') ) : ?>

                  <div class="post-share float-md-right">
                    <?php czr_fn_render_template( 'modules/social_block', array( 'model_id' => 'social_share' ) ) ?>
                  </div>

                <?php endif ?>
              </div>
            </div>
          </div>
          <?php czr_fn_render_template( 'content/post-lists/singles/footers/post_list_single_footer_author' ) ?>
        </section>
      </div>
    </article>
<?php if ( czr_fn_is_loop_end() ) : ?>
  </div>
</div>
<?php endif;