    <?php do_action('__before_main_wrapper'); ?>

    <div id="main-wrapper" class="container" <?php tc_echo('element_attributes') ?>>

      <?php if ( tc_has('breadcrumb') ) { tc_render_template('modules/breadcrumb'); } ?>

      <?php do_action('__before_main_container'); ?>

      <div class="container" role="main">
        <div class="<?php tc_echo( 'column_content_class' ) ?>">
          <?php
            if ( tc_has('left_sidebar') ) { tc_render_template('modules/widget_area_wrapper', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php tc_echo( 'article_wrapper_class' ) ?>">
                <?php
                  /* 404 and search with no results */
                  if ( CZR() -> controllers -> tc_is_no_results() || is_404() ) {
                    if ( tc_has('404') ) { tc_render_template('content/singles/404', '404'); }
                    elseif ( tc_has('no_results') ) { tc_render_template('content/singles/no_results', 'no_results'); }
                  }

                  else {
                    if ( tc_has('posts_list_headings') ) { tc_render_template('content/post-lists/post_list_headings', 'posts_list_headings'); }

                    if ( tc_has('main_loop') ) { tc_render_template('content/loop', 'main_loop'); }

                    if ( tc_has('comments') ) { tc_render_template('content/comments/comments'); }

                    if ( is_singular() && tc_has('post_navigation_singular') )
                      tc_render_template('content/singles/post_navigation_singular', 'post_navigation_singular');
                    elseif ( /*is_archive() && DISPLAYED ALSO IN THE BLOG*/ tc_has('post_navigation_posts') )
                      tc_render_template('content/post-lists/post_navigation_posts', 'post_navigation_posts');
                  //do_action( '__content__')
                  }
                ?>
              </div>

              <?php do_action('__after_content'); ?>

            <?php
            if ( tc_has('right_sidebar') ) { tc_render_template('modules/widget_area_wrapper', 'right_sidebar'); }
            //tc_render_template('content/main_container');
            //do_action( '__main_container__')
          ?>
        </div>
      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>
