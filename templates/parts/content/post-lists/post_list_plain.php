<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<?php if ( czr_fn_get_property( 'print_start_wrapper' ) ) : ?>
<div class="grid-container grid-container__plain <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="plain__wrapper row">
<?php endif ?>
    <article <?php czr_fn_echo( 'article_selectors' ) ?> >
      <div class="sections-wrapper grid__item">
        <?php
          if ( czr_fn_get_property( 'has_post_media' ) ) {
            czr_fn_render_template(
              'content/common/media',
               array(
                //'reset_to_defaults' => false,
                'model_args' => array(
                  'element_class'            => czr_fn_get_property('media_class'),
                  'media_type'               => 'wp_thumb',
                  'image_centering'          => 'no-js-centering'
               )
              )
            );
          }
        ?>
        <section class="tc-content entry-content__holder">
          <?php
            czr_fn_render_template(
              'content/post-lists/item-parts/headings/post_list_item_header-no_metas',
              array(
                'model_class' => 'content/post-lists/item-parts/headings/post_list_item_header',
                'model_args'  => array(
                  'entry_header_inner_class' => czr_fn_get_property( 'entry_header_inner_class' ),
                  'element_class'            => array('row')
                )
              )
            );
          ?>
          <div class="entry-content__wrapper row">
            <?php if ( $cat_list = czr_fn_get_property( 'cat_list' ) ) : ?>
              <div class="<?php czr_fn_echo( 'cat_list_class' ) ?>">
                <div class="tax__container entry-meta caps post-info">
                  <?php echo $cat_list ?>
                </div>
              </div>

            <?php endif; ?>
            <div class="tc-content-inner-wrapper <?php czr_fn_echo( 'content_inner_class' ) ?>" >
              <?php
              czr_post_format_part();
              /* Content Inner */
              czr_fn_render_template(
                'content/post-lists/item-parts/contents/post_list_item_content_inner',
                array(
                  'model_args' => array(
                    'content_type' => 'full',
                  )
                )
              )
              ?>
              <div class="row entry-meta justify-content-between align-items-center">
                <?php  if ( czr_fn_is_registered_or_possible('post_metas') && (bool) $tag_list = czr_fn_get_property( 'tag_list', 'post_metas' ) ) : ?>
                    <div class="post-tags col-xs-12 col-sm-auto col-sm">
                      <ul class="tags">
                        <?php echo $tag_list ?>
                      </ul>
                    </div>
                  <?php endif ?>

                  <!-- fake need to have social links somewhere -->
                <?php if ( czr_fn_is_registered_or_possible('social_share') ) : ?>

                  <div class="post-share col-xs-12 col-sm-auto col-sm">
                    <?php czr_fn_render_template( 'modules/common/social_block', array( 'model_id' => 'social_share' ) ) ?>
                  </div>

                <?php endif ?>
              </div>
            </div>
          </div>
          <?php
              /* footer */
              czr_fn_render_template( 'content/post-lists/item-parts/footers/post_list_item_footer_author',
                      array(
                        'model_args' => array(
                          'show_comment_meta' => czr_fn_get_property('show_comment_meta')
                        )
                      )
              )
          ?>
        </section>
      </div>
    </article>
<?php if ( czr_fn_get_property( 'print_end_wrapper' ) ) : ?>
  </div>
</div>
<?php endif;