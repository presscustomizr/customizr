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
        add_action ( '__before_article_container'            , array( $this , 'tc_sidebar_display' ) );
        add_action ( '__before_left_sidebar'                 , array( $this , 'tc_social_in_sidebar' ) );

        //displays right sidebar
        add_action ( '__after_article_container'             , array( $this , 'tc_sidebar_display' ) );
        add_action ( '__before_right_sidebar'                , array( $this , 'tc_social_in_sidebar' ) );
    }




    /**
    * Returns the sidebar or the front page featured pages area
    * @param Name of the widgetized area
    * @package Customizr
    * @since Customizr 1.0 
    */
    function tc_sidebar_display() {
      //first check if home and no content option is choosen
      if ( tc__f( '__is_home_empty') )
        return;
      

      //gets current screen layout
      $screen_layout        = tc__f( '__screen_layout' , tc__f ( '__ID' ) , 'sidebar'  );

      //gets position from current hook and checks the context
      $position             = apply_filters( 
                              'tc_sidebar_position', 
                              strpos(current_filter(), 'before') ? 'left' : 'right'
      );
      if ( 'left' == $position && $screen_layout != 'l' && $screen_layout != 'b' )
        return;
      if ( 'right' == $position && $screen_layout != 'r' && $screen_layout != 'b' )
        return;

      //gets the global layout settings
      $global_layout        = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
      $sidebar_layout       = $global_layout[$screen_layout];

      //defines the sidebar wrapper class
      $class                = sprintf('%1$s %2$s tc-sidebar',
                            apply_filters( "tc_{$position}_sidebar_class", $sidebar_layout['sidebar'] ),
                            $position
      );
      ob_start();
      ?>
       
      <div class="<?php echo $class  ?>">
         <div id="<?php echo $position ?>" class="widget-area" role="complementary">
             <?php if ( is_active_sidebar( $position ) ) : ?>
                <?php do_action( "__before_{$position}_sidebar" );##hook of social icons ?>
                <?php get_sidebar( $position ) ?>
                <?php do_action( "__after_{$position}_sidebar" ); ?>
              <?php endif; ?>
          </div><!-- #left -->
      </div><!--.tc-sidebar -->

      <?php
      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_sidebar_display', $html, $sidebar_layout, $position );
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
      
      ob_start();
      ?>
      
        <aside class="social-block widget widget_social">
          <h3 class="widget-title"><?php _e( 'Social links' , 'customizr' ) ?></h3>
          <?php echo tc__f( '__get_socials' ) ?>
        </aside>

      <?php
      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_social_in_sidebar', $html );
    }

 }//end of class
