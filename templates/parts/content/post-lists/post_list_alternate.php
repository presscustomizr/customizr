<?php
/**
 * The template for displaying the alternate article wrapper
 *
 * In WP loop
 *
 * @package Customizr
 */
if ( czr_fn_get_property( 'print_start_wrapper' ) ) : ?>
<div class="grid-container grid-container__alternate <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="alternate__wrapper row">
<?php endif ?>
    <article <?php czr_fn_echo( 'article_selectors' ) ?> >
      <div class="grid__item <?php czr_fn_echo('grid_item_class') ?>">
        <div class="sections-wrapper <?php czr_fn_echo( 'sections_wrapper_class' ) ?>">
        <?php
            if ( czr_fn_get_property( 'has_post_media' ) ) {
              /* Media */
              czr_fn_render_template(
                'content/common/media',
                array(
                  'model_id'   => 'media',
                  'reset_to_defaults' => false,

                  'model_args' =>  array(
                    'element_class'            => czr_fn_get_property( 'media_class' ),
                    'inner_wrapper_class'      => czr_fn_get_property( 'media_inner_class' ),
                    'link_class'               => czr_fn_get_property( 'media_link_class' ),
                    'image_centering'          => czr_fn_get_property( 'image_centering' ),
                  )
                )
              );
            }
             /* Content */
            ?>
            <section class="tc-content entry-content__holder <?php czr_fn_echo('content_class') ?>">
              <div class="entry-content__wrapper">
              <?php
                /* header */
                czr_fn_render_template( 'content/post-lists/item-parts/headings/post_list_item_header');
                /* content inner */
                czr_fn_render_template(
                    'content/post-lists/item-parts/contents/post_list_item_content_inner',
                    array(
                      'model_id'   => 'post_list_item_content_inner',
                      'reset_to_defaults' => false,
                    )

                );
                /* footer */
                czr_fn_render_template( 'content/post-lists/item-parts/footers/post_list_item_footer' );
              ?>
              </div>
            </section>
            <?php

        ?>
        </div>
      </div>
    </article>
<?php if ( czr_fn_get_property( 'print_end_wrapper' ) ) : ?>
  </div>
</div>
<?php endif;