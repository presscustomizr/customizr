<?php
/*
* Loop template
*/
if ( have_posts() ) {
    do_action( '__loop_start' );

      //WHAT DO WE WANT TO RENDER ?
      //=> FOR THE MOMENT WE ONLY HAVE A WP QUERY, which could be altered if set when invoking the following functions in templates/index.php
      //czr_fn_render_template(
          // 'loop',
          // 'model_args' = array(
          //     'query' => ...a custom query like new WP_Query()...
          // )
          //array( 'model_args' => czr_fn_get_main_content_loop_item() )
      //);

      //AT THIS POINT WE NEED TO SET A LOOP ITEM TEMPLATE AND OPTIONAL ARGUMENTS
      //LET'S DO IT

      //array( 'loop_item_tmpl' => string, loop_item_model => array() )
      $loop_item_tmpl = apply_filters( 'czr_page_loop_item_template', 'content/singular/page_content');
      $loop_item_model = array();

      if ( czr_fn_is_list_of_posts() ) {
          $loop_item_tmpl = 'modules/grid/grid_wrapper';
          $loop_item_model = array( 'model_id' => 'post_list_grid' );
          //$to_render = array( 'loop_item' => array( 'modules/grid/grid_wrapper', array( 'model_id' => 'post_list_grid' ) ) );

          if ( czr_fn_is_registered_or_possible('post_list') ) {
                $loop_item_tmpl = 'content/post-lists/post_list_alternate';
                $loop_item_model = array();
          
          } elseif ( czr_fn_is_registered_or_possible('post_list_plain') ) {
                $loop_item_tmpl = 'content/post-lists/post_list_plain';
                $loop_item_model = array();
                //$to_render = array( 'loop_item' => array('content/post-lists/post_list_plain' ));
          }
          $loop_item_tmpl = apply_filters( 'czr_post_list_loop_item_template', $loop_item_tmpl);
          $loop_item_model = apply_filters( 'czr_post_list_loop_item_model', $loop_item_model );

      } elseif ( is_single() ) {
          if ( czr_fn_is_registered_or_possible( 'attachment_image' ) ) {
                $loop_item_tmpl = apply_filters( 'czr_single_attachment_loop_item_template', 'content/singular/attachment_image_content');
          } else {
                $loop_item_tmpl = apply_filters( 'czr_single_loop_item_template', 'content/singular/post_content');  
          }          
      }

      $loop_item = apply_filters( "czr_main_content_loop_item", array(
          'loop_item_tmpl' => $loop_item_tmpl,
          'loop_item_model' => $loop_item_model
      ) );

      //LET'S LOOP ON THE WP QUERY
      while ( have_posts() ) {
          the_post();

          //czr_fn_render_template takes 2 params :
          //1) the template ( mandatory )
          //2) optional model properties or the entire model as a array()
          czr_fn_render_template(
              $loop_item['loop_item_tmpl'],//<= is a relative path
              $loop_item['loop_item_model']
          );

      }

    do_action('__loop_end' );
}