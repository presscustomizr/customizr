<?php
/**
* Sidebar action
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

        add_action  ( '__sidebar'                      , array( $this , 'tc_get_sidebar' ));
    }


    /**
    * Returns the sidebar or the front page featured pages area
    * @param Name of the widgetized area
    * @package Customizr
    * @since Customizr 1.0 
    */
    function tc_get_sidebar( $name) {

    tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

    //get layout options
    $sidebar            		    = tc__f( '__screen_layout' , tc__f( '__ID' ) , 'sidebar'  );
    $class              		    = tc__f( '__screen_layout' , tc__f( '__ID' ) , 'class'  );;
    
    ob_start();

    tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ );
    
      switch ( $name) {
        case 'left':
          //first check if home and no content option is choosen
          if (tc__f( '__is_home_empty')) {
            return;
          }
          if( $sidebar == 'l' || $sidebar == 'b' ) {
            ?>
            <div class="span3 left tc-sidebar">

              <?php get_sidebar( $name); ?>

            </div>
            <?php
          }
        break;


        case 'right':
         //first check if home and no content option is choosen
          if (tc__f( '__is_home_empty')) {
            return;
          }
          if( $sidebar == 'r' || $sidebar == 'b' ) {
            ?>
            <div class="span3 right tc-sidebar">

              <?php get_sidebar( $name); ?>
              
            </div>
            <?php
          }
        break;

        case 'footer':
              get_sidebar( $name);
        break;

      }
      $html = ob_get_contents();
      ob_end_clean();
      echo apply_filters( 'tc_get_sidebar', $html );
    }

 }//end of class
