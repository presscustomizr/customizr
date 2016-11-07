<?php
class CZR_cl_recently_updated_model_class extends CZR_cl_Model {
  public $recently_updated_text;


  /*
  * @override
  */
  function czr_fn_maybe_render_this_model_view () {
        if ( ! $this -> visibility )
          return;

        //First checks if we are in the loop and we are not displaying a page
        if ( ! in_the_loop() || is_page() )
          return;

        //Is the notice option enabled (checked by the controller) AND this post type eligible for updated notice ? (default is post)
        if ( /* 0 == esc_attr( czr_fn_get_opt( 'tc_post_metas_update_notice_in_title' ) ) || */! in_array( get_post_type(), apply_filters('czr_show_update_notice_for_post_types' , array( 'post') ) ) )
          return;

        //php version check for DateTime
        //http://php.net/manual/fr/class.datetime.php
        if ( version_compare( PHP_VERSION, '5.2.0' ) < 0 )
          return;

        //get the user defined interval in days
        $_interval = esc_attr( czr_fn_get_opt( 'tc_post_metas_update_notice_interval' ) );

        $_interval = ( 0 != $_interval ) ? $_interval : 30;

        //Check if the last update is less than n days old. (replace n by your own value)
        $has_recent_update = ( czr_fn_post_has_update( true ) && czr_fn_post_has_update( 'in_days') < $_interval ) ? true : false;

        if ( ! $has_recent_update )
          return;

        return true;
  }



  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
        $recently_updated_text =  esc_attr( czr_fn_get_opt( 'tc_post_metas_update_notice_text' ) );
        $element_class         = esc_attr( czr_fn_get_opt( 'tc_post_metas_update_notice_format' ) );

        return array_merge( $model, compact( 'element_class', 'recently_updated_text' ) );
  }
}
