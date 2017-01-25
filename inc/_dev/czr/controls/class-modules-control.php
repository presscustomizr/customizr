<?php
/*
* @since 4.0
*/
if ( ! class_exists( 'CZR_Customize_Modules' ) ) :
  class CZR_Customize_Modules extends CZR_controls {
    public $module_type;
    public $syncCollection;
    public $syncSektion;

    /**
    * Constructor.
    *
    */
    public function __construct($manager, $id, $args = array()) {
      //let the parent do what it has to
      parent::__construct($manager, $id, $args );

      //hook validation/sanitization callbacks for the $module_type module
      foreach ( $this -> settings as $key => $setting ) {
        foreach ( array( 'validate', 'sanitize', 'sanitize_js' ) as $callback_prefix ) {
          if ( method_exists( $this, "{$callback_prefix}_callback__{$this->module_type}" )  ) {
            add_filter( "customize_{$callback_prefix}_{$setting->id}", array( $this, "{$callback_prefix}_callback__{$this->module_type}" ), 0, 3 );
          }
        }
      }


    }

    public function render_content(){}

    public function to_json() {
      parent::to_json();
      $this->json['syncCollection'] = $this->syncCollection;
      $this->json['syncSektion'] = $this->syncSektion;
      $this->json['module_type'] = $this->module_type;
    }

    /** Social Module sanitization/validation **/
    public function sanitize_callback__czr_social_module( $socials ) {
      //sanitize urls and titles for the db
      foreach ( $socials as $index => &$social ) {
        $social['social-link']  = esc_url_raw( $social['social-link'] );
        $social['title']        = esc_attr( $social['title'] );
      }
      return $socials;
    }

    public function validate_callback__czr_social_module( $validity, $socials ) {
      $ids_malformed_url = array();
      //validate urls
      foreach ( $socials as $index => $social ) {
        if ( empty($social['social-link']) || $social['social-link'] != esc_url_raw( $social['social-link'] ) )
          array_push( $ids_malformed_url, $social[ 'id' ] );
      }

      if ( empty( $ids_malformed_url) )
        return null;

      return new WP_Error( 'required', __( 'Please fill the social link inputs with valid URLs', 'customizr' ), $ids_malformed_url );
    }
  }
endif;
?>