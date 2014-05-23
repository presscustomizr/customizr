<?php
/**
* Sidebar actions
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

class TC_sidebar {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;
        //displays left sidebar
        add_action  ( '__before_article_container'            , array( $this , 'tc_sidebar_display' ) );
        add_action  ( '__before_left_sidebar'                 , array( $this , 'tc_social_in_sidebar' ) );

        //displays right sidebar
        add_action  ( '__after_article_container'             , array( $this , 'tc_sidebar_display' ) );
        add_action  ( '__before_right_sidebar'                , array( $this , 'tc_social_in_sidebar' ) );
    }


    /**
    * Returns the sidebar or the front page featured pages area
    * @param Name of the widgetized area
    * @package Customizr
    * @since Customizr 1.0 
    */
    function tc_sidebar_display() {
      //first check if home and no content option is choosen
      if (tc__f( '__is_home_empty'))
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      //get layout options
      $sidebar          = tc__f( '__screen_layout' , tc__f( '__ID' ) , 'sidebar'  );
      //get position from current hook
      $position         = ( false != strpos(current_filter(), 'before') ) ? 'left' : 'right';

      ob_start();

          switch ( $position) {
            case 'left':
              //check if options are set to left or both sidebar
              if( $sidebar != 'l' && $sidebar != 'b' ) {
                return;
              }
              ?>
               
              <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
                <div class="span3 left tc-sidebar">
                  <div id="left" class="widget-area" role="complementary">
                    <?php if ( is_active_sidebar( 'left' ) ) : ?>
                      <?php do_action( '__before_left_sidebar' );##hook of social icons ?>

                        <?php dynamic_sidebar( 'left' ); ?>

                      <?php do_action( '__after_left_sidebar' ); ?>
                     <?php endif; ?>
                  </div><!-- #left -->
                </div><!--.tc-sidebar .span3 -->
               
              <?php
            break;


            case 'right':
              //check if options are set to right or both sidebar
              if( $sidebar != 'r' && $sidebar != 'b' ) {
                return;
              }
              ?>
               
              <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
                <div class="span3 right tc-sidebar">
                  <div id="right" class="widget-area" role="complementary">
                     <?php if ( is_active_sidebar( 'right' ) ) : ?>
                        <?php do_action( '__before_right_sidebar' );##hook of social icons ?>

                          <?php dynamic_sidebar( 'right' ); ?>

                        <?php do_action( '__after_right_sidebar' ); ?>
                      <?php endif; ?>
                  </div><!-- #right -->
                </div><!--.tc-sidebar .span3 -->
                
              <?php
            break;
          }//end switch

        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_sidebar_display', $html );
    }//end of function





    /**
    * Displays the social networks in sidebars
    * 
    * @package Customizr
    * @since Customizr 1.0 
    */
    function tc_social_in_sidebar() {
      $__options          = tc__f( '__options' );

      //get option from current hook
      $option               = ( false != strpos(current_filter(), 'left') ) ? 'tc_social_in_left-sidebar' : 'tc_social_in_right-sidebar';

      //returns if option not set
      if( $__options[$option] == 0)
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      ob_start();
      ?>
      
        <aside class="social-block widget widget_social">
          <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
          <h3 class="widget-title"><?php _e( 'Social links' , 'customizr' ) ?></h3>
          <?php echo tc__f( '__get_socials' ) ?>
        </aside>

      <?php
      $html = ob_get_contents();
      ob_end_clean();
      echo apply_filters( 'tc_social_in_sidebar', $html );
    }

 }//end of class
