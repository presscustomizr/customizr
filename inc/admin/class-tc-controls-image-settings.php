<?php
/*
 * @since 3.4.19
 * @package      Customizr
*/
if ( class_exists('WP_Customize_Cropped_Image_Control') && ! class_exists( 'TC_Customize_Cropped_Image_Control' ) ) :
  class TC_Customize_Cropped_Image_Control extends WP_Customize_Cropped_Image_Control {
    public $type = 'tc_cropped_image';
    public $title;
    public $notice;

    //@override
    public function __construct( $manager, $id, $args = array() ) {
      parent::__construct( $manager, $id, $args );
      //This is needed to load the backbone template, we can think about moving it in another place tough
      $manager -> register_control_type( 'TC_Customize_Cropped_Image_Control' );
    }

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
      $this->json['notice'] = !empty( $this -> notice ) ?           $this -> notice  :  '';
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
