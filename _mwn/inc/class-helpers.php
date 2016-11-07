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
      if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) )
        return;
      ?>
        <pre><h1 style="color:red"><?php echo $message ?></h1></pre>
      <?php
    }


    //A callback helper
    //a callback can be function or a method of a class
    //the class can be an instance!
    public function tc_fire_cb( $cb, $params = array() ) {
      //method of a class => look for an array( 'class_name', 'method_name')
      if ( is_array($cb) && 2 == count($cb) ) {
        if ( is_object($cb[0]) ) {
          call_user_func_array( array( $cb[0] ,  $cb[1] ), $params );
        }
        //instanciated with an instance property holding the object ?
        else if ( class_exists($cb[0]) && isset($cb[0]::$instance) && method_exists($cb[0]::$instance, $cb[1]) ) {
          call_user_func_array( array( $cb[0]::$instance ,  $cb[1] ), $params );
        }
        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            call_user_func_array( array( $_class_obj, $cb[1] ), $params );
        }
      } else if ( is_string($cb) && function_exists($cb) ) {
        call_user_func_array($cb, $params);
      }
    }

    public function tc_return_cb_result( $cb, $params = array() ) {
      //method of a class => look for an array( 'class_name', 'method_name')
      if ( is_array($cb) && 2 == count($cb) ) {
        if ( is_object($cb[0]) ) {
          return call_user_func_array( array( $cb[0] ,  $cb[1] ), $params );
        }
        //instanciated with an instance property holding the object ?
        else if ( class_exists($cb[0]) && isset($cb[0]::$instance) && method_exists($cb[0]::$instance, $cb[1]) ) {
          return call_user_func_array( array( $cb[0]::$instance ,  $cb[1] ), $params );
        }
        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            return call_user_func_array( array( $_class_obj, $cb[1] ), $params );
        }
      } else if ( is_string($cb) && function_exists($cb) ) {
        return call_user_func_array($cb, $params);
      }
    }
  }//end of class
endif;