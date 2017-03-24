<?php
/**
 * The template for displaying the alternate article wrapper
 *
 * In WP loop
 *
 * @package Customizr
 */
if ( czr_fn_get( 'print_start_wrapper' ) ) : ?>
<div class="grid-container__alternate <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="alternate__wrapper row">
<?php endif ?>
    <article <?php czr_fn_echo( 'article_selectors' ) ?> >
      <div class="grid__item <?php czr_fn_echo('grid_item_class') ?>">
        <div class="sections-wrapper <?php czr_fn_echo( 'sections_wrapper_class' ) ?>">
        <?php

            /* Media */
            if ( $has_post_media = czr_fn_get('has_post_media') ) {
              czr_fn_render_template(
                'content/post-lists/item-parts/post_list_item_media',
                array(
                  'model_args' =>  array(
                    'has_post_media'           => $has_post_media,
                    'element_class'            => czr_fn_get( 'media_cols' ),
                    'is_full_image'            => czr_fn_get( 'is_full_image' ),
                    'has_format_icon_media'    => czr_fn_get( 'has_format_icon_media' ),
                  )
                )
              );
            }
             /* Content */
            ?>
            <section class="tc-content entry-content__holder <?php czr_fn_echo('content_cols') ?>">
              <div class="entry-content__wrapper">
              <?php
                /* header */
                czr_fn_render_template( 'content/post-lists/item-parts/headings/post_list_item_header' );
                /* content inner */
                czr_fn_render_template( 'content/post-lists/item-parts/contents/post_list_item_content_inner' );
                /* footer */
                czr_fn_render_template( 'content/post-lists/item-parts/footers/post_list_item_footer',
                        array(
                          'model_args' => array(
                            'show_comment_meta' => czr_fn_get('show_comment_meta')
                          )
                        )
                )
              ?>
              </div>
            </section>
            <?php

        ?>
        </div>
      </div>
    </article>
<?php if ( czr_fn_get( 'print_end_wrapper' ) ) : ?>
  </div>
</div>
<?php endif;