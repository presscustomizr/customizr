<?php
/**
* Featured pages actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_featured_pages' ) ) :
  class TC_featured_pages {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          add_action  ( '__before_main_container'     , array( $this , 'tc_fp_block_display'), 10 );
      }



      /**
    	* The template displaying the front page featured page block.
    	*
    	*
    	* @package Customizr
    	* @since Customizr 3.0
    	*/
      function tc_fp_block_display() {

      		//gets display options
      		$tc_show_featured_pages 	      = esc_attr( tc__f( '__get_option' , 'tc_show_featured_pages' ) );
      		$tc_show_featured_pages_img     = esc_attr( tc__f( '__get_option' , 'tc_show_featured_pages_img' ) );

          if ( !apply_filters( 'tc_show_fp', 0 != $tc_show_featured_pages && tc__f('__is_home') ) )
            return;

      		//gets the featured pages array and sets the fp layout
      		$fp_ids                         = apply_filters( 'tc_featured_pages_ids' , TC_init::$instance -> fp_ids);
          $fp_nb                          = count($fp_ids);
          $fp_per_row                     = apply_filters( 'tc_fp_per_line', 3 );
          
          //defines the span class
          $span_array = array(
            1 => 12,
            2 => 6,
            3 => 4,
            4 => 3,
            5 => 2,
            6 => 2,
            7 => 2
          );
          $span_value = 4;
          $span_value = ( $fp_per_row > 7) ? 1 : $span_value;
          $span_value = isset( $span_array[$fp_per_row] ) ? $span_array[$fp_per_row] :  $span_value;
          
          //save $args for filter
          $args = array($fp_ids, $fp_nb, $fp_per_row, $span_value);

      		?>

          <?php ob_start(); ?>

    			<div class="container marketing">

            <?php 
              do_action ('__before_fp') ;

              $j = 1;
              for ($i = 1; $i <= $fp_nb ; $i++ ) {
                    printf('%1$s<div class="span%2$s fp-%3$s">%4$s</div>%5$s',
                        ( 1 == $j ) ? '<div class="row widget-area" role="complementary">' : '',
                        $span_value,
                        $fp_ids[$i - 1],
                        $this -> tc_fp_single_display( $fp_ids[$i - 1] , $tc_show_featured_pages_img ),
                        ( $j == $fp_per_row || $i == $fp_nb ) ? '</div>' : ''
                    );
              //set $j back to start value if reach $fp_per_row
              $j++;
              $j = ($j == ($fp_per_row + 1)) ? 1 : $j;
              }

              do_action ('__after_fp') ; 
            ?>

    			</div><!-- .container -->

          <?php  echo ! tc__f( '__is_home_empty') ? apply_filters( 'tc_after_fp_separator', '<hr class="featurette-divider '.current_filter().'">' ) : ''; ?>

         <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          echo apply_filters( 'tc_fp_block_display' , $html, $args );
  	   }





  	     /**
        * The template displaying one single featured page
        *
        * @package Customizr
        * @since Customizr 3.0
        * @param area are defined in featured-pages templates,show_img is a customizer option
        * @todo better area definition : dynamic
        */
        function tc_fp_single_display( $fp_single_id,$show_img) {
          $_skin_color                        = TC_utils::$instance -> tc_get_skin_color();
          $fp_holder_img                      = apply_filters (
            'tc_fp_holder_img' , 
            sprintf('<img class="tc-holder-img" data-src="holder.js/270x250/%1$s:%2$s" data-no-retina alt="Holder Thumbnail" />',
              ( '#E4E4E4' != $_skin_color ) ? '#EEE' : '#5A5A5A',
              $_skin_color
            ) 
          );
          $featured_page_id                   = 0;
          
          //if fps are not set
          if ( null == tc__f( '__get_option' , 'tc_featured_page_'.$fp_single_id ) || ! tc__f( '__get_option' , 'tc_featured_page_'.$fp_single_id ) ) {
              //admin link if user logged in
              $featured_page_link             = is_user_logged_in() ? apply_filters( 'tc_fp_link_url', admin_url().'customize.php' , $fp_single_id ) : '';
              $admin_link                     = is_user_logged_in() ? '<a href="'.admin_url().'customize.php" title="'.__( 'Customizer screen' , 'customizr' ).'">'.__( ' here' , 'customizr' ).'</a>' : '';
              
              //rendering
              $featured_page_id               =  null;
              $featured_page_title            =  apply_filters( 'tc_fp_title', __( 'Featured page' , 'customizr' ), $fp_single_id, $featured_page_id);
              $text                           =  apply_filters( 
                                                  'tc_fp_text', 
                                                  sprintf( __( 'Featured page description text : use the page excerpt or set your own custom text in the Customizr screen%s.' , 'customizr' ),
                                                    $admin_link 
                                                  ),
                                                  $fp_single_id,
                                                  $featured_page_id
                                                );
              $fp_img                         =  apply_filters ('fp_img_src' , $fp_holder_img, $fp_single_id , $featured_page_id );

          }
            
          else {
              $featured_page_id               = apply_filters( 'tc_fp_id', esc_attr( tc__f( '__get_option' , 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );

              $featured_page_link             = apply_filters( 'tc_fp_link_url', get_permalink( $featured_page_id ), $fp_single_id );

              $featured_page_title            = apply_filters( 'tc_fp_title', get_the_title( $featured_page_id ), $fp_single_id, $featured_page_id );
              //when are we displaying the edit link?
              $edit_enabled                   = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : false;
              $edit_enabled                   = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;
              $edit_enabled                   = apply_filters( 'tc_edit_in_fp_title', $edit_enabled );

              $featured_text                  = apply_filters( 'tc_fp_text', tc__f( '__get_option' , 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
              $featured_text                  = apply_filters( 'tc_fp_text_sanitize', strip_tags( html_entity_decode( $featured_text ) ), $fp_single_id, $featured_page_id );

              //get the page/post object
              $page                           = get_post($featured_page_id);
                
              //set page excerpt as default text if no $featured_text
              $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
              $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_content )) : $text ;

              //limit text to 200 car
              $default_fp_text_length         = apply_filters( 'tc_fp_text_length', 200, $fp_single_id, $featured_page_id );
              $text                           = ( strlen($text) > $default_fp_text_length ) ? substr( $text , 0 , strpos( $text, ' ' , $default_fp_text_length) ). ' ...' : $text;
                
              //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
              $fp_img_size                    = apply_filters( 'tc_fp_img_size' , 'tc-thumb', $fp_single_id, $featured_page_id );
              //allow user to specify a custom image id
              $fp_custom_img_id               = apply_filters( 'fp_img_id', false , $fp_single_id , $featured_page_id );

              if ( has_post_thumbnail( $featured_page_id ) && ! $fp_custom_img_id ) {
                    $fp_img_id                = get_post_thumbnail_id( $featured_page_id );

                    //check if tc-thumb size exists for attachment and return large if not
                    $image                    = wp_get_attachment_image_src( $fp_img_id , $fp_img_size );
                    $fp_img_size              = ( isset($image[3]) && null == $image[3] ) ? 'medium' : $fp_img_size ;

                    $fp_img                   = get_the_post_thumbnail( $featured_page_id , $fp_img_size);
                    //get height and width if set
                    if ( ! empty($image[1]) && ! empty($image[2]) ) {
                      $fp_img_height            = $image[2];
                      $fp_img_width             = $image[1];
                    }
              }

              //If not uses the first attached image
              else {
                  //look for attachements
                  $tc_args = array(
                    'numberposts'           =>  1,
                    'post_type'             =>  'attachment' ,
                    'post_status'           =>  null,
                    'post_parent'           =>  $featured_page_id,
                    'post_mime_type'        =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' )
                    ); 

                    $attachments            =  ! $fp_custom_img_id ? get_posts( $tc_args) : get_post( $fp_custom_img_id );

                    if ( $attachments) {

                        foreach ( $attachments as $attachment) {
                           //check if tc-thumb size exists for attachment and return large if not
                          $image            = wp_get_attachment_image_src( $attachment->ID, $fp_img_size );
                          $fp_img_size      = ( isset($image[3]) && false == $image[3] ) ? 'medium' : $fp_img_size;
                          $fp_img           = wp_get_attachment_image( $attachment->ID, $fp_img_size );
                          //get height and width
                          if ( ! empty($image[1]) && ! empty($image[2]) ) {
                            $fp_img_height            = $image[2];
                            $fp_img_width             = $image[1];
                          }
                        }//end foreach

                    }//end if

              }

              //finally we define a default holder if no thumbnail found or page is protected
              $fp_img                 = apply_filters ('fp_img_src' , ( ! isset( $fp_img) || post_password_required($featured_page_id) ) ? $fp_holder_img : $fp_img , $fp_single_id , $featured_page_id );
            }//end if

            //Let's render this
            ob_start();
            ?>

            <div class="widget-front">
              <?php 
                if ( isset( $show_img) && $show_img == 1 ) { //check if image option is checked
                  printf('<div class="thumb-wrapper %1$s">%2$s%3$s</div>',
                     ( $fp_img == $fp_holder_img ) ? 'tc-holder' : '',
                     apply_filters('tc_fp_round_div' , sprintf('<a class="round-div" href="%1$s" title="%2$s"></a>',
                                                      $featured_page_link,
                                                      $featured_page_title
                                                    ) , 
                                  $fp_single_id,
                                  $featured_page_id
                                  ),
                     $fp_img
                  );
                }//end if image enabled check
                

                //title block
                $tc_fp_title_block  = sprintf('<%1$s>%2$s %3$s</%1$s>',
                                    apply_filters( 'tc_fp_title_tag' , 'h2', $fp_single_id, $featured_page_id ),
                                    $featured_page_title,
                                    ( isset($edit_enabled) && $edit_enabled )? sprintf('<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
                                              get_edit_post_link(),
                                              __( 'Edit' , 'customizr' )
                                              ) : ''
                );
                echo apply_filters( 'tc_fp_title_block' , $tc_fp_title_block , $featured_page_title , $fp_single_id, $featured_page_id );

                //text block
                $tc_fp_text_block   = sprintf('<p class="fp-text-%1$s">%2$s</p>',
                                    $fp_single_id,
                                    $text
                );
                echo apply_filters( 'tc_fp_text_block' , $tc_fp_text_block , $fp_single_id , $text, $featured_page_id);

                //button block
                $tc_fp_button_block = sprintf('<a class="%1$s" href="%2$s" title="%3$s">%4$s</a>',
                                    apply_filters( 'tc_fp_button_class' , 'btn btn-primary fp-button', $fp_single_id ),
                                    $featured_page_link,
                                    $featured_page_title,
                                    apply_filters( 'tc_fp_button_text' , esc_attr( tc__f( '__get_option' , 'tc_featured_page_button_text') ) , $fp_single_id )
                );
                echo apply_filters( 'tc_fp_button_block' , $tc_fp_button_block , $featured_page_link , $featured_page_title , $fp_single_id, $featured_page_id );

              ?>

            </div><!-- /.widget-front -->
            
            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();
            return apply_filters( 'tc_fp_single_display' , $html, $fp_single_id, $show_img, $fp_img, $featured_page_link, $featured_page_title, $text, $featured_page_id );
        }//end of function

   }//end of class
endif;