<?php
/**
* Featured pages actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_featured_pages' ) ) :
  class TC_featured_pages {
    static $instance;
    function __construct () {
        self::$instance =& $this;
        add_action( '__before_main_container'     , array( $this , 'tc_fp_block_display'), 10 );
        add_action( '__after_fp'                  , array( $this , 'tc_maybe_display_dismiss_notice'));
    }



    /******************************
    * FP NOTICE VIEW
    *******************************/
    /**
    * hook : __after_fp
    * @since v3.4+
    */
    function tc_maybe_display_dismiss_notice() {
      if ( ! TC_placeholders::tc_is_fp_notice_on() )
        return;

      $_customizer_lnk = apply_filters( 'tc_fp_notice_customizer_url', TC_utils::tc_get_customizer_url( array( 'control' => 'tc_show_featured_pages', 'section' => 'frontpage_sec') ) );

      ?>
      <div class="tc-placeholder-wrap tc-fp-notice">
        <?php
          printf('<p><strong>%1$s</strong></p>',
            sprintf( __("Edit those featured pages %s, or %s (you'll be able to add yours later)." , "customizr"),
              sprintf( '<a href="%3$s" title="%1$s">%2$s</a>', __( "Edit those featured pages", "customizr" ), __( "now", "customizr" ), $_customizer_lnk ),
              sprintf( '<a href="#" class="tc-inline-remove" title="%1$s">%2$s</a>', __( "Remove the featured pages", "customizr" ), __( "remove them", "customizr" ) )
            )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
            __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }



    /******************************
    * FP WRAPPER VIEW
    *******************************/
    /**
  	* The template displaying the front page featured page block.
  	* hook : __before_main_container
  	*
  	* @package Customizr
  	* @since Customizr 3.0
  	*/
    function tc_fp_block_display() {
      if ( ! $this -> tc_show_featured_pages()  )
        return;

      $tc_show_featured_pages_img     = $this -> tc_show_featured_pages_img();

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
                    ( 1 == $j ) ? sprintf('<div class="%1$s" role="complementary">',
                                  implode(" " , apply_filters( 'tc_fp_widget_area' , array( 'row' , 'widget-area' ) ) )
                                  ) : '',
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




      /******************************
      * SINGLE FP VIEW
      *******************************/
	   /**
      * The template displaying one single featured page
      * fired in : tc_fp_block_display()
      *
      * @package Customizr
      * @since Customizr 3.0
      * @param area are defined in featured-pages templates,show_img is a customizer option
      * @todo better area definition : dynamic
      */
      function tc_fp_single_display( $fp_single_id,$show_img) {
        $_skin_color                        = TC_utils::$inst -> tc_get_skin_color();
        $fp_holder_img                      = apply_filters (
          'tc_fp_holder_img' ,
          sprintf('<img class="tc-holder-img" data-src="holder.js/270x250/%1$s:%2$s" data-no-retina alt="Holder Thumbnail" style="width:270px;height:250px;"/>',
            ( '#E4E4E4' != $_skin_color ) ? '#EEE' : '#5A5A5A',
            $_skin_color
          )
        );
        $featured_page_id                   = 0;

        //if fps are not set
        if ( null == TC_utils::$inst->tc_opt( 'tc_featured_page_'.$fp_single_id ) || ! TC_utils::$inst->tc_opt( 'tc_featured_page_'.$fp_single_id ) ) {
            //admin link if user logged in
            $featured_page_link             = '';
            $customizr_link                 = '';
            if ( ! TC___::$instance -> tc_is_customizing() && is_user_logged_in() && current_user_can('edit_theme_options') ) {
              $customizr_link              = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>',
                TC_utils::tc_get_customizer_url( array( 'control' => 'tc_featured_text_'.$fp_single_id, 'section' => 'frontpage_sec') ),
                __( 'Customizer screen' , 'customizr' ),
                __( 'Edit now.' , 'customizr' )
              );
              $featured_page_link          = apply_filters( 'tc_fp_link_url', TC_utils::tc_get_customizer_url( array( 'control' => 'tc_featured_page_'.$fp_single_id, 'section' => 'frontpage_sec') ) );
            }

            //rendering
            $featured_page_id               =  null;
            $featured_page_title            =  apply_filters( 'tc_fp_title', __( 'Featured page' , 'customizr' ), $fp_single_id, $featured_page_id);
            $text                           =  apply_filters(
                                                'tc_fp_text',
                                                sprintf( '%1$s %2$s',
                                                  __( 'Featured page description text : use the page excerpt or set your own custom text in the Customizr screen.' , 'customizr' ),
                                                  $customizr_link
                                                ),
                                                $fp_single_id,
                                                $featured_page_id
                                              );
            $fp_img                         =  apply_filters ('fp_img_src' , $fp_holder_img, $fp_single_id , $featured_page_id );

        }

        else {
            $featured_page_id               = apply_filters( 'tc_fp_id', esc_attr( TC_utils::$inst->tc_opt( 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );

            $featured_page_link             = apply_filters( 'tc_fp_link_url', get_permalink( $featured_page_id ), $fp_single_id );

            $featured_page_title            = apply_filters( 'tc_fp_title', get_the_title( $featured_page_id ), $fp_single_id, $featured_page_id );

            $edit_enabled                   = false;
            //when are we displaying the edit link?
            //never display when customizing
            if ( ! TC___::$instance -> tc_is_customizing() ) {
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : $edit_enabled;
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;
            }
            
            $edit_enabled                   = apply_filters( 'tc_edit_in_fp_title', $edit_enabled );

            $featured_text                  = apply_filters( 'tc_fp_text', TC_utils::$inst->tc_opt( 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
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
            $fp_custom_img_id               = apply_filters( 'fp_img_id', null , $fp_single_id , $featured_page_id );

            $fp_img = $this -> tc_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id);
            $fp_img = $fp_img ? $fp_img : $fp_holder_img;

            $fp_img                 = apply_filters ('fp_img_src' , $fp_img , $fp_single_id , $featured_page_id );
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
                                            get_edit_post_link($featured_page_id),
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
              $tc_fp_button_text = apply_filters( 'tc_fp_button_text' , esc_attr( TC_utils::$inst->tc_opt( 'tc_featured_page_button_text') ) , $fp_single_id );

              if ( $tc_fp_button_text || TC___::$instance -> tc_is_customizing() ){
                $tc_fp_button_class = apply_filters( 'tc_fp_button_class' , 'btn btn-primary fp-button', $fp_single_id );
                $tc_fp_button_class = $tc_fp_button_text ? $tc_fp_button_class : $tc_fp_button_class . ' hidden';
                $tc_fp_button_block = sprintf('<a class="%1$s" href="%2$s" title="%3$s">%4$s</a>',
                                    $tc_fp_button_class,
                                    $featured_page_link,
                                    $featured_page_title,
                                    $tc_fp_button_text

                );
                echo apply_filters( 'tc_fp_button_block' , $tc_fp_button_block , $featured_page_link , $featured_page_title , $fp_single_id, $featured_page_id );
              }
            ?>

          </div><!-- /.widget-front -->

          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters( 'tc_fp_single_display' , $html, $fp_single_id, $show_img, $fp_img, $featured_page_link, $featured_page_title, $text, $featured_page_id );
      }//end of function



    /******************************
    * HELPERS
    *******************************/
    function tc_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id ){
      //try to get "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
      //tc_get_thumbnail_model( $requested_size = null, $_post_id = null , $_thumb_id = null )
      $_fp_img_model = TC_post_thumbnails::$instance -> tc_get_thumbnail_model( $fp_img_size, $featured_page_id, $fp_custom_img_id );

      //finally we define a default holder if no thumbnail found or page is protected
      if ( isset( $_fp_img_model["tc_thumb"]) && ! empty( $_fp_img_model["tc_thumb"] ) && ! post_password_required( $featured_page_id ) )
        $fp_img = $_fp_img_model["tc_thumb"];
      else
        $fp_img = false;

      return $fp_img;
    }


    function tc_show_featured_pages() {
      //gets display fp option
      $tc_show_featured_pages 	      = esc_attr( TC_utils::$inst->tc_opt( 'tc_show_featured_pages' ) );

      return apply_filters( 'tc_show_fp', 0 != $tc_show_featured_pages && tc__f('__is_home') );
    }


    function tc_show_featured_pages_img() {
      //gets  display img option
      return apply_filters( 'tc_show_featured_pages_img', esc_attr( TC_utils::$inst->tc_opt( 'tc_show_featured_pages_img' ) ) );
    }

  }//end of class
endif;
