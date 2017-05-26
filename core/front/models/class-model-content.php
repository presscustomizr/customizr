<?php
class CZR_content_model_class extends CZR_Model {


      /*
      * @override
      */
      function __construct( $model ) {

            parent::__construct( $model );

            //in singular context we might want to display the featured image for standard headings
            $this -> czr_fn_process_singular_thumbnail();
      }



      function czr_fn_process_singular_thumbnail() {

            if ( ! is_singular() )
                  return;

            $context =  is_single() ? 'post' : 'page';

            //do nothing if we don't display regular {context} heading
            if ( ! czr_fn_has( "regular_{$context}_heading" ) )
                  return;

            //__before_main_wrapper, 200
            //__before_regular_{post|page}_heading_title
            //__after_regular_{post|page}_heading_title
            $_singular_thumb_option = czr_fn_opt( "tc_single_${context}_thumb_location" );

            //nothing to do:
            if ( ! ( $_singular_thumb_option && 'hide' != $_singular_thumb_option ) ) {

                  return;

            }

            //define old customizr compatibility map:
            $_compat_location_hook_map = array(

                  //old hook                => new_hook

                  '__before_main_wrapper'   => '__before_main_wrapper',
                  '__before_content'        => '__before_regular_heading_title',
                  '__after_content_title'   => '__after_regular_heading_title',

            );


            //process location
            $_exploded_location   = explode('|', $_singular_thumb_option );
            $_hook                = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
            //map the old location hook to the new location hook
            $_hook                = array_key_exists( $_hook, $_compat_location_hook_map ) ? $_compat_location_hook_map[ $_hook ] : '__before_regular_heading_title';



            //let's prepare the thumb
            //register the model and the template for displaying the thumbnail at a specific hook
            $singular_thumb_model_id = czr_fn_register( array( 'template' => 'content/common/media',

                  'id'         => 'singular_thumbnail',
                  'hook'       => $_hook,
                  'args'       => array(

                        'media_type'               => 'wp_thumb',
                        'has_permalink'            => false,
                        'has_lightbox'             => false,
                        'element_class'            => 'tc-single-post-thumbnail-wrapper tc-singular-thumbnail-wrapper'
                        //TODO: img size depending on the location?
                        //consider that we decided to not have image sizes for the slider so...
                  ),
                  'controller' => 'singular_thumbnail'
            ) );

            //control the vsibility
            add_filter( "czr_do_render_view_{$singular_thumb_model_id}", array( $this, 'czr_fn_display_view_singular_thumbnail' ), 100, 2 );

            //css
            //needed only when not __after_regular_heading_title
            if ( '__after_regular_heading_title' != $_hook ) {

                  add_filter( 'czr_user_options_style'    , array( $this , 'czr_fn_write_thumbnail_inline_css') );

            }

      }



      function czr_fn_setup_children() {

            $children = array(

                  array(

                        //registered here also because we access to its properties from other templates
                        //which as of now is only possibile with already registered models
                        'model_class' => 'content/post-metas/post_metas',
                        'id' => 'post_metas',

                  ),

                  /*********************************************
                  * SLIDER
                  *********************************************/
                  /* Need to be registered before rendering because of the custom style*/
                  array(

                        'model_class' => 'modules/slider/slider',
                        'id'          => 'main_slider'

                  ),
                  //slider of posts
                  array(

                        'id'          => 'main_posts_slider',
                        'model_class' => array( 'parent' => 'modules/slider/slider', 'name' => 'modules/slider/slider_of_posts' )

                  ),
                  /** end slider **/

                  /* Needs to access the czr_user_options_style */
                  /*********************************************
                  * GRID (POST LIST)
                  *********************************************/
                  array(

                        'id'          => 'post_list_grid',
                        'model_class' => 'modules/grid/grid_wrapper',

                  ),
                  /* END GRID */

            );

            return $children;
      }


      function czr_fn_get_content_to_render() {

            //fallback
            $to_render = array( 'loop_item' => array('content/singular/page_content' ) );

            if ( czr_fn_is_list_of_posts() ) {

                  $to_render = array( 'loop_item' => array( 'modules/grid/grid_wrapper', array( 'model_id' => 'post_list_grid' ) ) );

                  if ( czr_fn_has('post_list') ) {

                        $to_render = array( 'loop_item' => array('content/post-lists/post_list_alternate' ));

                  }elseif ( czr_fn_has('post_list_plain') ) {

                        $to_render = array( 'loop_item' => array('content/post-lists/post_list_plain' ));

                  }

            }
            elseif( is_single() ) {

                  $to_render = array( 'loop_item' => array('content/singular/post_content' ));

            }

            return $to_render;

      }



      /*
      * Singular thumbnail stuff
      *
      * TODO : maybe create a specific model
      * slider and fi before main wrapper xor
      */

      function czr_fn_display_view_singular_thumbnail( $bool, $model ) {

        if ( !$bool )
          return;

        $_hook = isset( $model->hook ) ? $model->hook : false;

        if ( !$_hook )
          return $bool;

        $_slider_shown = ( did_action( '__after_main_slider' ) || did_action( '__after_main_posts_slider' ) );

        return '__before_main_wrapper' == $_hook && $_slider_shown ? false : true;

      }


      function czr_fn_write_thumbnail_inline_css( $_css ) {
            $context =  is_single() ? 'post' : 'page';

            $_thumb_height   = apply_filters( "tc_${context}_post_thumb_height", esc_attr( czr_fn_opt( "tc_{$context}_post_thumb_height" ) ) );
            $_thumb_height   = (! $_thumb_height || ! is_numeric($_thumb_height) ) ? 250 : $_thumb_height;

            return sprintf("%s\n%s",
              $_css,
              ".tc-singular-thumbnail-wrapper .entry-media__wrapper {
                max-height: {$_thumb_height}px;
                height :{$_thumb_height}px
              }\n
              .tc-singular-thumbnail-wrapper .js-centering.entry-media__wrapper img {
                opacity : 0;
                -webkit-transition: opacity .5s ease-in-out;
                -moz-transition: opacity .5s ease-in-out;
                -ms-transition: opacity .5s ease-in-out;
                -o-transition: opacity .5s ease-in-out;
                transition: opacity .5s ease-in-out;
              }\n"
            );
      }


}