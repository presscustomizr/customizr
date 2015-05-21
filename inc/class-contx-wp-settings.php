<?php
if ( ! class_exists( 'TC_Customize_Setting') ) :
  class TC_Customize_Setting extends WP_Customize_Setting {

    /**
     * Fetch the value of the setting.
     *
     * @since 3.4.0
     *
     * @return mixed The value.
     */
    public function value() {
      // Get the callback that corresponds to the setting type.
      switch( $this->type ) {
        case 'theme_mod' :
          $function = 'get_theme_mod';
          break;
        case 'option' :
          $function = 'get_option';
          break;
        default :
          return apply_filters( 'customize_value_' . $this->id_data[ 'base' ], $this->default );
      }

      // Handle non-array value
      if ( empty( $this->id_data[ 'keys' ] ) )
        return $function( $this->id_data[ 'base' ], $this->default );

      $_base_values   = $function( $this->id_data[ 'base' ] );
      $_opt_values    = $this->multidimensional_get( $_base_values, $this->id_data[ 'keys' ], $this->default );
      //check option group
      // Handle simple array-based options.
      if ( TC___::$tc_option_group != $this->id_data[ 'base' ] )
        return $_opt_values;

      //handle contextualized array based options
      $_context       = TC_contx::$instance -> tc_get_context();

      if ( ! $_context || null == $_context )
        return isset($_opt_values['all_ctx']) ? $_opt_values['all_ctx'] : $_opt_values;

      if ( isset($_opt_values[$_context]) )
        return $_opt_values[$_context];
      else if ( isset($_opt_values['all_ctx']) )
        return $_opt_values['all_ctx'];
      else
        return $_opt_values;
    }




    /**
     * Update the option from the value of the setting.
     *
     * @since 3.4.0
     *
     * @param mixed $value The value to update.
     * @return bool|null The result of saving the value.
     */
    protected function _update_option( $value ) {
      $_keys = $this->id_data[ 'keys'];
      // Handle non-array option.
      if ( empty( $_keys ) )
        return update_option( $this->id_data[ 'base' ], $value );

      $_base_values   = get_option( $this->id_data[ 'base' ] );
      //check option group
      // Handle simple array-based options.
      if ( TC___::$tc_option_group != $this->id_data[ 'base' ] ) {
        $_base_values = $this->multidimensional_replace( $_base_values, $_keys, $value );
        if ( isset( $_base_values ) )
          return update_option( $this->id_data[ 'base' ], $_base_values );
      }

      //handle contextualized array based options
      if ( isset( $_keys[0]) ) {
        $_context       = TC_contx::$instance -> tc_get_context();
        $_opt_name      = $_keys[0];
        //make sure it's set and an array
        $_base_values[$_opt_name] = ( ! isset($_base_values[$_opt_name]) || ! is_array($_base_values[$_opt_name]) ) ? array() : $_base_values[$_opt_name];

        //if context exist
        if ( null != $_context )
          $_base_values[$_opt_name][$_context] = $value;
        else
          $_base_values[$_opt_name]['all_ctx'] = $value;

        //always make sure that the 'all_ctx' is present
        //$_base_values[$_opt_name]['all_ctx'] =  isset($_base_values[$_opt_name]['all_ctx']) ? $_base_values[$_opt_name]['all_ctx'] : $value;
        /*?>
          <pre>
            <?php print_r($_POST); ?>
          </pre>
        <?php*/

        return update_option( $this->id_data[ 'base' ], $_base_values );
      }
    }


    /**
     * Callback function to filter the theme mods and options.
     *
     * @since 3.4.0
     * @uses WP_Customize_Setting::multidimensional_replace()
     *
     * @param mixed $original Old value.
     * @return mixed New or old value.
     */
    public function _preview_filter( $original ) {
      ### TEST ###
      // if ( ! empty($this->id_data[ 'keys'] ) && in_array( 'tc_test_context' , $this->id_data[ 'keys'] ) ) {
      //   $_result = $this->multidimensional_replace( $original, $this->id_data[ 'keys' ], $this->post_value() );
      //   return TC_contx::$instance -> tc_get_context();
      // }

      return $this->multidimensional_replace( $original, $this->id_data[ 'keys' ], $this->post_value() );
    }

  }
endif;