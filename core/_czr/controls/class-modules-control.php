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

    /***
    * Social Module sanitization/validation
    **/
    public function sanitize_callback__czr_social_module( $socials ) {
      if ( empty( $socials ) )
        return array();

      //sanitize urls and titles for the db
      foreach ( $socials as $index => &$social ) {
        if ( ! is_array( $social ) || ! ( array_key_exists( 'social-link', $social) &&  array_key_exists( 'title', $social) ) )
          continue;

        $social['social-link']  = esc_url_raw( $social['social-link'] );
        $social['title']        = esc_attr( $social['title'] );
      }
      return $socials;
    }

    public function validate_callback__czr_social_module( $validity, $socials ) {
      $ids_malformed_url = array();
      $malformed_message = __( 'An error occurred: malformed social links', 'customizr' );

      if ( empty( $socials ) )
        return array();


      //(
      //     [0] => Array
      //         (
      //             [is_mod_opt] => 1
      //             [module_id] => tc_social_links_czr_module
      //             [social-size] => 15
      //         )

      //     [1] => Array
      //         (
      //             [id] => czr_social_module_0
      //             [title] => Follow us on Renren
      //             [social-icon] => fa-renren
      //             [social-link] => http://customizr-dev.dev/feed/rss/
      //             [social-color] => #6d4c8e
      //             [social-target] => 1
      //         )
      // )
      //validate urls
      foreach ( $socials as $index => $item_or_modopt ) {
        if ( ! is_array( $item_or_modopt ) )
          return new WP_Error( 'required', $malformed_message );

        //should be an item or a mod opt
        if ( ! array_key_exists( 'is_mod_opt', $item_or_modopt ) && ! array_key_exists( 'id', $item_or_modopt ) )
          return new WP_Error( 'required', $malformed_message );

        //if modopt case, skip
        if ( array_key_exists( 'is_mod_opt', $item_or_modopt ) )
          continue;

        if ( $item_or_modopt['social-link'] != esc_url_raw( $item_or_modopt['social-link'] ) )
          array_push( $ids_malformed_url, $item_or_modopt[ 'id' ] );
      }

      if ( empty( $ids_malformed_url) )
        return null;

      return new WP_Error( 'required', __( 'Please fill the social link inputs with a valid URLs', 'customizr' ), $ids_malformed_url );
    }
  }
endif;
?>