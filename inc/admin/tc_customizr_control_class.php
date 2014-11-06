<?php
/**
 * Add Controls
 * @package Customizr
 * @since Customizr 1.0 
 */
class TC_Controls extends WP_Customize_Control
	{
	    public $tc;
	    public $link;
	    public $label;
	    public $type;
	    public $buttontext;
	    public $settings;
	    public $hr_after;
	    public $notice;
	    //number vars
	    public $step;
	    public $min;

	    public function render_content()
	    {
	        switch ($this -> tc) {
	        	case 'hr':
	        		echo '<hr style="border-color: white;margin-top:15px" />';
	        		break;
	        	
	        	case 'button':
	        		echo '<a class="button-primary" href="'.admin_url($this -> link ).'" target="_blank">'.$this -> buttontext.'</a>';
	        		if ($this -> hr_after == true)
	        			echo '<hr style="border-color: white;margin-top:15px">';
	        		break;

	        	case 'number':
	        		?>
	        		<label>
	        		<span class="customize-control-title"><?php echo esc_html( $this->label ) ?></span>
		        		<input <?php $this->link() ?> type="number" step="<?php echo $this-> step ?>" min="<?php echo $this-> min ?>" id="posts_per_page" value="<?php echo $this->value() ?>" class="small-text">
		        		<?php if(!empty($this -> notice)) : ?>
			        		<i><?php echo esc_html( $this-> notice ) ?></i>
			        	<?php endif; ?>
		        	</label>
		        	<?php
	        		break;

	        	case 'checkbox':
				?>
				<label>
					<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
					<?php echo esc_html( $this->label ); ?>
				</label><br />
				<?php if(!empty($this -> notice)) : ?>
			        	<i><?php echo esc_html( $this-> notice ) ?></i>
			        <?php endif; ?>
				<?php
				break;

	        	case 'textarea':
	        		?>
					<label>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
						<span style="color: #999;font-size:11px"><?php echo esc_html( $this-> notice); ?></span>
						<textarea class="widefat" rows="3" cols="10" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
					</label>
					<?php
		        	break;

	        	case 'url':
	        		?>
					<label>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
						<input type="text" value="<?php echo esc_url( $this->value() ); ?>"  <?php $this->link(); ?> />
					</label>
					<?php
		        	break;
	        	
	        	case 'slider-check':
	        	$name_value = '';

	        	$saved = (array) get_option( 'tc_theme_options' );
	        	
	        	if(isset($saved['tc_front_slider']))
	        		$name_value = $saved['tc_front_slider'];
	        	
	        		 //check if there is at least on slider name created
			        $have_sliders = get_terms( 'slider', 'number=1&orderby=count&hide_empty=0' );

			        $slider_check = false;
			        if($have_sliders)
			          $slider_check = true;

			        //get the selected slider object
			        $slider = get_term_by( 'id', $name_value, 'slider' );
			        
			        //check if there is at least one slide published
			        $have_slides = get_posts(  array(
			          'numberposts'    =>  5,
			          'post_type'      =>  'slide',
			          'post_status'    =>  'publish' )
			        );
			        $slide_check = false;
			        if($have_slides)
			          $slide_check = true;

			            if(!$slider_check) {
			               echo '<div style="width:99%; padding: 5px;">';
			                  echo '<p class="description">'.__("You haven't created any slider yet. Click on the button to add you first slider. Once created, just add any slides you need to it.", "customizr" ).'<br/><a class="button-primary" href="'.admin_url( 'edit-tags.php?taxonomy=slider&post_type=slide').'" target="_blank">'.__('Create a slider','customizr').'</a></p>
			              </div>';
			            }
	        		break;

	        	default:
	        		screen_icon( $this -> tc );
	        		break;
	        }
	    }
	}



if(!function_exists('tc_sanitize_textarea')) :
/**
 * adds sanitization callback funtion : textarea
 * @package Customizr
 * @since Customizr 1.1.4
 */
	function tc_sanitize_textarea($value) {
		$value = esc_textarea( $value);
		return $value;
	}
endif;




if(!function_exists('tc_sanitize_number')) :
/**
 * adds sanitization callback funtion : number
 * @package Customizr
 * @since Customizr 1.1.4
 */
	function tc_sanitize_number($value) {
		$value = esc_attr( $value); // clean input
		$value = (int) $value; // Force the value into integer type.
   		return ( 0 < $value ) ? $value : null;
	}
endif;



if(!function_exists('tc_sanitize_url')) :
/**
 * adds sanitization callback funtion : url
 * @package Customizr
 * @since Customizr 1.1.4
 */
	function tc_sanitize_url($value) {
		$value = esc_url( $value);
		return $value;
	}
endif;


?>