<?php
if ( ! function_exists( 'tc_get_slider' ) ) :
/**
 *
 * @package Customizr
 * @since Customizr 1.0
 *
 */
    function tc_get_slider() {
      //prevent the main ID override when creating a new query. (only if it is included in the main loop but who knows...)
      if (is_404() || is_archive() || is_search())
        return;
      global $tc_theme_options;

      //get the current slider id
      $slider_name_id = $tc_theme_options['tc_current_screen_slider'];
        if(is_front_page() && $tc_theme_options['tc_front_slider'] !=null)
          $slider_name_id = $tc_theme_options['tc_front_slider'];

      //get slider options if any
      $layout_value   = get_post_meta( get_the_ID(), $key = 'slider_layout_key', $single = true );
      if (is_home() || is_front_page()) {
        $layout_value = tc_get_options('tc_slider_width');
      }
      $layout_class = '';
      $img_size     = 'slider';
      if ($layout_value == 0) {//if boxed slider is checked
        $layout_class = 'container';
        $img_size     = 'slider';
      }
      else {
        $img_size     = 'slider-full';
      }

      //render the slider : two cases
      switch ($slider_name_id) {
        case 'demo':

        //admin link if user logged in
        $admin_link = '';
        if (is_user_logged_in())
          $admin_link = admin_url().'customize.php';

        ?>
          <div id="customizr-slider" class="<?php echo $layout_class ?> carousel slide">
            <div class="carousel-inner">
                <div class="item active">
                   <div class="carousel-image">
                      <img width="1170" height="500" src="<?php echo TC_BASE_URL ?>inc/img/laverie.jpg" class="slide wp-post-image" alt="<?php _e( 'Customizr is a clean responsive theme','customizr') ?>">
                    </div>
                    <div class="carousel-caption">
                        <h1><?php _e( 'Customizr is a clean responsive theme','customizr') ?></h1>
                          <p class="lead"><?php _e( 'Let your creativity speak and easily customiz\'it the way you want!','customizr') ?></p>
                         <a class="btn btn-large btn-primary" href="<?php echo $admin_link; ?>"><?php _e( 'Learn more','customizr') ?></a>
                    </div>
                </div>
              <div class="item">
                 <div class="carousel-image">
                      <img width="1170" height="500" src="<?php echo TC_BASE_URL ?>inc/img/architecture.jpg" class="slide wp-post-image" alt="<?php _e( 'Style your WordPress site in live!','customizr') ?>">
                  </div>
                  <div class="carousel-caption">
                      <h1><?php _e( 'Style your WordPress site in live!','customizr') ?></h1>
                        <p class="lead"><?php _e( 'Many layout and design options are available from the WordPress customizer screen : see your changes in live !','customizr') ?></p>
                       <a class="btn btn-large btn-primary" href="<?php echo $admin_link; ?>"><?php _e( 'Just try it!','customizr') ?></a>
                    </div>
                </div>
              <div class="item">
                 <div class="carousel-image">
                  <img width="1170" height="500" src="<?php echo TC_BASE_URL ?>inc/img/colonnes.jpg" class="slide wp-post-image" alt="<?php _e( 'Create beautiful sliders','customizr') ?>">
                </div>
                <div class="carousel-caption">
                  <h1><?php _e( 'Create beautiful sliders','customizr') ?></h1>
                      <p class="lead"><?php _e( 'Customizr comes with a cool slider generator : add a slider to any post or page!','customizr') ?></p>
                     <a class="btn btn-large btn-primary" href="<?php echo $admin_link; ?>"><?php _e( 'Discover the features','customizr') ?></a>
                </div>
              </div>
            </div><!-- /.carousel-inner -->
            <a class="left carousel-control" href="#customizr-slider" data-slide="prev">&lsaquo;</a>
            <a class="right carousel-control" href="#customizr-slider" data-slide="next">&rsaquo;</a>
          </div>
          <?php
          break;
        
        

        default:
            $tc_theme_options['another_query_in_the_main_loop'] = true;
            $tc_theme_options['original_ID'] = get_the_ID();

            //get the slider ID
            /*There is a tricky case with the blog page. If we choose to assign a page for the blog posts, then this page will return a 
            *'true' value if we test it with is_home(). Even if it is not the home page of the website!
            *to solve this problem, we check with is_front_page().
            */
            global $post;
            //Post type must be 'post'.
            $post_type = get_post_type(tc_get_the_ID());
            

            //define the slide list. 
            /* If we are in the preview mode (condition is post_type = 'slide' && is_single()), showing only one slide. */
            $slides = array();
            if($post_type == 'slide' && is_single()) {
              $slides[0] = get_post( tc_get_the_ID());
            }
            else {
              $slides = get_posts(  array(
                'numberposts'    =>  -1,
                'tax_query' => array(
                                  array(
                                    'taxonomy' => 'slider',
                                    'field' => 'id',
                                    'terms' => $slider_name_id
                                  )
                                ),
                'orderby'      =>  'post_date',
                'order'        =>  'DESC',
                'post_type'      =>  'slide',
                'post_status'    =>  'publish' )
              );
            }
            //init slide index
            $i = 0;
            ?>
            <?php if($slides) : ?>
              <div id="customizr-slider" class="<?php echo $layout_class ?> carousel slide">
                  <div class="carousel-inner">
                    <?php foreach ($slides as $s) { 
                        //set up variables
                        $id           = $s -> ID;
                        $title        = get_the_title( $id );
                        $show_title   = get_post_meta( $id, $key = 'slide_title_key', $single = true );
                        $text         = get_post_meta( $id, $key = 'slide_text_key', $single = true );
                        $text_color   = get_post_meta( $id, $key = 'slide_color_key', $single = true );
                        $button_text  = get_post_meta( $id, $key = 'slide_button_key', $single = true );
                        $button_link  = get_post_meta( $id, $key = 'slide_link_key', $single = true );

                        //set the first slide active
                        $active       = '';
                        if ($i==0) {$active ='active';}

                        //check if $text_color is set and create an html style attribute
                        $color_style ='';
                        if($text_color != null) {
                          $color_style = 'style="color:'.$text_color.'"';
                        }

                      ?>
                    <div class="item <?php echo $active; ?>">
                       <div class="carousel-image">
                        <?php echo get_the_post_thumbnail( $id, $img_size, array('class' => 'slide', 'alt' => get_the_title( $id ) ) ); ?>
                       </div>
                        <?php if (($title != null && $show_title) && $text != null && $button_text != null ) : ?>
                          <div class="carousel-caption">
                            <?php if($title != null && $show_title) : ?>
                              <h1 <?php echo $color_style ?>><?php echo $title ?></h1>
                            <?php endif; ?>
                            <?php if($text != null) : ?>
                              <p <?php echo $color_style ?> class="lead"><?php echo $text ?></p>
                            <?php endif; ?>
                            <?php if($button_text != null && $button_link != null) : ?>
                              <a class="btn btn-large btn-primary" href="<?php echo get_permalink($button_link); ?>"><?php echo $button_text; ?></a>
                            <?php elseif($button_text != null ) : ?>
                             <a class="btn btn-large btn-primary" href="#"><?php echo $button_text;?></a>
                           <?php endif; ?>
                          </div>
                        <?php endif; ?>
                    </div>
                    <?php
                     $i++;
                     }//end slides loop
                    ?>
                  </div><!-- /.carousel-inner -->
                  <a class="left carousel-control" href="#customizr-slider" data-slide="prev">&lsaquo;</a>
                  <a class="right carousel-control" href="#customizr-slider" data-slide="next">&rsaquo;</a>
                </div><!-- /.carousel -->
              <?php endif; ?>
            
            <?php
          break;
      }//end switch
    }
