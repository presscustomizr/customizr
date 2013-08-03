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

    function __construct () {
        add_action  ( '__sidebar'                      , array( $this , 'tc_get_sidebar' ));
    }


    /**
    * Returns the sidebar or the front page featured pages area
    * @param Name of the widgetized area
    * @package Customizr
    * @since Customizr 1.0 
    */
    function tc_get_sidebar( $name) {
    
    //get layout options
    $sidebar            		    = tc__f( '__screen_layout' , tc__f( '__ID' ) , 'sidebar'  );
    $class              		    = tc__f( '__screen_layout' , tc__f( '__ID' ) , 'class'  );;
  
      switch ( $name) {
        case 'left':
          if( $sidebar == 'l' || $sidebar == 'b' ) {
            ?>
            <div class="span3 left tc-sidebar">

              <?php get_sidebar( $name); ?>

            </div>
            <?php
          }
        break;


        case 'right':
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
    }

 }//end of class
