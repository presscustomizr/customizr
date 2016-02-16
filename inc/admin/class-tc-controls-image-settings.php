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
      $this->json['notice'] = !empty( $this -> notice ) ?           $this -> notice  : '';
      error_log( $this -> width );
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