endif;




if ( ! function_exists( 'tc_slider_footer_options' ) ) :
add_action('wp_footer', 'tc_slider_footer_options', 20);
/**
 * Add a the slider options script in wp_footer()
 * @package Customizr
 * @since Customizr 1.0
 *
**/
    function tc_slider_footer_options() {
      //get slider options if any
      $name_value       = get_post_meta( get_the_ID(), $key = 'slider_name_key', $single = true );
      $delay_value      = get_post_meta( get_the_ID(), $key = 'slider_delay_key', $single = true );
      
      //get the slider id and delay if we display home/front page
      if(is_front_page() || is_home()) {
        $name_value     = tc_get_options('tc_front_slider');
        $delay_value    = tc_get_options('tc_slider_delay');
      }

      //render the delay script
      if(!empty($delay_value)) {
          $delay = '{interval:'.$delay_value.'}';
      }
      else {
          $delay = '';
      }

      //fire the slider with the optionnal delay parameter
      if($name_value != null) {//check if a slider is defined
       
        ?>
          <script type="text/javascript">
            !function ($) {
              jQuery(function(){
                // slider init
                $('#customizr-slider').carousel(<?php echo $delay; ?>)
              })
            }(window.jQuery)
          </script>
          
        <?php
      }//end if slider defined
    }
endif;




if ( ! function_exists( 'tc_slider_redirect' ) ) :
/**
 * Redirect to the home page when displaying a single slide post if user is not logged in.
 * @package Customizr
 * @since Customizr 1.0
 *
**/
 function tc_slider_redirect() {
    $object = get_queried_object(); 
    if(is_single() && $object->post_type = 'slide') {
      if (!is_admin() && !is_user_logged_in()) {
        wp_redirect( home_url(), 301 ); 
      exit;
      }
    }
 }
 endif;
