<?php
/***************************************************
* AUGMENTS WP CUSTOMIZE SETTINGS
***************************************************/
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

            /**
             * Filter a Customize setting value not handled as a theme_mod or option.
             *
             * The dynamic portion of the hook name, `$this->id_date['base']`, refers to
             * the base slug of the setting name.
             *
             * For settings handled as theme_mods or options, see those corresponding
             * functions for available hooks.
             *
             * @since 3.4.0
             *
             * @param mixed $default The setting default value. Default empty.
             */
            return apply_filters( 'customize_value_' . $this->id_data[ 'base' ], $this->default );
        }

        // Handle non-array value
        if ( empty( $this->id_data[ 'keys' ] ) )
          return $function( $this->id_data[ 'base' ], $this->default );

        // Handle array-based value
        $values = $function( $this->id_data[ 'base' ] );

        //Ctx future backward compat
        $_maybe_array = $this->multidimensional_get( $values, $this->id_data[ 'keys' ], $this->default );
        if ( ! is_array( $_maybe_array ) )
          return $_maybe_array;
        if ( isset($_maybe_array['all_ctx']) )
          return $_maybe_array['all_ctx'];
        if ( isset($_maybe_array['all_ctx_over']) )
          return $_maybe_array['all_ctx_over'];

        return $_maybe_array;
        //$this->default;
      }
  }
endif;


