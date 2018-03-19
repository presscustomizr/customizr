<?php
function czr_fn_register_social_links_module( $args ) {
    $defaults = array(
        'id' => '',
        'section' => array(), //array( 'id' => '', 'label' => '' ),
        'sanitize_callback' => '',
        'validate_callback' => '',
        'text-domain' => '',
        'base_url_path' => '',//PC_AC_BASE_URL/inc/czr-modules/social-links/
        'version' => '',
        'option_value' => array() //<= will be used for the dynamic registration
    );
    $args = wp_parse_args( $args, $defaults );

    // set the social module text-domain in the current context => 'customizr', 'hueman', ...
    if ( ! defined( 'PC_SOCIAL_MODULE_TEXT_DOMAIN' ) ) { define( 'PC_SOCIAL_MODULE_TEXT_DOMAIN' , $args['text-domain'] ); }

    $czrnamespace = str_replace( 'CZR_Fmk_Base', '', $GLOBALS['czr_base_fmk']);
    //pc\czr_base_fmk\czr_register_module
    $function_name = $czrnamespace . 'czr_register_module';
    if ( ! function_exists( $function_name ) ) {
        error_log( __FUNCTION__ . ' => Namespace problem' );
        return;
    }

    $function_name( array(
        'id' => $args['id'],
        'dynamic_registration' => true,
        'module_type' => 'czr_social_module',
        'option_value' => ! is_array( $args['option_value'] ) ? array() : $args['option_value'],

        'setting' => array(
            'type' => 'option',
            'default'  => array(),
            'transport' => 'refresh',
            'sanitize_callback' => 'czr_sanitize_callback__czr_social_module',
            'validate_callback' => 'czr_validate_callback__czr_social_module'
        ),

        'section' => array(
            'id' => 'social_links',
            'title' => __( 'Manage your social links', PC_SOCIAL_MODULE_TEXT_DOMAIN ),
            'panel' => '',
            'priority' => 10
        ),

        'control' => array(
            'priority' => 10,
            'label' => __( 'Create and organize your social links', PC_SOCIAL_MODULE_TEXT_DOMAIN ),
            'type'  => 'czr_module',
        ),


        'customizer_assets' => array(
            'control_js' => array(
                // handle + params for wp_enqueue_script()
                // @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
                'czr-social-links-module' => array(
                    'src' => sprintf(
                        '%1$s/assets/js/%2$s',
                        $args['base_url_path'],
                        '_2_7_socials_module.js'
                    ),
                    'deps' => array('customize-controls' , 'jquery', 'underscore'),
                    'ver' => ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : $args['version'],
                    'in_footer' => true
                )
            ),
            'localized_control_js' => array(
                'deps' => 'czr-customizer-fmk',
                'global_var_name' => 'socialLocalized',
                'params' => array(
                    //Social Module
                    'defaultSocialColor' => 'rgb(90,90,90)',
                    'defaultSocialSize'  => 14,
                    //option value for dynamic registration
                )
            )
        ),

        'tmpl' => array(
            'pre-item' => array(
                'social-icon' => array(
                    'input_type'  => 'select',
                    'title'       => __('Select an icon', PC_SOCIAL_MODULE_TEXT_DOMAIN)
                ),
                'social-link'  => array(
                    'input_type'  => 'text',
                    'title'       => __('Social link url', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'notice_after'      => __('Enter the full url of your social profile (must be valid url).', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'placeholder' => __('http://...,mailto:...,...', PC_SOCIAL_MODULE_TEXT_DOMAIN)
                )
            ),
            'mod-opt' => array(
                'social-size' => array(
                    'input_type'  => 'number',
                    'title'       => __('Size in px', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'step'        => 1,
                    'min'         => 5,
                    'transport' => 'postMessage'
                )
            ),
            'item-inputs' => array(
                'social-icon' => array(
                    'input_type'  => 'select',
                    'title'       => __('Social icon', PC_SOCIAL_MODULE_TEXT_DOMAIN)
                ),
                'social-link'  => array(
                    'input_type'  => 'text',
                    'title'       => __('Social link', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'notice_after'      => __('Enter the full url of your social profile (must be valid url).', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'placeholder' => __('http://...,mailto:...,...', PC_SOCIAL_MODULE_TEXT_DOMAIN)
                ),
                'title'  => array(
                    'input_type'  => 'text',
                    'title'       => __('Title', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'notice_after'      => __('This is the text displayed on mouse over.', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                ),
                'social-color'  => array(
                    'input_type'  => 'color',
                    'title'       => sprintf( '%1$s <i>%2$s %3$s</i>', __('Icon color', PC_SOCIAL_MODULE_TEXT_DOMAIN), __('default:', PC_SOCIAL_MODULE_TEXT_DOMAIN), 'rgba(255,255,255,0.7)' ),
                    'notice_after'      => __('Set a unique color for your icon.', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'transport' => 'postMessage'
                ),
                'social-target' => array(
                    'input_type'  => 'check',
                    'title'       => __('Link target', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'notice_after'      => __('Check this option to open the link in a another tab of the browser.', PC_SOCIAL_MODULE_TEXT_DOMAIN),
                    'width-100'   => true
                )
            )
        )
    ));
}//ac_register_social_links_module()





/////////////////////////////////////////////////////////////////
// SANITIZATION
/***
* Social Module sanitization/validation
**/
function czr_sanitize_callback__czr_social_module( $socials ) {
  // error_log( 'IN SANITIZATION CALLBACK' );
  // error_log( print_r( $socials, true ));
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

function czr_validate_callback__czr_social_module( $validity, $socials ) {
  // error_log( 'IN VALIDATION CALLBACK' );
  // error_log( print_r( $socials, true ));
  $ids_malformed_url = array();
  $malformed_message = __( 'An error occurred: malformed social links', PC_SOCIAL_MODULE_TEXT_DOMAIN);

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

  return new WP_Error( 'required', __( 'Please fill the social link inputs with a valid URLs', PC_SOCIAL_MODULE_TEXT_DOMAIN ), $ids_malformed_url );
}

