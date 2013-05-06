<?php
/**
 * The main template file.
 *
 *
 * @package Customizr
 * @since Customizr 1.0
 */
get_header();

	//no content if previewing a slide
      /*$object = get_queried_object(); 
      if(is_single() && $object->post_type == 'slide')
        return;*/

      //get layout options
      global $tc_theme_options;
      
      $tc_type =  get_post_type( tc_get_the_ID());
      tc_get_sidebar('front');
      tc_get_breadcrumb();
      ?>
            <div class="container" role="main">
                <div class="row">
                    <?php 
                        tc_get_sidebar('left');

				         //initialize the thumbnail class alternative index
				        global $tc_i;
				        $tc_i = 1;
				        echo '<div class="'.$tc_theme_options['tc_current_screen_layout']['class'].' article-container">';

				            /* get additionnal header for archive, search, 404 */
				            get_template_part( 'parts/post', 'list-header');

				              /* Start the Loop for all other case*/
				              if ( have_posts() ) {
				                while ( have_posts() ) {
				                    the_post();
				                    
				                      get_template_part( 'article', 'content');         
				                    
				                    comments_template( '', true );
				                    
				                    $tc_i++;
				                }
				              }
				              //no loop if error 404 or no search results
				              else { //(is_404() || (is_search() && !have_posts())) 
				                get_template_part( 'article', 'content');
				              }

				            /* include navigation for posts */
				            if($tc_type != 'page')
				              get_template_part( 'parts/nav');

				        echo '</div>';//end of current post layout class

                        tc_get_sidebar('right');
                    ?>
                </div><!--#row -->
            </div><!-- #container -->
    <?php

get_footer();

?>