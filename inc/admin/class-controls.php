<?php
/**
* Add controls to customizer
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
	    	do_action( '__before_setting_control' , $this-> id );

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


	        	case 'button':
	        		echo '<a class="button-primary" href="'.admin_url( $this -> link ).'" target="_blank">'.$this -> buttontext.'</a>';
	        		if ( $this -> hr_after == true)
	        			echo '<hr class="tc-after-button">';
	        		break;


	        	case 'select':
    					if ( empty( $this->choices ) )
    						return;
    					?>
    					<?php if (!empty( $this->title)) : ?>
    						<h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
    					<?php endif; ?>
    					<label>
    						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
    						<select <?php $this->link(); ?>>
    							<?php
    								//IF SKIN, THEN DEFINE SOME VARS
    								$_data_hex 	= '';
    								$_color_map = array();
    								if ( 'tc_theme_options[tc_skin]' == $this -> id ) {
    									$_color_map = TC_init::$instance -> skin_color_map;
    								}
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
                          printf('<optgroup label="%1$s">%2$s</optgroup>',
                            $_opt_list['name'],
                            implode($_options)
                          );
                        }
                      break;

                      default:
                        foreach ( $this->choices as $value => $label ) {
                          $_data_hex  = isset($_color_map[esc_attr( $value )]);
                          printf('<option value="%1$s" %2$s %4$s>%3$s</option>',
                            esc_attr( $value ),
                            selected( $this->value(), $value, false ),
                            $label,
                            isset($_color_map[esc_attr( $value )]) ? sprintf( 'data-hex="%s"', $_color_map[esc_attr( $value )] ) : ''
                          );
                        }
                      break;
                    }

    								?>
    						</select>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="tc-notice"><?php echo $this -> notice ?></span>
                <?php endif; ?>
    					</label>
    					<?php
    					//retrieve all sliders in option array
    			        $options          = get_option( 'tc_theme_options' );
    			        $sliders 					= array();
    			        if ( isset( $options['tc_sliders'])) {
    			        	$sliders        = $options['tc_sliders'];
    			    	}

    					if ( 'tc_theme_options[tc_front_slider]' == $this -> id  && empty( $sliders ) ) {
    						 echo '<div style="width:99%; padding: 5px;">';
    		                  echo '<p class="description">'.__("You haven't create any slider yet. Go to the media library, edit your images and add them to your sliders.", "customizr" ).'<br/><a class="button-primary" href="'.admin_url( 'upload.php' ).'" target="_blank">'.__( 'Create a slider' , 'customizr' ).'</a></p>
    		              </div>';
    					}

    				break;


	        	case 'number':
	        		?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
	        		<label>
	        			<span class="tc-number-label customize-control-title"><?php echo esc_html( $this->label ) ?></span>
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
    		        		esc_html( $this->label )
    		        	);
    					?>
    					<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />

    					<?php if(!empty( $this -> notice)) : ?>
    					 <span class="tc-notice"><?php echo $this-> notice ?></span>
    					<?php endif; ?>
    					<hr class="tc-customizer-separator-invisible" />
    					<?php
    				break;

	        	case 'textarea':
	        		?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
    					<label>
    						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
    						<?php if(!empty( $this -> notice)) : ?>
    							<span class="tc-notice"><?php echo $this-> notice; ?></span>
    						<?php endif; ?>
    						<textarea class="widefat" rows="3" cols="10" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
    					</label>
    					<?php
		        	break;

	        	case 'url':
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php
	        		printf('<label><span class="customize-control-title %1$s">%2$s</span><input type="text" value="%3$s" %4$s /></label>',
	        			! empty( $this -> icon) ? $this -> icon : '',
	        			esc_html( $this->label ),
	        			esc_url( $this->value() ),
	        			call_user_func( array( $this, 'get_link' ) )
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
    							<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
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
	}//end of class
endif;


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
			?>
      <?php if ( isset( $this->title) ) : ?>
        <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
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
		}
	}
endif;