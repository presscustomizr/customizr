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
<div class="grid-container__plain short <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
<?php
  do_action( '__post_list_plain_loop_start', czr_fn_get('id') );
endif ?>
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
                'is_full_image'            => czr_fn_get( 'is_full_image' )
              )
            )
          );
      ?>
      <section class="tc-content entry-content__holder">
      <?php
        /*
        * Get the category list if any
        * impacts on inner layout
        */
        $cat_list = czr_fn_get( 'cat_list', 'post_metas', array(
          'limit' => 3
          )
        );
        czr_fn_render_template(
          'content/post-lists/singles/headings/post_list_single_header-no_metas',
          array(
            'model_class' => 'content/post-lists/singles/headings/post_list_single_header',
            'model_args'  => array(
              'entry_header_inner_class' => $cat_list ? czr_fn_get( 'plain_entry_header_inner_class' ) : array('col-xs-12'),
              'element_class'            => czr_fn_get( 'plain_entry_header_class' )
            )
          )
        );
      ?>
        <div class="entry-content__wrapper row">
          <?php
          if ( $cat_list ) :
          ?>
            <div class="entry-meta tax__container col-md-3 col-xs-12 small caps">
              <?php echo $cat_list ?>
            </div>

          <?php
          endif;
          /* Content Inner */
          ?>
          <div class="tc-content-inner-wrapper <?php
            if ( $cat_list )
              czr_fn_echo( 'plain_content_inner_class' );
            else
              echo 'col-xs-12';
            ?>" >
            <?php
            /* Content Inner */
            czr_fn_render_template(
              'content/post-lists/singles/contents/post_list_single_content_inner',
              array(
                'model_args' => array(
                  'show_full_content' => false,
                )
              )
            )
            ?>
          </div>
        </div>
        <?php czr_fn_render_template( 'content/post-lists/singles/footers/post_list_single_footer_author' ) ?>
      </section>
    </div>
  </article>
<?php if ( czr_fn_is_loop_end() ) :
  do_action( '__post_list_plain_loop_end', czr_fn_get('id') );
?>
</div>
<?php endif;