/***************************************************
* AUGMENTS WP CUSTOMIZE CONTROLS
***************************************************/
/**
* Add controls to customizer
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_controls' ) ) :
	class TC_controls extends WP_Customize_Control	{
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

	    public function render_content()  {
	    	do_action( '__before_setting_control' , $this -> id );

        switch ( $this -> type) {
	        	case 'hr':
	        		echo '<hr class="tc-customizer-separator" />';
	        	break;


	        	case 'title' :
  	        	?>
  	        	<?php if (isset( $this->title)) : ?>
  						<h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php if (isset( $this->notice)) : ?>
  						<i class="tc-notice"><?php echo $this -> notice ?></i>
  					 <?php endif; ?>

  					<?php
  					break;


	        	case 'select':
    					if ( empty( $this->choices ) )
    						return;
    					?>
    					<?php if (!empty( $this->title)) : ?>
    						<h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
    					<?php endif; ?>
    					<label>
    						<span class="customize-control-title"><?php echo $this->label; ?></span>
    						<?php $this -> tc_print_select_control( in_array( $this->id, array( 'tc_theme_options[tc_fonts]', 'tc_theme_options[tc_skin]' ) ) ? 'select2' : '' ) ?>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="tc-notice"><?php echo $this -> notice ?></span>
                <?php endif; ?>
    					</label>
    					<?php
    					if ( 'tc_theme_options[tc_front_slider]' == $this -> id ) {
                //retrieve all sliders in option array
                $options          = get_option( 'tc_theme_options' );
                $sliders          = array();
                if ( isset( $options['tc_sliders'])) {
                  $sliders        = $options['tc_sliders'];
                }
                if ( empty( $sliders ) ) {
      						printf('<div class="tc-notice" style="width:99%; padding: 5px;"><p class="description">%1$s<br/><a class="button-primary" href="%2$s" target="_blank">%3$s</a><br/><span class="tc-notice">%4$s <a href="%5$s" title="%6$s" target="_blank">%6$s</a></span></p>',
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
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
	        		<label>
	        			<span class="tc-number-label customize-control-title"><?php echo $this->label ?></span>
		        		<input <?php $this->link() ?> type="number" step="<?php echo $this-> step ?>" min="<?php echo $this-> min ?>" id="posts_per_page" value="<?php echo $this->value() ?>" class="tc-number-input small-text">
		        		<?php if(!empty( $this -> notice)) : ?>
			        		<span class="tc-notice"><?php echo $this-> notice ?></span>
			        	<?php endif; ?>
		        	</label>
		        	<?php
	        		break;

	        	case 'checkbox':
	        		?>
		        	<?php if (isset( $this->title)) : ?>
    						<h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
    					<?php endif; ?>
    					<?php
    		        		printf('<div class="tc-check-label"><label><span class="customize-control-title">%1$s</span></label></div>',
    		        		$this->label
    		        	);
    					?>
    					<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />

    					<?php if(!empty( $this -> notice)) : ?>
    					 <span class="tc-notice"><?php echo $this-> notice ?></span>
    					<?php endif; ?>
    					<?php
    				break;

	        	case 'textarea':
	        		?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
    					<label>
    						<span class="customize-control-title"><?php echo $this->label; ?></span>
    						<?php if(!empty( $this -> notice)) : ?>
    							<span class="tc-notice"><?php echo $this-> notice; ?></span>
    						<?php endif; ?>
    						<textarea class="widefat" rows="3" cols="10" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
    					</label>
    					<?php
		        	break;

	        	case 'url':
	        	case 'email':
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php
	        		printf('<label><span class="customize-control-title %1$s">%2$s</span><input type="text" value="%3$s" %4$s /></label>',
	        			! empty( $this -> icon) ? $this -> icon : '',
	        			$this->label,
	        			call_user_func( array( TC_utils_settings_map::$instance, 'tc_sanitize_' . $this -> type), $this->value() ),
	        			call_user_func( array( $this, 'get'.'_'.'link' ) )
	        		);
		        	break;

	        	default:
	        		global $wp_version;
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
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
    							<span class="tc-notice"><?php echo $this-> notice; ?></span>
    						<?php endif; ?>
    					</label>
    					<?php
    				break;
	        }//end switch
	        do_action( '__after_setting_control' , $this -> id );
		 }//end function




    private function tc_print_select_control($class) {
      printf('<select %1$s class="%2$s">%3$s</select>',
        call_user_func( array( $this, 'get'.'_'.'link' ) ),
        $class,
        $this -> tc_get_select_options()
      );
    }


    private function tc_get_select_options() {
      $_options_html = '';
      switch ( $this -> id ) {
        case 'tc_theme_options[tc_fonts]':
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

        case 'tc_theme_options[tc_skin]':
          $_data_hex  = '';
          $_color_map = TC_utils::$inst -> tc_get_skin_color( 'all' );
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

	}//end of class
endif;



/*
 * @since 3.4.19
 * @package      Customizr
*/
if ( class_exists('WP_Customize_Cropped_Image_Control') && ! class_exists( 'TC_Customize_Cropped_Image_Control' ) ) :
  class TC_Customize_Cropped_Image_Control extends WP_Customize_Cropped_Image_Control {
    public $type = 'tc_cropped_image';
    public $title;
    public $notice;
    public $dst_width;
    public $dst_height;


    /**
    * Refresh the parameters passed to the JavaScript via JSON.
    *
    * @since 3.4.19
    * @package      Customizr
    *
    * @Override
    * @see WP_Customize_Control::to_json()
    */
    public function to_json() {
        parent::to_json();
        $this->json['title']  = !empty( $this -> title )  ? esc_html( $this -> title ) : '';
        $this->json['notice'] = !empty( $this -> notice ) ?           $this -> notice  : '';

        $this->json['dst_width']  = isset( $this -> dst_width )  ?  $this -> dst_width  : $this -> width;
        $this->json['dst_height'] = isset( $this -> dst_height ) ?  $this -> dst_height : $this -> height;
        //overload WP_Customize_Upload_Control
        //we need to re-build the absolute url of the logo src set in old Customizr
        $value = $this->value();
        if ( $value ) {
          //re-build the absolute url if the value isn't an attachment id before retrieving the id
          if ( (int) esc_attr( $value ) < 1 ) {
            $upload_dir = wp_upload_dir();
            $value  = false !== strpos( $value , '/wp-content/' ) ? $value : $upload_dir['baseurl'] . $value;
          }
          // Get the attachment model for the existing file.
          $attachment_id = attachment_url_to_postid( $value );
          if ( $attachment_id ) {
              $this->json['attachment'] = wp_prepare_attachment_for_js( $attachment_id );
      }
      }//end overload
    }

    /**
  * Render a JS template for the content of the media control.
  *
  * @since 3.4.19
    * @package      Customizr
    *
    * @Override
  * @see WP_Customize_Control::content_template()
  */
    public function content_template() {
    ?>
    <# if ( data.title ) { #>
        <h3 class="tc-customizr-title">{{{ data.title }}}</h3>
      <# } #>
        <?php parent::content_template(); ?>
      <# if ( data.notice ) { #>
        <span class="tc-notice">{{{ data.notice }}}</span>
      <# } #>
    <?php
    }
  }//end class
endif;


/**************************************************************************************************
* MULTIPICKER CLASSES
***************************************************************************************************/
if ( ! class_exists( 'TC_Customize_Multipicker_Control' ) ) :
  /**
  * Customize Multi-picker Control Class
  *
  * @package WordPress
  * @subpackage Customize
  * @since 3.4.10
  */
  abstract class TC_Customize_Multipicker_Control extends TC_controls {

    public function render_content() {

      if ( ! $this -> type ) return;
      do_action( '__before_setting_control' , $this -> id );

      $dropdown = $this -> tc_get_dropdown_multipicker();

      if ( empty( $dropdown ) ) return;

      $dropdown = str_replace( '<select', '<select multiple="multiple"' . $this->get_link(), $dropdown );
      //start rendering
      if (!empty( $this->title)) :
    ?>
        <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>

      <label>
        <span class="customize-control-title"><?php echo $this->label; ?></span>
        <?php echo $dropdown; ?>
        <?php if(!empty( $this -> notice)) : ?>
          <span class="tc-notice"><?php echo $this -> notice ?></span>
         <?php endif; ?>
      </label>
    <?php
      do_action( '__after_setting_control' , $this -> id );
    }

    //to define in the extended classes
    abstract public function tc_get_dropdown_multipicker();
  }//end class
