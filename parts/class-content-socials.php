<?php
/**
* Social networks content actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0.10
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_socials {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        add_action  ( '__social'                        , array( $this , 'tc_social_display' ));

    }


    /**
      * Displays the social networks in header, sidebars and footer
      * 
      * @package Customizr
      * @since Customizr 1.0 
      */
      function tc_social_display( $pos) {

        $__options          = tc__f( '__options' );

        if( $__options[$pos] == 0)
          return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        $socials = array (
              'tc_rss'            => 'feed',
              'tc_twitter'        => 'twitter',
              'tc_facebook'       => 'facebook',
              'tc_google'         => 'google',
              'tc_instagram'      => 'instagram',
              'tc_wordpress'      => 'wordpress',
              'tc_youtube'        => 'youtube',
              'tc_pinterest'      => 'pinterest',
              'tc_github'         => 'github',
              'tc_dribbble'       => 'dribbble',
              'tc_linkedin'       => 'linkedin'
              );
          
          $html = '';
          $html .= tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ );

          //check if sidebar option is checked
          if (preg_match( '/left|right/' , $pos)) {
            $html = '<h3 class="widget-title">'.__( 'Social links' , 'customizr' ).'</h3>';
          }
          //$html .= '<ul>';
            foreach ( $socials as $key => $nw) {
              //all cases except rss
              $title = __( 'Follow me on ' , 'customizr' ).$nw;
              $target = 'target=_blank';
              //rss case
              if ( $key == 'tc_rss' ) {
                $title = __( 'Suscribe to my rss feed' , 'customizr' );
                $target = '';
              }

              if ( $__options[$key] != '' ) {
                //$html .= '<li>';
                  $html .= '<a class="social-icon icon-'.$nw.'" href="'.esc_url( $__options[$key]).'" title="'.$title.'" '.$target.'></a>';
              }
           }
          //$html .= '</li></ul>';

        echo apply_filters( 'tc_social_display', $html );
      }

}//end of class