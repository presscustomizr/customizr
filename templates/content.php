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
                  if ( tc_has('posts_list_headings') ) { tc_render_template('content/post-lists/post_list_headings', 'posts_list_headings'); }

                  if ( tc_has('main_loop') ) { tc_render_template('content/loop', 'main_loop'); }

                  if ( tc_has('comments') ) { tc_render_template('content/comments/comments'); }

                  if ( is_singular() && tc_has('post_navigation_singular') )
                    tc_render_template('content/navigation/post_navigation', 'post_navigation_singular');
                  elseif ( /*is_archive() && DISPLAYED ALSO IN THE BLOC*/ tc_has('post_navigation_posts') )
                    tc_render_template('content/navigation/post_navigation', 'post_navigation_posts');
                  //do_action( '__content__')
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
