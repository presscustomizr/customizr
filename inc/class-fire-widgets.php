<?php
/**
* Widgets factory
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

class TC_widgets {
  
      function __construct () {
          add_action( 'widgets_init'                         , array( $this , 'tc_widgets_factory' ));
      }


      /**
      * 
      * Registers the widget areas
      * @package Customizr
      * @since Customizr 3.0 
      */
      function tc_widgets_factory() {
          $tc_widgets = array(
                      'right'         => array(
                                      'name'                 => __( 'Right Sidebar' , 'customizr' ),
                                      'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
                      ),
                      'left'          => array(
                                      'name'                 => __( 'Left Sidebar' , 'customizr' ),
                                      'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
                      ),
                      'footer_one'    => array(
                                      'name'                 => __( 'Footer Widget Area One' , 'customizr' ),
                                      'description'          => __( 'Just use it as you want !' , 'customizr' )
                      ),
                      'footer_two'    => array(
                                      'name'                 => __( 'Footer Widget Area Two' , 'customizr' ),
                                      'description'          => __( 'Just use it as you want !' , 'customizr' )
                      ),
                      'footer_three'   => array(
                                      'name'                 => __( 'Footer Widget Area Three' , 'customizr' ),
                                      'description'          => __( 'Just use it as you want !' , 'customizr' )
                      ),
          );

          foreach ( $tc_widgets as $id => $infos) {
              register_sidebar(   array(
                                  'name'                    => $infos['name'],
                                  'id'                      => $id,
                                  'description'             => $infos['description'],
                                  'before_widget'           => '<aside id="%1$s" class="widget %2$s">' ,
                                  'after_widget'            => '</aside>' ,
                                  'before_title'            => '<h3 class="widget-title">' ,
                                  'after_title'             => '</h3>' ,
              ));
          }
      }

}//end of class

