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

class TC_featured_pages {

    function __construct () {
        add_action  ( '__fp_block'               , array( $this , 'tc_display_fp_block' ));
        add_action  ( '__fp_single'              , array( $this , 'tc_display_fp_single' ), 10, 2);
    }



    /**
	 * The template displaying the front page featured page block.
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
    function tc_display_fp_block() {
    		//global customizr options array
    		$__options              = tc__f ( '__options' );

    		//get display options
    		$tc_show_featured_pages 	     = esc_attr( $__options['tc_show_featured_pages']);
    		$tc_show_featured_pages_img    = esc_attr( $__options['tc_show_featured_pages_img']);

    		//set the areas array
    		$areas = array ( 'one' , 'two' , 'three' );

    		?>

    		<?php if ( $tc_show_featured_pages  != 0 && (is_front_page())) : ?>

    			<div class="container marketing">

    				<div class="row widget-area" role="complementary">

    					<?php foreach ( $areas as $area) : ?>

    						<div class="span4">
    							<?php 
    								if ( !empty( $__options['tc_featured_page_'.$area] ) )  {
    									do_action(
    										'__fp_single' , 
    										$area,	
    										$tc_show_featured_pages_img
    										);
    								}
    								else {
    									do_action(
    										'__fp_single' , 
    										'not-set' ,	
    										$tc_show_featured_pages_img
    										);
    								}
    							 ?>
    						</div><!-- .span4 -->

    					<?php endforeach; ?>

    				</div><!-- .row widget-area -->

    			</div><!-- .container -->

    			<hr class="featurette-divider">

    		<?php endif; ?>
    	<?php
	 }





	/**
      * The template displaying one single featured page
      *
      * @package Customizr
      * @since Customizr 3.0
      * @param area are defined in featured-pages templates,show_img is a customizer option
      * @todo better area definition : dynamic
      */
      function tc_display_fp_single( $area,$show_img) {
        switch ( $area) {
          case 'not-set':
              //admin link if user logged in
              $featured_page_link           = '';
              $admin_link                   = '';
              if (is_user_logged_in()) {
              $featured_page_link           = admin_url().'customize.php';
              $admin_link                   = '<a href="'.admin_url().'customize.php" title="'.__( 'Customizer screen' , 'customizr' ).'">'.__( ' here' , 'customizr' ).'</a>';
              }

              //rendering
              $featured_page_id             =  null;
              $featured_page_title          =  __( 'Featured page' , 'customizr' );
              $text                         =  sprintf(__( 'Featured page description text : use the page excerpt or set your own custom text in the Customizr screen%s.' , 'customizr' ),
              $admin_link 
                );
              $tc_thumb                     =  '<img data-src="holder.js/270x250" alt="Holder Thumbnail">';

            break;
          


          default://for areas one, two, three
              //get saved options
              $__options                    = tc__f ( '__options' );
              $featured_page_id             = esc_attr( $__options['tc_featured_page_'.$area]);
              $featured_page_link           = get_permalink( $featured_page_id );
              $featured_page_title          = get_the_title( $featured_page_id );
              $featured_text                = esc_attr( $__options['tc_featured_text_'.$area] );

              //get the page/post object
              $page                         =  get_post( $featured_page_id);
              
              //limit text to 200 car
              $text                         = strip_tags( $featured_text);
              if (empty( $text)) {
                $text                       = strip_tags( $page->post_content);
              }
              if (strlen( $text) > 200) {
                $text                       = substr( $text,0,strpos( $text, ' ' ,200));
                $text                       = esc_html( $text) . ' ...';
              }
              else {
                $text                       = esc_textarea( $text );
              }
              
              
            //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
            $tc_thumb_size                  = 'tc-thumb';

             if (has_post_thumbnail( $featured_page_id)) {
                  $tc_thumb_id              = get_post_thumbnail_id( $featured_page_id);

                  //check if tc-thumb size exists for attachment and return large if not
                  $image = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);
                  if (null == $image[3])
                    $tc_thumb_size          = 'medium';

                  $tc_thumb                 = get_the_post_thumbnail( $featured_page_id,$tc_thumb_size);
                  //get height and width
                  $tc_thumb_height          = $image[2];
                  $tc_thumb_width           = $image[1];
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

                    $attachments            = get_posts( $tc_args);

                    if ( $attachments) {

                        foreach ( $attachments as $attachment) {
                           //check if tc-thumb size exists for attachment and return large if not
                          $image            = wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
                          if (false == $image[3]) {
                            $tc_thumb_size  = 'medium';
                          }
                          $tc_thumb         = wp_get_attachment_image( $attachment->ID, $tc_thumb_size);
                          //get height and width
                          $tc_thumb_height  = $image[2];
                          $tc_thumb_width   = $image[1];
                        }//end foreach

                    }//end if

              }//end else

              if (!isset( $tc_thumb)) {
                $tc_thumb                   = '<img data-src="holder.js/270x250" alt="Holder Thumbnail" />';
              }

            break;
          }//end switch

          //Rendering
          ?>
            <div class="widget-front">
              <?php if ( isset( $show_img) && $show_img == 1) : //check if image option is checked ?>
                  <div class="thumb-wrapper <?php if(!isset( $tc_thumb)) {echo 'tc-holder';} ?>">
                      <a class="round-div" href="<?php echo $featured_page_link ?>" title="<?php echo $featured_page_title ?>"></a>
                        <?php echo $tc_thumb; ?>
                  </div>
              <?php endif; ?>
                <h2><?php echo $featured_page_title ?></h2>
                <p class="fp-text-<?php echo $area ?>"><?php echo $text;  ?></p>
                <p><a class="btn btn-primary" href="<?php echo $featured_page_link ?>" title="<?php echo $featured_page_title ?>"><?php _e( 'Read more &raquo;' , 'customizr' ) ?></a></p>
            </div><!-- /.widget-front -->
          
          <?php
      }//end of function

 }//end of class