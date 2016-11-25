<?php
/**
 * The template for displaying the alternate article wrapper
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<?php
  if ( czr_fn_is_loop_start() ) : ?>
<div class="grid-container__alternate <?php czr_fn_echo('element_class') ?>"  <?php czr_fn_echo('element_attributes') ?>>
  <div class="alternate__wrapper row">
<?php endif ?>
    <article <?php czr_fn_echo( 'article_selectors' ) ?> >
      <div class="sections-wrapper <?php czr_fn_echo( 'sections_wrapper_class' ) ?> grid__item">
      <?php

        if ( 'content' == czr_fn_get( 'place_1' ) ) {
          /* Content */
          ?>
          <section class="tc-content entry-content__holder <?php czr_fn_echo('content_cols') ?>">
            <div class="entry-content__wrapper">
            <?php
              /* header */
              czr_fn_render_template( 'content/post-lists/singles/headings/post_list_single_header' );
              /* content inner */
              czr_fn_render_template( 'content/post-lists/singles/contents/post_list_single_content_inner' );
              /* footer */
              czr_fn_render_template( 'content/post-lists/singles/footers/post_list_single_footer' );
            ?>
            </div>
          </section>
          <?php
          /* Media */
          if ( $has_post_media = czr_fn_get('has_post_media') ) {
            czr_fn_render_template(
              'content/post-lists/singles/post_list_single_media',
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

        } else {
          /* Media */
          if ( $has_post_media = czr_fn_get('has_post_media') ) {
            czr_fn_render_template(
              'content/post-lists/singles/post_list_single_media',
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
              czr_fn_render_template( 'content/post-lists/singles/headings/post_list_single_header' );
              /* content inner */
              czr_fn_render_template( 'content/post-lists/singles/contents/post_list_single_content_inner' );
              /* footer */
              czr_fn_render_template( 'content/post-lists/singles/footers/post_list_single_footer' );
            ?>
            </div>
          </section>
          <?php
        }
      ?>
      </div>
    </article>
<?php if ( czr_fn_is_loop_end() ) : ?>
  </div>
</div>
<?php endif;