<?php
/**
* Add controls to customizer
*
*/
if ( ! class_exists( 'CZR_controls' ) ) :
  class CZR_controls extends WP_Customize_Control  {
      public $type;
      public $link;
      public $title;
      public $label;
      public $buttontext;
      public $settings;
      public $hr_after;
      public $notice;
      //number vars
      public $step;
      public $min;
      public $icon;

      public $ubq_section;

      static $enqueued_resources;

      public function render_content()  {

        do_action( '__before_setting_control' , $this -> id );

        switch ( $this->type) {
            case 'hr':
              echo '<hr class="czr-customizer-separator" />';
            break;


            case 'title' :
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php if (isset( $this->notice)) : ?>
              <i class="czr-notice"><?php echo $this -> notice ?></i>
             <?php endif; ?>

            <?php
            break;

            case 'select':
              if ( empty( $this->choices ) )
                return;
              ?>
              <?php if (!empty( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php $this -> czr_fn_print_select_control( in_array( $this->id, array( CZR_THEME_OPTIONS.'[tc_fonts]', CZR_THEME_OPTIONS.'[tc_skin]' ) ) ? 'czrSelect2 no-selecter-js' : '' ) ?>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="czr-notice"><?php echo $this -> notice ?></span>
                <?php endif; ?>
              </label>
              <?php
              if ( CZR_THEME_OPTIONS.'[tc_front_slider]' == $this -> id ) {
                //retrieve all sliders in option array
                $sliders          = czr_fn_opt( 'tc_sliders' );

                if ( empty( $sliders ) ) {
                  printf('<div class="czr-notice" style="padding: 5px;"><span class="czr-notice">%1$s<br/><a class="button-primary" href="%2$s" target="_blank">%3$s</a><br/><span class="tc-notice">%4$s <a href="%5$s" title="%6$s" target="_blank">%6$s</a></span></span>',
                    __("You haven't create any slider yet. Go to the media library, edit your images and add them to your sliders.", "customizr" ),
                    admin_url( 'upload.php?mode=list' ),
                    __( 'Create a slider' , 'customizr' ),
                    __( 'Need help to create a slider ?' , 'customizr' ),
                    esc_url( "http://docs.presscustomizr.com/article/3-creating-a-slider-with-customizr-wordpress-theme" ),
                    __( 'Check the documentation' , 'customizr' )
                  );
                }
              }
            break;


            case 'number':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="czr-number-label customize-control-title"><?php echo $this->label ?></span>
                <input <?php $this->link() ?> type="number" step="<?php echo $this-> step ?>" min="<?php echo $this-> min ?>" id="posts_per_page" value="<?php echo $this->value() ?>" class="czr-number-input small-text">
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="czr-notice"><?php echo $this-> notice ?></span>
                <?php endif; ?>
              </label>
              <?php
              break;

            case 'checkbox':
            case 'nimblecheck':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>

              <?php if ( 'checkbox' === $this->type ) : ?>
                <?php
                    printf('<div class="czr-check-label"><label><span class="customize-control-title">%1$s</span></label></div>',
                      $this->label
                    );
                ?>
                <input <?php $this->link(); ?> type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>"  <?php czr_fn_checked( $this->value() ); ?> />
              <?php elseif ( 'nimblecheck' === $this->type ) : ?>
                <div class="czr-control-nimblecheck">
                  <?php
                    printf('<div class="czr-check-label"><label><span class="customize-control-title">%1$s</span></label></div>',
                      $this->label
                    );
                  ?>
                  <div class="nimblecheck-wrap">
                    <input id="nimblecheck-<?php echo $this -> id; ?>" <?php $this->link(); ?> type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>"  <?php czr_fn_checked( $this->value() ); ?> class="nimblecheck-input">
                    <label for="nimblecheck-<?php echo $this -> id; ?>" class="nimblecheck-label">Switch</label>
                  </div>
                </div>
              <?php endif; ?>

              <?php if(!empty( $this -> notice)) : ?>
               <span class="czr-notice"><?php echo $this-> notice ?></span>
              <?php endif; ?>
              <?php
            break;

            case 'textarea':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="czr-notice"><?php echo $this-> notice; ?></span>
                <?php endif; ?>
                <textarea class="widefat" rows="3" cols="10" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
              </label>
              <?php
              break;

            case 'url':
            case 'email':
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php
              printf('<label><span class="customize-control-title %1$s">%2$s</span><input type="text" value="%3$s" %4$s /></label>',
                ! empty( $this -> icon) ? $this -> icon : '',
                $this->label,
                call_user_func( 'czr_fn_sanitize_' . $this -> type, $this->value() ),
                call_user_func( array( $this, 'get'.'_'.'link' ) )
              );
              break;


            default:
              global $wp_version;
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <?php if ( ! empty( $this->label ) ) : ?>
                  <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $this->description ) ) : ?>
                  <span class="description customize-control-description"><?php echo $this->description; ?></span>;;;
                <?php endif; ?>
                <?php if ( ! version_compare( $wp_version, '4.0', '>=' ) ) : ?>
                  <input type="<?php echo esc_attr( $this->type ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                <?php else : ?>
                  <input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                <?php endif; ?>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="czr-notice"><?php echo $this-> notice; ?></span>
                <?php endif; ?>
              </label>
              <?php
            break;
          }//end switch
          do_action( '__after_setting_control' , $this -> id );
     }//end function




    private function czr_fn_print_select_control($class) {
      printf('<select %1$s class="%2$s">%3$s</select>',
        call_user_func( array( $this, 'get'.'_'.'link' ) ),
        $class,
        $this -> czr_fn_get_select_options()
      );
    }


    private function czr_fn_get_select_options() {
      $_options_html = '';
      switch ( $this -> id ) {
        case CZR_THEME_OPTIONS.'[tc_fonts]':
          foreach ( $this -> choices as $_opt_group => $_opt_list ) {
            $_options = array();
            foreach ( $_opt_list['list'] as $label => $value ) {
              $_options[] = sprintf('<option value="%1$s" %2$s>%3$s</option>',
                esc_attr( $label ),
                selected( $this->value(), $value, false ),
                $value
              );
            }
            $_options_html .= sprintf('<optgroup label="%1$s">%2$s</optgroup>',
              $_opt_list['name'],
              implode($_options)
            );
          }
        break;

        case CZR_THEME_OPTIONS.'[tc_skin]':
          $_data_hex  = '';
          //only for czr3
          if ( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE )
            return;

          $_color_map = CZR_utils::$inst->czr_fn_get_skin_color( 'all' );
          //Get the color map array structured as follow
          // array(
          //       'blue.css'        =>  array( '#08c', '#005580' ),
          //       ...
          // )
          foreach ( $this->choices as $value => $label ) {
            if ( is_array($_color_map) && isset( $_color_map[esc_attr( $value )] ) )
              $_data_hex       = isset( $_color_map[esc_attr( $value )][0] ) ? $_color_map[esc_attr( $value )][0] : '';
            $_options_html .= sprintf('<option value="%1$s" %2$s data-hex="%4$s">%3$s</option>',
              esc_attr( $value ),
              selected( $this->value(), $value, false ),
              $label,
              $_data_hex
            );
          }
        break;
        default:
          foreach ( $this->choices as $value => $label ) {
            $_options_html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
              esc_attr( $value ),
              selected( $this->value(), $value, false ),
              $label
            );
          }
        break;
      }//end switch
      return $_options_html;
    }//end of fn


    /**
    * Enqueue scripts/styles
    * fired by the parent Control class constructor
    *
    */
    public function enqueue() {
        if ( ! empty( self::$enqueued_resources ) )
          return;

        self::$enqueued_resources = true;

        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
    }

    /**
    * Refresh the parameters passed to the JavaScript via JSON.
    *
    *
    * @Override
    * @see WP_Customize_Control::to_json()
    */
    public function to_json() {
      parent::to_json();
      if ( is_array( $this->ubq_section ) && array_key_exists( 'section', $this->ubq_section ) ) {
        $this->json['ubq_section'] = $this->ubq_section;
      }
    }
  }//end of class
endif;
?>