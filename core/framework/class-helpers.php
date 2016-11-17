<?php
if ( ! class_exists( 'CZR_Helpers' ) ) :
  class CZR_Helpers {
    static $instance;

    function __construct( $args = array() ) {
      self::$instance =& $this;

      add_action( 'czr_dev_notice', array( $this, 'czr_fn_print_r') );
    }


    //hook : czr_dev_notice
    function czr_fn_print_r($message) {
      if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) || is_feed() )
        return;
      ?>
        <pre><h6 style="color:red"><?php echo $message ?></h6></pre>
      <?php
    }

    function czr_fn_stringify_array( $array, $sep = ' ' ) {
      if ( is_array( $array ) )
        $array = join( $sep, array_unique( array_filter( $array ) ) );
      return $array;
    }

    //A callback helper
    //a callback can be function or a method of a class
    //the class can be an instance!
    public function czr_fn_fire_cb( $cb, $params = array(), $return = false ) {
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


    public function czr_fn_return_cb_result( $cb, $params = array() ) {
      return $this -> czr_fn_fire_cb( $cb, $params, $return = true );
    }




    /* Same as helpers above but passing the param argument as an exploded array of params*/
    //A callback helper
    //a callback can be function or a method of a class
    //the class can be an instance!
    public function czr_fn_fire_cb_array( $cb, $params = array(), $return = false ) {
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

    public function czr_fn_return_cb_result_array( $cb, $params = array() ) {
      return $this -> czr_fn_fire_cb_array( $cb, $params, $return = true );
    }
  }//end of class

endif;
