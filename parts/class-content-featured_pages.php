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

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        add_action  ( '__before_main_container'     , array( $this , 'tc_fp_block_display'), 10 );
        add_action  ( '__fp_single'                 , array( $this , 'tc_fp_single_display' ), 10, 2);
    }



    /**
	 * The template displaying the front page featured page block.
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
    function tc_fp_block_display() {

    		//get display options
    		$tc_show_featured_pages 	     = esc_attr( tc__f( '__get_option' , 'tc_show_featured_pages' ) );
    		$tc_show_featured_pages_img    = esc_attr( tc__f( '__get_option' , 'tc_show_featured_pages_img' ) );

    		//set the areas array
    		$areas = array ( 'one' , 'two' , 'three' );

    		?>

    		<?php if ( $tc_show_featured_pages  != 0 && tc__f('__is_home')  ) : ?>

          <?php tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ ); ?>

          <?php ob_start(); ?>

    			<div class="container marketing">
            <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>

    				<div class="row widget-area" role="complementary">

    					<?php foreach ( $areas as $area) : ?>

    						<div class="span4">
    							<?php do_action('__fp_single' , $area, $tc_show_featured_pages_img);?>
    						</div><!-- .span4 -->

    					<?php endforeach; ?>

    				</div><!-- .row widget-area -->

    			</div><!-- .container -->
          <?php if ( !tc__f( '__is_home_empty')) : ?>
    			   <hr class="featurette-divider">
          <?php endif; ?>

           <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo apply_filters( 'tc_fp_block_display' , $html );
            ?>

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
      function tc_fp_single_display( $area,$show_img) {

        //if not set
        if ( null == tc__f( '__get_option' , 'tc_featured_page_'.$area ) ) {
            //admin link if user logged in
            $featured_page_link             = '';
            $admin_link                     = '';
            if (is_user_logged_in()) {
            $featured_page_link             = admin_url().'customize.php';
            $admin_link                     = '<a href="'.admin_url().'customize.php" title="'.__( 'Customizer screen' , 'customizr' ).'">'.__( ' here' , 'customizr' ).'</a>';
            }

            //rendering
            $featured_page_id               =  null;
            $featured_page_title            =  __( 'Featured page' , 'customizr' );
            $text                           =  sprintf(__( 'Featured page description text : use the page excerpt or set your own custom text in the Customizr screen%s.' , 'customizr' ),
            $admin_link 
              );
            $tc_thumb                       =  '<img data-src="holder.js/270x250" alt="Holder Thumbnail">';

        }
          
        else {
              //get saved options
              $__options                    = tc__f( '__options' );
              $featured_page_id             = esc_attr( $__options['tc_featured_page_'.$area]);
              $featured_page_link           = get_permalink( $featured_page_id );
              $featured_page_title          = get_the_title( $featured_page_id );
              $featured_text                = strip_tags(html_entity_decode((esc_html( $__options['tc_featured_text_'.$area] ))));

              //get the page/post object
              $page                         =  get_post($featured_page_id);
              
              //limit text to 200 car
              $text                         = $featured_text ;
              if ( empty($text) && !post_password_required($featured_page_id) ) {
                $text                       = strip_tags(apply_filters( 'the_content' , $page->post_content ));
              }

              if ( strlen($text) > 200 ) {
                $text                       = substr( $text,0,strpos( $text, ' ' ,200));
                $text                       = $text . ' ...';
              }

              else {
                $text                       = $text;
              }
              
              
            //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
            $tc_thumb_size                  = 'tc-thumb';

             if ( has_post_thumbnail( $featured_page_id) ) {
                  $tc_thumb_id              = get_post_thumbnail_id( $featured_page_id);

                  //check if tc-thumb size exists for attachment and return large if not
                  $image = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);
                  if ( null == $image[3] )
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

              if (!isset( $tc_thumb) || post_password_required($featured_page_id) ) {
                $tc_thumb                   = '<img data-src="holder.js/270x250" alt="Holder Thumbnail" />';
              }
          }//end if

          //Rendering
          ob_start();
          ?>

          <div class="widget-front">
            <?php if ( isset( $show_img) && $show_img == 1 ) : //check if image option is checked ?>

                <div class="thumb-wrapper <?php if(!isset( $tc_thumb)) {echo 'tc-holder';} ?>">
                    <a class="round-div" href="<?php echo $featured_page_link ?>" title="<?php echo $featured_page_title ?>"></a>
                      <?php echo $tc_thumb; ?>
                </div>

            <?php endif; ?>

              <h2><?php echo $featured_page_title ?></h2>
              <p class="fp-text-<?php echo $area ?>"><?php echo $text;  ?></p>
              <p>
                 <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__, 'right'); ?>
                <a class="btn btn-primary fp-button" href="<?php echo $featured_page_link ?>" title="<?php echo $featured_page_title ?>">
                  <?php echo esc_attr( tc__f( '__get_option' , 'tc_featured_page_button_text') ) ?>
                </a>
              </p>

          </div><!-- /.widget-front -->
          
          <?php
          $html = ob_get_contents();
          ob_end_clean();
          echo apply_filters( 'tc_fp_single_display' , $html );
      }//end of function

 }//end of class