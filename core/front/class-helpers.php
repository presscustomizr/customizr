<?php
if ( ! class_exists( 'TC_Helpers' ) ) :
  class TC_Helpers {
    static $instance;

    function __construct( $args = array() ) {
      self::$instance =& $this;

      add_action( 'tc_dev_notice', array( $this, 'tc_print_r') );
    }


    //hook : tc_dev_notice
    function tc_print_r($message) {
      if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) || is_feed() )
        return;
      ?>
        <pre><h6 style="color:red"><?php echo $message ?></h6></pre>
      <?php
    }

    function tc_stringify_array( $array, $sep = ' ' ) {
      if ( is_array( $array ) )
        $array = join( $sep, array_unique( array_filter( $array ) ) );
      return $array;
    }

    //A callback helper
    //a callback can be function or a method of a class
    //the class can be an instance!
    public function tc_fire_cb( $cb, $params = array(), $return = false ) {
      $to_return = false;
      //method of a class => look for an array( 'class_name', 'method_name')
      if ( is_array($cb) && 2 == count($cb) ) {
        if ( is_object($cb[0]) ) {
          $to_return = call_user_func( array( $cb[0] ,  $cb[1] ), $params );
        }
        //instantiated with an instance property holding the object ?
        else if ( class_exists($cb[0]) && isset($cb[0]::$instance) && method_exists($cb[0]::$instance, $cb[1]) ) {
          $to_return = call_user_func( array( $cb[0]::$instance ,  $cb[1] ), $params );
        }
        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            $to_return = call_user_func( array( $_class_obj, $cb[1] ), $params );
        }
      } else if ( is_string($cb) && function_exists($cb) ) {
        $to_return = call_user_func($cb, $params);
      }

      if ( $return )
        return $to_return;
    }


    public function tc_return_cb_result( $cb, $params = array() ) {
      return $this -> tc_fire_cb( $cb, $params, $return = true );
    }




    /* Same as helpers above but passing the param argument as an exploded array of params*/
    //A callback helper
    //a callback can be function or a method of a class
    //the class can be an instance!
    public function tc_fire_cb_array( $cb, $params = array(), $return = false ) {
      $to_return = false;
      //method of a class => look for an array( 'class_name', 'method_name')
      if ( is_array($cb) && 2 == count($cb) ) {
        if ( is_object($cb[0]) ) {
          $to_return = call_user_func_array( array( $cb[0] ,  $cb[1] ), $params );
        }
        //instantiated with an instance property holding the object ?
        else if ( class_exists($cb[0]) && isset($cb[0]::$instance) && method_exists($cb[0]::$instance, $cb[1]) ) {
          $to_return = call_user_func_array( array( $cb[0]::$instance ,  $cb[1] ), $params );
        }
        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            $to_return = call_user_func_array( array( $_class_obj, $cb[1] ), $params );
        }
      } else if ( is_string($cb) && function_exists($cb) ) {
        $to_return = call_user_func_array($cb, $params);
      }

      if ( $return )
        return $to_return;
    }


    public function tc_return_cb_result_array( $cb, $params = array() ) {
      return $this -> tc_fire_cb_array( $cb, $params, $return = true );
    }


    /**
    * Returns or displays the selectors of the article depending on the context
    *
    * @package Customizr
    * @since 3.1.0
    */
    function tc_get_the_article_selectors($post_class = '') {
      //gets global vars
      global $post;
      global $wp_query;

      //declares selector var
      $selectors                  = '';

      if ( empty( $post_class ) )
        $post_class  = $this -> tc_get_the_post_class( $post_class );

      // POST LIST
      $post_list_selector_bool    = ( isset($post) && !is_singular() && !is_404() && !tc__f( '__is_home_empty') ) || ( is_search() && 0 != $wp_query -> post_count );
      $selectors                  = $post_list_selector_bool ? apply_filters( 'tc_post_list_selectors' , 'id="post-'.get_the_ID().'" '. $post_class ) : $selectors;

      $selectors = apply_filters( 'tc_article_selectors', $selectors );

      return $selectors;
    }//end of function



    /**
    * Returns the classes for the post div.
    *
    * @param string|array $class One or more classes to add to the class list.
    * @param int $post_id An optional post ID.
    * @package Customizr
    * @since 3.0.10
    */
    function tc_get_the_post_class( $class = '', $post_id = null ) {
      //Separates classes with a single space, collates classes for post DIV
      return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
    }



  }//end of class

endif;
