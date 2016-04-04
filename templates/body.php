<body <?php body_class() ?> <?php echo tc_get( 'element_attributes' ); ?>  >
  <?php
    if ( tc_has('sidenav') && tc_has('header') ){ tc_render_template('header/sidenav'); };
  ?>

  <?php do_action('__before_page_wrapper'); ?>

  <div id="tc-page-wrap">

    <?php tc_render_template('header/header'); ?>

    <?php do_action('__before_main_wrapper'); ?>

    <div id="main-wrapper" class="container">

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
                  if ( tc_has('404') ) { tc_render_template('content/content_404', '404'); }

                  elseif ( tc_has('no_results') ) { tc_render_template('content_no_results', 'no_results'); }

                  if ( tc_has('posts_list_headings') ) { tc_render_template('content/headings', 'posts_list_headings'); }

                  if ( tc_has('main_loop') ) { tc_render_template('loop', 'main_loop'); }

                  if ( tc_has('comments') ) { tc_render_template('content/comments'); }

                  if ( is_singular() && tc_has('post_navigation_singular') )
                    tc_render_template('content/post_navigation', 'post_navigation_singular');
                  elseif ( is_archive() && tc_has('post_navigation_posts') )
                    tc_render_template('content/post_navigation', 'post_navigation_posts');
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

    <?php tc_render_template('footer/footer'); ?>

  </div><!-- #tc-page-wrap -->

  <?php do_action('__after_page_wrapper'); ?>

  <?php wp_footer() ?>
</body>