endif;

if ( ! class_exists( 'TC_Customize_Multipicker_Categories_Control' ) ) :
  class TC_Customize_Multipicker_Categories_Control extends TC_Customize_Multipicker_Control {

    public function tc_get_dropdown_multipicker() {
      $cats_dropdown = wp_dropdown_categories(
          array(
              'name'               => '_customize-'.$this->type,
              'id'                 => $this -> id,
              //hide empty, set it to false to avoid complains
              'hide_empty'         => 0 ,
              'echo'               => 0 ,
              'walker'             => new TC_Walker_CategoryDropdown_Multipicker(),
              'hierarchical'       => 1,
              'class'              => 'select2 '.$this->type,
              'selected'           => implode(',', $this->value() )
          )
      );

      return $cats_dropdown;
    }
  }
endif;


/**
 * @ dropdown multi-select walker
 * Create HTML dropdown list of Categories.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 *
 * we need to allow more than one "selected" attribute
 */

if ( ! class_exists( 'TC_Walker_CategoryDropdown_Multipicker' ) ) :
  class TC_Walker_CategoryDropdown_Multipicker extends Walker_CategoryDropdown {
    /**
     * Start the element output.
     *
     * @Override
     *
     * @see Walker::start_el()
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category Category data object.
     * @param int    $depth    Depth of category. Used for padding.
     * @param array  $args     Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
     *                         See {@see wp_dropdown_categories()}.
     */
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
      $pad = str_repeat('&mdash;', $depth );
      /** This filter is documented in wp-includes/category-template.php */
      $cat_name = apply_filters( 'list_cats', $category->name, $category );

      $value_field = 'term_id';

      $output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$value_field} ) . "\"";
      //Treat selected arg as array
      if ( in_array( (string) $category->{$value_field}, explode( ',', $args['selected'] ) ) )
        $output .= ' selected="selected"';

      $output .= '>';
      $output .= $pad.$cat_name;
      if ( $args['show_count'] )
        $output .= '&nbsp;&nbsp;('. number_format_i18n( $category->count ) .')';
      $output .= "</option>\n";
    }
  }
endif;
/**************************************************************************************************
* END OF MULTIPICKER CLASSES
***************************************************************************************************/












/*********************************************************************************
* Old upload control used until v3.4.18, still used if current version of WP is < 4.3
**********************************************************************************/

if ( ! class_exists( 'TC_Customize_Upload_Control' ) ) :
  /**
   * Customize Upload Control Class
   *
   * @package WordPress
   * @subpackage Customize
   * @since 3.4.0
   */
  class TC_Customize_Upload_Control extends WP_Customize_Control {
    public $type    = 'tc_upload';
    public $removed = '';
    public $context;
    public $extensions = array();
    public $title;
    public $notice;

    /**
     * Enqueue control related scripts/styles.
     *
     * @since 3.4.0
     */
    public function enqueue() {
      wp_enqueue_script( 'wp-plupload' );
    }

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @since 3.4.0
     * @uses WP_Customize_Control::to_json()
     */
    public function to_json() {
      parent::to_json();

      $this->json['removed'] = $this->removed;

      if ( $this->context )
        $this->json['context'] = $this->context;

      if ( $this->extensions )
        $this->json['extensions'] = implode( ',', $this->extensions );
    }

    /**
     * Render the control's content.
     *
     * @since 3.4.0
     */
  public function render_content() {
      do_action( '__before_setting_control' , $this -> id );
      ?>
      <?php if ( isset( $this->title) ) : ?>
        <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>
      <label>
        <?php if ( ! empty( $this->label ) ) : ?>
          <span class="customize-control-title"><?php echo $this->label; ?></span>
        <?php endif;
        if ( ! empty( $this->description ) ) : ?>
          <span class="description customize-control-description"><?php echo $this->description; ?></span>
        <?php endif; ?>
        <div>
          <a href="#" class="button-secondary tc-upload"><?php _e( 'Upload' , 'customizr'  ); ?></a>
          <a href="#" class="remove"><?php _e( 'Remove' , 'customizr'  ); ?></a>
        </div>
        <?php if(!empty( $this -> notice)) : ?>
          <span class="tc-notice"><?php echo $this -> notice; ?></span>
        <?php endif; ?>
      </label>
      <?php
      do_action( '__after_setting_control' , $this -> id );
    }
  }
endif;
