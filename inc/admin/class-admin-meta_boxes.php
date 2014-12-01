<?php
/**
* Posts, pages and attachment actions and filters
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
if ( ! class_exists( 'TC_meta_boxes' ) ) :
  class TC_meta_boxes {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          add_action( 'add_meta_boxes'                       , array( $this , 'tc_post_meta_boxes' ));
          add_action( '__post_slider_infos'                  , array( $this , 'tc_get_post_slider_infos' ));
          add_action( 'save_post'                            , array( $this , 'tc_post_fields_save' ));

          add_action( 'add_meta_boxes'                       , array( $this , 'tc_attachment_meta_box' ));
          add_action( '__attachment_slider_infos'            , array( $this , 'tc_get_attachment_slider_infos' ));
          add_action( 'edit_attachment'                      , array( $this , 'tc_slide_save' ));

          add_action( '__show_slides'                        , array( $this , 'tc_show_slides' ), 10, 2);

          add_action( 'wp_ajax_slider_action'                , array( $this , 'tc_slider_cb' ));

          add_action( 'admin_enqueue_scripts'                , array( $this , 'tc_slider_admin_scripts' ));



        /**
         * checks if WP version strictly < 3.5
         * before 3.5, attachements were not managed as posts. But two filter hooks can are very useful
         * @package Customizr
         * @since Customizr 2.0
         */
        global $wp_version;
        if (version_compare( $wp_version, '3.5' , '<' ) ) {
            add_filter( 'attachment_fields_to_edit'           , array( $this , 'tc_attachment_filter' ), 11, 2 );
            add_filter( 'attachment_fields_to_save'           , array( $this , 'tc_attachment_save_filter' ), 11, 2 );
          }

      }//end of __construct



    /*
    ----------------------------------------------------------------
    -------- DEFINE POST/PAGE LAYOUT AND SLIDER META BOXES ---------
    ----------------------------------------------------------------
    */

    /**
     * Adds layout and slider metaboxes to pages and posts
     * @package Customizr
     * @since Customizr 1.0
     */
      function tc_post_meta_boxes() {//id, title, callback, post_type, context, priority, callback_args
           /***
            Determines which screens we display the box
          **/
          //1 - retrieves the custom post types
          $args                 = array(
          //'public'   => true,
          '_builtin' => false
          );
          $custom_post_types    = get_post_types($args);

          //2 - Merging with the builtin post types, pages and posts
          $builtin_post_types   = array(
            'page' => 'page',
            'post' => 'post'
            );
          $screens              = array_merge( $custom_post_types, $builtin_post_types );

          //3- Adding the meta-boxes to those screens
          foreach ( $screens as $key => $screen) {
              //skip if acf
              if ('acf' == $screen )
                continue;
              add_meta_box(
                  'layout_sectionid' ,
                  __( 'Layout Options' , 'customizr' ),
                  array( $this , 'tc_post_layout_box' ),
                  $screen,
                  ( 'page' == $screen | 'post' == $screen ) ? 'side' : 'normal',//displays meta box below editor for custom post types
                  apply_filters('tc_post_meta_boxes_priority' , 'high', $screen )
              );
              add_meta_box(
                  'slider_sectionid' ,
                  __( 'Slider Options' , 'customizr' ),
                  array( $this , 'tc_post_slider_box' ),
                  $screen,
                  'normal' ,
                  apply_filters('tc_post_meta_boxes_priority' , 'high', $screen)
              );
          }//end foreach
      }







      /**
       * Prints the box content
       * @package Customizr
       * @since Customizr 1.0
       */
      function tc_post_layout_box( $post ) {
            // Use nonce for verification
            wp_nonce_field( plugin_basename( __FILE__ ), 'post_layout_noncename' );

            // The actual fields for data entry
            // Use get_post_meta to retrieve an existing value from the database and use the value for the form
            //Layout name setup
            $layout_id            = 'layout_field';

            $layout_value         = esc_attr(get_post_meta( $post -> ID, $key = 'layout_key' , $single = true ));

            //Generates layouts select list array
            $layouts              = array();
            $global_layout        = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
            foreach ( $global_layout as $key => $value ) {
              $layouts[$key]      = call_user_func( '__' , $value['metabox'] , 'customizr' );
            }

            //by default we apply the global default layout
            $tc_sidebar_default_layout  = esc_attr( tc__f( '__get_option' , 'tc_sidebar_global_layout' ) );

            //get the lists of eligible post types + normal posts (not pages!)
            $args                 = array(
            //'public'   => true,
            '_builtin' => false
            );
            $custom_post_types    = get_post_types($args);
            $add_normal_post      = array(
              'post' => 'post'
              );
            $eligible_posts       = array_merge( $custom_post_types, $add_normal_post );

            //eligible posts (and custom posts types) default layout
            if ( in_array($post->post_type , $eligible_posts ) ) {
              $tc_sidebar_default_layout  = esc_attr( tc__f( '__get_option' , 'tc_sidebar_post_layout') );
            }

            //page default layout
            if ( $post->post_type == 'page' ) {
              $tc_sidebar_default_layout  = esc_attr( tc__f( '__get_option' , 'tc_sidebar_page_layout') );
            }

            //check if the 'force default layout' option is checked
            $force_layout                 = esc_attr( tc__f( '__get_option' , 'tc_sidebar_force_layout') );


            ?>
            <div class="meta-box-item-content">
              <?php if( $layout_value == null) : ?>
                <p><?php printf(__( 'Default %1$s layout is set to : %2$s' , 'customizr' ), $post -> post_type == 'page' ? __( 'pages' , 'customizr' ):__( 'posts' , 'customizr' ), '<strong>'.$layouts[$tc_sidebar_default_layout].'</strong>' ) ?></p>
              <?php endif; ?>

              <?php if ( $force_layout == 1) :?>
              <div style="width:99%; padding: 5px;">
                <p><i><?php _e( 'You have checked the <i>"Force global default layout for all posts and pages"</i>, you must unchecked this option to enable a specific layout for this post.' , 'customizr' ); ?></i></p>
                <p><a class="button-primary" href="<?php echo admin_url( 'customize.php' ); ?>" target="_blank"><?php _e( 'Change layout options' , 'customizr' ) ?></a></p>
              </div>

              <?php else : ?>
                  <i><?php printf(__( 'You can define a specific layout for %1$s by using the pre-defined left and right sidebars. The default layouts can be defined in the WordPress customizer screen %2$s.<br />' , 'customizr' ),
                    $post -> post_type == 'page' ? __( 'this page' , 'customizr' ):__( 'this post' , 'customizr' ),
                    '<a href="'.admin_url( 'customize.php' ).'" target="_blank">'.__( 'here' , 'customizr' ).'</a>'
                    ); ?>
                  </i>
                  <h4><?php printf(__( 'Select a specific layout for %1$s' , 'customizr' ),
                  $post -> post_type == 'page' ? __( 'this page' , 'customizr' ):__( 'this post' , 'customizr' )); ?></h4>
                  <select name="<?php echo $layout_id; ?>" id="<?php echo $layout_id; ?>">
                  <?php //no layout selected ?>
                    <option value="" <?php selected( $layout_value, $current = null, $echo = true ) ?>> <?php printf(__( 'Default layout %1s' , 'customizr' ),
                         '( '.$layouts[$tc_sidebar_default_layout].' )'
                         );
                      ?></option>
                    <?php foreach( $layouts as $key => $l) : ?>
                      <option value="<?php echo $key; ?>" <?php selected( $layout_value, $current = $key, $echo = true ) ?>><?php echo $l; ?></option>
                   <?php endforeach; ?>
                  </select>
             <?php endif; ?>
          </div>

          <?php
      }






      /*
      ----------------------------------------------------------------
      ------------------ POST/PAGE SLIDER BOX ------------------------
      ----------------------------------------------------------------
      */


      /**
       * Prints the slider box content
       * @package Customizr
       * @since Customizr 2.0
       */
        function tc_post_slider_box( $post ) {
            // Use nonce for verification
            wp_nonce_field( plugin_basename( __FILE__ ), 'post_slider_noncename' );

            // The actual fields for data entry
            //title check field setup
            $post_slider_check_id       = 'post_slider_check_field';
            $post_slider_check_value    = esc_attr(get_post_meta( $post -> ID, $key = 'post_slider_check_key' , $single = true ));

            ?>
           <div class="meta-box-item-title">
                <h4><?php _e( 'Add a slider to this post/page' , 'customizr' ); ?></h4>
                  <label for="<?php echo $post_slider_check_id; ?>">
              </label>
            </div>
            <div class="meta-box-item-content">
              <input name="tc_post_id" id="tc_post_id" type="hidden" value="<?php echo $post-> ID ?>"/>
               <?php
                 $post_slider_checked = false;
                 if ( $post_slider_check_value == 1)
                  $post_slider_checked = true;
                ?>
              <input name="<?php echo $post_slider_check_id; ?>" type="hidden" value="0"/>
              <input name="<?php echo $post_slider_check_id ?>" id="<?php echo $post_slider_check_id; ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $post_slider_checked, $current = true, $echo = true ) ?>/>
            </div>
            <div id="post_slider_infos">
              <?php do_action( '__post_slider_infos' , $post -> ID ); ?>
            </div>
          <?php
      }//end of function





    /**
     * Display post slider dynamic content
     * This function is also called by the ajax call back
     * @package Customizr
     * @since Customizr 2.0
     */
      function tc_get_post_slider_infos( $postid) {
          //check value is ajax saved ?
          $post_slider_check_value   = esc_attr(get_post_meta( $postid, $key = 'post_slider_check_key' , $single = true ));

         //retrieve all sliders in option array
          $options                   = get_option( 'tc_theme_options' );
          if ( isset($options['tc_sliders']) ) {
            $sliders                   = $options['tc_sliders'];
          }

          //post slider fields setup
          $post_slider_id            = 'post_slider_field';

          //get current post slider
          $current_post_slider       = esc_attr(get_post_meta( $postid, $key = 'post_slider_key' , $single = true ));
          if ( isset( $sliders[$current_post_slider])) {
            $current_post_slides     = $sliders[$current_post_slider];
          }

          //Delay field setup
          $delay_id                  = 'slider_delay_field';
          $delay_value               = esc_attr(get_post_meta( $postid, $key = 'slider_delay_key' , $single = true ));

          //Layout field setup
          $layout_id                 = 'slider_layout_field';
          $layout_value              = esc_attr(get_post_meta( $postid, $key = 'slider_layout_key' , $single = true ));

          //sliders field
          $slider_id                 = 'slider_field';


          ?>
          <?php if( $post_slider_check_value == true) : ?>
              <div class="meta-box-item-title">
                <h4><?php _e("Choose a slider", 'customizr' ); ?></h4>
              </div>
              <?php if (isset($sliders) && !empty( $sliders)) : ?>
                <div class="meta-box-item-content">
                  <span class="spinner" style="float: left;"></span>
                  <select name="<?php echo $post_slider_id; ?>" id="<?php echo $post_slider_id; ?>">
                    <?php //no link option ?>
                    <option value="" <?php selected( $current_post_slider, $current = null, $echo = true ) ?>> <?php _e( '&mdash; Select a slider &mdash; ' , 'customizr' ); ?></option>
                       <?php foreach( $sliders as $slider_name => $slider_posts) : ?>
                            <option value="<?php echo esc_attr( $slider_name) ?>" <?php selected( $slider_name, $current = $current_post_slider, $echo = true ) ?>><?php echo esc_attr( $slider_name) ?></option>
                       <?php endforeach; ?>
                  </select>
                   <i><?php _e( 'To create a new slider : open the media library, edit your images and add them to your new slider.' , 'customizr' ) ?></i>
                </div>

                <div class="meta-box-item-title">
                  <h4><?php _e("Delay between each slides in milliseconds (default : 5000 ms)", 'customizr' ); ?></h4>
                </div>
                <div class="meta-box-item-content">
                    <input name="<?php echo esc_attr( $delay_id) ; ?>" id="<?php echo esc_attr( $delay_id); ?>" value="<?php if (empty( $delay_value)) { echo '5000';} else {echo esc_attr( $delay_value);} ?>"/>
                </div>

                <div class="meta-box-item-title">
                    <h4><?php _e("Slider Layout : set the slider in full width", 'customizr' );  ?></h4>
                </div>
                <div class="meta-box-item-content">
                    <?php
                    if ( $layout_value ==null || $layout_value ==1 )
                    {
                      $layout_check_value = true;
                    }
                    else {
                      $layout_check_value = false;
                    }
                    ?>
                    <input name="<?php echo $layout_id; ?>" type="hidden" value="0"/>
                    <input name="<?php echo $layout_id; ?>" id="<?php echo $layout_id; ?>" type="checkbox" class="iphonecheck" value="1"<?php checked( $layout_check_value, $current = true, $echo = true ) ?>/>
                </div>
                <?php if (isset( $current_post_slides)) : ?>
                      <div style="z-index: 1000;position: relative;">
                        <p style="display: inline-block;float: right;"><a href="#TB_inline?width=350&height=100&inlineId=post_slider-warning-message" class="thickbox"><?php _e( 'Delete this slider' , 'customizr' ) ?></a></p>
                      </div>
                      <div id="post_slider-warning-message" style="display:none;">
                        <div style="text-align:center">
                           <p>
                             <?php _e( 'The slider will be deleted permanently (images, call to actions and link will be kept).' , 'customizr' ) ?>
                          </p>
                            <br/>
                             <a class="button-secondary" id="delete-slider" href="#" title="<?php _e( 'Delete slider' , 'customizr' ); ?>" onClick="javascript:window.parent.tb_remove()"><?php _e( 'Delete slider' , 'customizr' ); ?></a>
                        </div>
                      </div>
                    <?php  do_action( '__show_slides' , $current_post_slides, $current_attachement_id = null); ?>
                <?php endif; ?>
              <?php else : //if no slider created yet ?>

                 <div class="meta-box-item-content">
                   <p class="description"> <?php _e("You haven't create any slider yet. Go to the media library, edit your images and add them to your sliders.", "customizr" ) ?><br/>
                   </p>
                    <br />
                </div>
              <?php endif; //sliders? ?>
            <?php endif; //check slider? ?>
        <?php
      }






      /*
      ----------------------------------------------------------------
      ------- SAVE POST/PAGE FIELDS (LAYOUT AND SLIDER FIELDS) -------
      ----------------------------------------------------------------
      */
      /**
       * When the post/page is saved, saves our custom data for slider and layout options
       * @package Customizr
       * @since Customizr 1.0
       */
      function tc_post_fields_save( $post_id ) {
        // verify if this is an auto save routine.
        // If it is our form has not been submitted, so we dont want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        // Check permissions
        if ( isset( $_POST['post_type']) && 'page' == $_POST['post_type'] )
        {
          if ( !current_user_can( 'edit_page' , $post_id ) )
              return;
        }
        else
        {
          if ( !current_user_can( 'edit_post' , $post_id ) )
              return;
        }

        ################# LAYOUT BOX #################
        // verify this came from our screen and with proper authorization,
        if ( isset( $_POST['post_layout_noncename']) && !wp_verify_nonce( $_POST['post_layout_noncename'], plugin_basename( __FILE__ ) ) )
            return;

        // OK, we're authenticated: we need to find and save the data
        //set up the fields array
        $tc_post_layout_fields = array(
            'layout_field'              =>  'layout_key'
            );

        //if saving in a custom table, get post_ID
       if ( isset( $_POST['post_ID'])) {
          $post_ID = $_POST['post_ID'];
          //sanitize user input by looping on the fields
          foreach ( $tc_post_layout_fields as $tcid => $tckey) {
              if ( isset( $_POST[$tcid])) {
                $mydata = sanitize_text_field( $_POST[$tcid] );

                // Do something with $mydata
                // either using
                add_post_meta( $post_ID, $tckey, $mydata, true) or
                  update_post_meta( $post_ID, $tckey , $mydata);
                // or a custom table (see Further Reading section below)
              }
             }
        }

        ################# SLIDER BOX #################
        // verify this came from our screen and with proper authorization,
        if ( isset( $_POST['post_slider_noncename']) && !wp_verify_nonce( $_POST['post_slider_noncename'], plugin_basename( __FILE__ ) ) )
            return;

        // OK, we're authenticated: we need to find and save the data
        //set up the fields array
        $tc_post_slider_fields = array(
            'post_slider_check_field'   => 'post_slider_check_key' ,
            'slider_delay_field'        => 'slider_delay_key' ,
            'slider_layout_field'       => 'slider_layout_key' ,
            'post_slider_field'         => 'post_slider_key' ,
            );

        //if saving in a custom table, get post_ID
       if ( isset( $_POST['post_ID'])) {
          $post_ID = $_POST['post_ID'];
          //sanitize user input by looping on the fields
          foreach ( $tc_post_slider_fields as $tcid => $tckey) {
            if ( isset( $_POST[$tcid])) {
                $mydata = sanitize_text_field( $_POST[$tcid] );

                // Do something with $mydata
                // either using
                add_post_meta( $post_ID, $tckey, $mydata, true) or
                  update_post_meta( $post_ID, $tckey , $mydata);
                // or a custom table (see Further Reading section below)
               }
            }
          }
      }





      /*
      ----------------------------------------------------------------
      ------------------ ATTACHMENT SLIDER META BOX ------------------
      ----------------------------------------------------------------
      */
      /**
       * Add a slider metabox to attachments
       * @package Customizr
       * @since Customizr 2.0
       */
        function tc_attachment_meta_box() {//id, title, callback, post_type, context, priority, callback_args
          $screens = array( 'attachment' );
          foreach ( $screens as $screen) {
              add_meta_box(
                  'slider_sectionid' ,
                  __( 'Slider Options' , 'customizr' ),
                  array( $this , 'tc_attachment_slider_box' ),
                  $screen/*,
                  'side' ,
                  'high'*/
              );
            }
        }






      /**
       * Prints the slider box content
       * @package Customizr
       * @since Customizr 2.0
       */
        function tc_attachment_slider_box( $post ) {
            // Use nonce for verification
            //wp_nonce_field( plugin_basename( __FILE__ ), 'slider_noncename' );
            // The actual fields for data entry
            //title check field setup
            $slider_check_id       = 'slider_check_field';
            $slider_check_value    = esc_attr(get_post_meta( $post -> ID, $key = 'slider_check_key' , $single = true ));

            ?>
           <div class="meta-box-item-title">
                <h4><?php _e( 'Add to a slider' , 'customizr' ); ?></h4>
                  <label for="<?php echo $slider_check_id; ?>">
                </i><?php _e( 'Add to a slider (create one if needed)' , 'customizr' ) ?></i>
              </label>
            </div>
            <div class="meta-box-item-content">
              <input name="tc_post_id" id="tc_post_id" type="hidden" value="<?php echo $post-> ID ?>"/>
               <?php
                 $slider_checked = false;
                 if ( $slider_check_value == 1)
                  $slider_checked = true;
                ?>
              <input name="<?php echo $slider_check_id; ?>" type="hidden" value="0"/>
              <input name="<?php echo $slider_check_id ?>" id="<?php echo $slider_check_id; ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $slider_checked, $current = true, $echo = true ) ?>/>
            </div>
           <div id="tc_slider_list">
             <?php do_action( '__attachment_slider_infos' , $post -> ID); ?>
           </div>
          <?php
      }







      /**
       * Display attachment slider dynamic content
       * This function is also called by the ajax call back function
       * @package Customizr
       * @since Customizr 2.0
       */
        function tc_get_attachment_slider_infos( $postid) {
          //check value is ajax saved ?
          $slider_check_value     = esc_attr(get_post_meta( $postid, $key = 'slider_check_key' , $single = true ));

          //post slider fields setup
          $post_slider_id         = 'post_slider_field';

          //sliders field
          $slider_id              = 'slider_field';

          //retrieve all sliders in option array
          $options                = get_option( 'tc_theme_options' );
          $sliders                = array();
          if ( isset( $options['tc_sliders'])) {
            $sliders              = $options['tc_sliders'];
          }

          //get_attachment details for default slide values
          $attachment             = get_post( $postid);
          $default_title          = $attachment->post_title;
          $default_description    = $attachment->post_excerpt;

          //title field setup
          $title_id               = 'slide_title_field';
          $title_value            = esc_attr(get_post_meta( $postid, $key = 'slide_title_key' , $single = true ));
          //we define a filter for the slide_text_length
          $default_title_length   = apply_filters( 'tc_slide_title_length', 80 );

          //check if we already have a custom key created for this field, if not apply default value
          if(!in_array( 'slide_title_key' ,get_post_custom_keys( $postid))) {
            $title_value = $default_title;
          }
          if (strlen( $title_value) > $default_title_length) {
            $title_value = substr( $title_value,0,strpos( $title_value, ' ' , $default_title_length));
            $title_value = esc_html( $title_value) . ' ...';
          }
          else {
            $title_value = esc_html( $title_value);
          }


          //text_field setup : sanitize and limit length
          $text_id        = 'slide_text_field';
          $text_value     = esc_html(get_post_meta( $postid, $key = 'slide_text_key' , $single = true ));
           //we define a filter for the slide_title_length
          $default_text_length   = apply_filters( 'tc_slide_text_length', 250 );

           //check if we already have a custom key created for this field, if not apply default value
          if(!in_array( 'slide_text_key' ,get_post_custom_keys( $postid)))
            $text_value = $default_description;

          if (strlen( $text_value) > $default_text_length) {
            $text_value = substr( $text_value,0,strpos( $text_value, ' ' ,$default_text_length));
            $text_value = $text_value . ' ...';
          }
          else {
            $text_value = $text_value;
          }

           //Color field setup
          $color_id       = 'slide_color_field';
          $color_value    = esc_attr(get_post_meta( $postid, $key = 'slide_color_key' , $single = true ));

          //button field setup
          $button_id      = 'slide_button_field';
          $button_value   = esc_attr(get_post_meta( $postid, $key = 'slide_button_key' , $single = true ));
          //we define a filter for the slide text_button length
          $default_button_length   = apply_filters( 'tc_slide_button_length', 80 );

          if (strlen( $button_value) > $default_button_length) {
            $button_value = substr( $button_value,0,strpos( $button_value, ' ' ,$default_button_length));
            $button_value = $button_value . ' ...';
          }
          else {
            $button_value = $button_value;
          }

          //link field setup
          $link_id        = 'slide_link_field';
          $link_value     = esc_attr(get_post_meta( $postid, $key = 'slide_link_key' , $single = true ));

          //retrieve post, pages and custom post types (if any) and generate the ordered select list for the button link
          $post_types     = get_post_types(array( 'public' => true));
          $excludes       = array( 'attachment' );

          foreach ( $post_types as $t) {
              if (!in_array( $t, $excludes)) {
               //get the posts a tab of types
               $tc_all_posts[$t] = get_posts(  array(
                  'numberposts'     =>  100,
                  'orderby'         =>  'date' ,
                  'order'           =>  'DESC' ,
                  'post_type'       =>  $t,
                  'post_status'     =>  'publish' )
                );
              }
            };

          //display fields if slider button is checked
          if ( $slider_check_value == 1)  {
             ?>
            <div class="meta-box-item-title">
                <h4><?php _e( 'Title text (80 car. max length)' , 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input class="widefat" name="<?php echo esc_attr( $title_id); ?>" id="<?php echo esc_attr( $title_id); ?>" value="<?php echo esc_attr( $title_value); ?>" style="width:50%">
            </div>

            <div class="meta-box-item-title">
                <h4><?php _e( 'Description text (below the title, 250 car. max length)' , 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <textarea name="<?php echo esc_attr( $text_id); ?>" id="<?php echo esc_attr( $text_id); ?>" style="width:50%"><?php echo esc_attr( $text_value); ?></textarea>
            </div>

             <div class="meta-box-item-title">
                <h4><?php _e("Title and text color", 'customizr' );  ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input id="<?php echo esc_attr( $color_id); ?>" name="<?php echo esc_attr( $color_id); ?>" value="<?php echo esc_attr( $color_value); ?>"/>
                <div id="colorpicker"></div>
            </div>

             <div class="meta-box-item-title">
                <h4><?php _e( 'Button text (80 car. max length)' , 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input class="widefat" name="<?php echo esc_attr( $button_id); ?>" id="<?php echo esc_attr( $button_id); ?>" value="<?php echo esc_attr( $button_value); ?>" style="width:50%">
            </div>

            <div class="meta-box-item-title">
                <h4><?php _e("Choose a linked page or post (among the last 100).", 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <select name="<?php echo esc_attr( $link_id); ?>" id="<?php echo esc_attr( $link_id); ?>">
                  <?php //no link option ?>
                  <option value="" <?php selected( $link_value, $current = null, $echo = true ) ?>> <?php _e( 'No link' , 'customizr' ); ?></option>
                  <?php foreach( $tc_all_posts as $type) : ?>
                      <?php foreach ( $type as $key => $item) : ?>
                    <option value="<?php echo esc_attr( $item -> ID); ?>" <?php selected( $link_value, $current = $item -> ID, $echo = true ) ?>>{<?php echo esc_attr( $item -> post_type) ;?>}&nbsp;<?php echo esc_attr( $item -> post_title); ?></option>
                      <?php endforeach; ?>
                 <?php endforeach; ?>
                </select><br />
            </div>
            <div class="meta-box-item-title">
              <h4><?php _e("Choose a slider", 'customizr' ); ?></h4>
            </div>
            <?php if (!empty( $sliders)) : ?>
              <div class="meta-box-item-content">
                  <?php //get current post slider
                    $current_post_slider = null;
                    foreach( $sliders as $slider_name => $slider_posts) {
                       if (in_array( $postid, $slider_posts)) {
                            $current_post_slider = $slider_name;
                            $current_post_slides = $slider_posts;
                        }
                    }
                  ?>
                  <select name="<?php echo esc_attr( $post_slider_id); ?>" id="<?php echo esc_attr( $post_slider_id); ?>">
                    <?php //no link option ?>
                    <option value="" <?php selected( $current_post_slider, $current = null, $echo = true ) ?>> <?php _e( '&mdash; Select a slider &mdash; ' , 'customizr' ); ?></option>
                       <?php foreach( $sliders as $slider_name => $slider_posts) : ?>
                            <option value="<?php echo $slider_name ?>" <?php selected( $slider_name, $current = $current_post_slider, $echo = true ) ?>><?php echo $slider_name?></option>
                       <?php endforeach; ?>
                  </select>
                  <input name="<?php echo $slider_id  ?>" id="<?php echo $slider_id ?>" value=""/>
                  <span class="button-primary" id="tc_create_slider"><?php _e( 'Add a slider' , 'customizr' ) ?></span>
                  <span class="spinner" style="float: left;"></span>
                  <?php if (isset( $current_post_slides)) : ?>
                      <p style="text-align:right"><a href="#TB_inline?width=350&height=100&inlineId=slider-warning-message" class="thickbox"><?php _e( 'Delete this slider' , 'customizr' ) ?></a></p>
                      <div id="slider-warning-message" style="display:none;">
                        <div style="text-align:center">
                           <p>
                             <?php _e( 'The slider will be deleted permanently (images, call to actions and link will be kept).' , 'customizr' ) ?>
                          </p>
                            <br/>
                             <a class="button-secondary" id="delete-slider" href="#" title="<?php _e( 'Delete slider' , 'customizr' ); ?>" onClick="javascript:window.parent.tb_remove()"><?php _e( 'Delete slider' , 'customizr' ); ?></a>
                        </div>
                      </div>
                  <?php endif; ?>
                </div>


                <?php
                  if ( isset( $current_post_slides) ) {
                    $current_attachement_id = $postid;
                    do_action( '__show_slides' ,$current_post_slides, $current_attachement_id);
                  }
                ?>

            <?php else : //if no slider created yet ?>

                 <div class="meta-box-item-content">
                   <p class="description"> <?php _e("You haven't create any slider yet. Write a slider name and click on the button to add you first slider.", "customizr" ) ?><br/>
                    <input name="<?php echo $slider_id  ?>" id="<?php echo $slider_id ?>" value=""/>
                    <span class="button-primary" id="tc_create_slider"><?php _e( 'Add a slider' , 'customizr' ) ?></span>
                    <span class="spinner" style="float: left"></span>
                   </p>
                    <br />
                </div>
            <?php endif; ?>
              <?php
          }//endif slider checked (used for ajax call back!)
      }





      /*
      ----------------------------------------------------------------
      -------------------- SAVE ATTACHMENT FIELDS --------------------
      ----------------------------------------------------------------
      */

      /**
       * When the attachment is saved, saves our custom slider data
       * @package Customizr
       * @since Customizr 2.0
       */
        function tc_slide_save( $post_id ) {
          // verify if this is an auto save routine.
          // If it is our form has not been submitted, so we dont want to do anything


          if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
              return;

          // verify this came from our screen and with proper authorization,
          // because save_post can be triggered at other times

          if ( isset( $_POST['slider_noncename']) && !wp_verify_nonce( $_POST['slider_noncename'], plugin_basename( __FILE__ ) ) )
              return;

          // Check permissions
          if ( !current_user_can( 'edit_post' , $post_id ) )
                return;

          // OK, we're authenticated: we need to find and save the data

          //set up the fields array
          $tc_slider_fields = array(
              'slide_title_field'           => 'slide_title_key' ,
              'slide_text_field'            => 'slide_text_key' ,
              'slide_color_field'           => 'slide_color_key' ,
              'slide_button_field'          => 'slide_button_key' ,
              'slide_link_field'            => 'slide_link_key'
              );

          //if saving in a custom table, get post_ID
          if ( $post_id == null)
            return;

            //sanitize user input by looping on the fields
            foreach ( $tc_slider_fields as $tcid => $tckey) {
                if ( isset( $_POST[$tcid])) {
                  $mydata = sanitize_text_field( $_POST[$tcid] );
                    switch ( $tckey) {
                      //different sanitizations

                      case 'slide_text_key':
                          $default_text_length = apply_filters( 'tc_slide_text_length', 250 );
                          if (strlen( $mydata) > $default_text_length) {
                          $mydata = substr( $mydata,0,strpos( $mydata, ' ' ,$default_text_length));
                          $mydata = esc_html( $mydata) . ' ...';
                          }
                          else {
                            $mydata = esc_html( $mydata);
                          }
                        break;

                      default://for button, color, title and post link field (actually not a link but an id)
                          $default_title_length = apply_filters( 'tc_slide_title_length', 80 );
                         if (strlen( $mydata) > $default_title_length) {
                          $mydata = substr( $mydata,0,strpos( $mydata, ' ' , $default_title_length));
                          $mydata = esc_attr( $mydata) . ' ...';
                          }
                          else {
                            $mydata = esc_attr( $mydata);
                          }
                        break;
                    }//end switch
                  //write in DB
                  add_post_meta( $post_id, $tckey, $mydata, true) or
                  update_post_meta( $post_id, $tckey , $mydata);
                }//end if isset $tckey
            }//end foreach
        }






      /*
      ----------------------------------------------------------------
      ---------- DISPLAY SLIDES TABLE (post and attachment) ----------
      ----------------------------------------------------------------
      */

      /**
       * Display slides table dynamic content for the selected slider
       * @package Customizr
       * @since Customizr 2.0
       */
      function tc_show_slides ( $current_post_slides,$current_attachement_id) {
          //check if we have slides to show
          ?>
          <?php if(empty( $current_post_slides)) : ?>
            <div class="meta-box-item-content">
               <p class="description"> <?php _e("This slider has not slides to show. Go to the media library and start adding images to it.", "customizr" ) ?><br/>
               </p>
              <br />
            </div>
          <?php else : // render?>
            <div id="tc_slides_table">
              <div id="update-status"></div>
                  <table class="wp-list-table widefat fixed media" cellspacing="0">
                    <thead>
                        <tr>
                          <th scope="col"><?php _e( 'Slide Image' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Title' , 'customizr' ) ?></th>
                          <th scope="col" style="width: 35%"><?php _e( 'Slide Text' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Button Text' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Button Link' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Edit' , 'customizr' ) ?></th>
                        </tr>
                      </thead>
                    <tbody id="sortable">
                      <?php
                      //loop on the slides and render if the selected slider is checked
                      foreach ( $current_post_slides as $index => $slide) {
                        //get the attachment object
                        $tc_slide = get_post( $slide );

                        //check if $tc_slide object exists otherwise go to the next iteration
                        if (!isset( $tc_slide))
                          continue;

                        //check if slider is checked for this attachment => otherwise go to the next iteration
                        $slider_check_value     = esc_attr(get_post_meta( $tc_slide -> ID, $key = 'slider_check_key' , $single = true ));
                        if ( $slider_check_value == false)
                          continue;

                        //set up variables
                        $id                     = $tc_slide -> ID;
                        $slide_src              = wp_get_attachment_image_src( $id, 'thumbnail' );
                        $slide_url              = $slide_src[0];
                        $title                  = esc_attr(get_post_meta( $id, $key = 'slide_title_key' , $single = true ));
                        $text                   = esc_html(get_post_meta( $id, $key = 'slide_text_key' , $single = true ));
                        $text_color             = esc_attr(get_post_meta( $id, $key = 'slide_color_key' , $single = true ));
                        $button_text            = esc_attr(get_post_meta( $id, $key = 'slide_button_key' , $single = true ));
                        $button_link            = esc_attr(get_post_meta( $id, $key = 'slide_link_key' , $single = true ));

                        //check if $text_color is set and create an html style attribute
                        $color_style ='';
                        if( $text_color != null) {
                          $color_style = 'style="color:'.$text_color.'"';
                        }
                        ?>
                        <tr id="<?php echo $index ?>" class="ui-state-default" valign="middle">
                          <td style="vertical-align:middle" class="column-icon">
                              <?php if( $slide_url != null) : ?>
                                <img width="100" height="100" src="<?php echo $slide_url; ?>" class="attachment-80x60" alt="Hydrangeas">
                              <?php else : ?>
                                <div style="height:100px;width:100px;background:#eee;text-align:center;line-height:100px;vertical-align:middle">
                                  <?php _e( 'No Image Selected' , 'customizr' ); ?>
                                </div>
                              <?php endif; ?>
                          </td>
                          <td style="vertical-align:middle" class="">
                              <?php if( $title != null) : ?>
                                <p <?php echo $color_style ?>><strong><?php echo $title ?></strong></p>
                              <?php endif; ?>
                          </td>
                          <td style="vertical-align:middle" class="">
                               <?php if( $text != null) : ?>
                                <p <?php echo $color_style ?> class="lead"><?php echo $text ?></p>
                              <?php endif; ?>
                          </td>
                          <td style="vertical-align:middle" class="">
                              <?php if( $button_text != null) : ?>
                                <p class="btn btn-large btn-primary"><?php echo $button_text; ?></a>
                              <?php endif; ?>
                          </td>
                           <td style="vertical-align:middle" class="">
                              <?php if( $button_link != null) : ?>
                                <p class="btn btn-large btn-primary" href="<?php echo get_permalink( $button_link); ?>"><?php echo get_the_title( $button_link); ?></p>
                              <?php endif; ?>
                          </td>
                           <td style="vertical-align:middle" class="">
                              <?php if( $id != $current_attachement_id) : ?>
                                <a class="button-primary" href="<?php echo admin_url( 'post.php?post='.$id.'&action=edit' ) ?>" target="_blank"><?php _e( 'Edit this slide' , 'customizr' )?></a>
                              <?php else : ?>
                                <span style="color:#999898"><?php _e( 'Current slide' , 'customizr' )?></span>
                              <?php endif; ?>
                          </td>
                        </tr>
                        <?php
                      }//end foreach
                   echo '</tbody></table><br/>';
                   ?>
              </div><!-- #tc_slides_table -->
         <?php endif; // empty( $current_post_slides? ?>
        <?php
      }





      /*
      ----------------------------------------------------------------
      ---------------- AJAX SAVE (post and attachment) ---------------
      ----------------------------------------------------------------
      */
      /**
       * Ajax saving of options and meta fields in DB for post and attachement screens
       * works along with tc_ajax_slider.js
       * @package Customizr
       * @since Customizr 2.0
       */
      function tc_slider_ajax_save( $post_id ) {

            //We check the ajax nonce (common for post and attachment)
            if ( isset( $_POST['SliderCheckNonce']) && !wp_verify_nonce( $_POST['SliderCheckNonce'], 'tc-slider-check-nonce' ) )
                return;

            // Check permissions
            if ( !current_user_can( 'edit_post' , $post_id ) )
                return;

            // Do we have a post_id?
            if ( !isset( $_POST['tc_post_id'])) {
                return;
            }
            else {
                $post_ID = $_POST['tc_post_id'];
            }

            //OPTION FIELDS
            //get options and some useful $_POST vars
            $tc_options                 = get_option( 'tc_theme_options' );

            if (isset( $_POST['tc_post_type']))
              $tc_post_type             = esc_attr( $_POST['tc_post_type']);
            if (isset( $_POST['currentpostslider']))
              $current_post_slider      = esc_attr( $_POST['currentpostslider']);
            if (isset( $_POST['new_slider_name']))
              $new_slider_name          = esc_attr( $_POST['new_slider_name'] );

            //Save user input by looping on the fields
            foreach ( $_POST as $tckey => $tcvalue) {
                switch ( $tckey) {
                  //delete slider
                  case 'delete_slider':
                    //first we delete the meta fields related to the deleted slider
                    //which screen are we coming from?
                    if( $tc_post_type == 'attachment' ) {
                      query_posts( 'meta_key=post_slider_key&meta_value='.$current_post_slider);
                      //we loop the posts with the deleted slider meta key
                        if(have_posts()) {
                          while ( have_posts() ) : the_post();
                              //delete the post meta
                              delete_post_meta(get_the_ID(), $key = 'post_slider_key' );
                          endwhile;
                        }
                      wp_reset_query();
                    }

                    //we delete from the post/page screen
                    else {
                      $post_slider_meta = esc_attr(get_post_meta( $post_ID, $key = 'post_slider_key' , $single = true ));
                      if(!empty( $post_slider_meta)) {
                        delete_post_meta( $post_ID, $key = 'post_slider_key' );
                      }
                    }

                    //in all cases, delete DB option
                    unset( $tc_options['tc_sliders'][$current_post_slider]);
                    //update DB with new slider array
                    update_option( 'tc_theme_options' , $tc_options );
                  break;


                  //reorder slides
                  case 'newOrder':
                      //turn new order into array
                      if(!empty( $tcvalue))

                      $neworder = explode( ',' , esc_attr( $tcvalue ));

                      //initialize the newslider array
                      $newslider = array();

                      foreach ( $neworder as $new_key => $new_index) {
                          $newslider[$new_index] =  $tc_options['tc_sliders'][$current_post_slider][$new_index];
                      }

                      $tc_options['tc_sliders'][$current_post_slider] = $newslider;

                       //update DB with new slider array
                      update_option( 'tc_theme_options' , $tc_options );
                    break;




                  //sliders are added in options
                  case 'new_slider_name':
                      //check if we have something to save
                      $new_slider_name                                  = esc_attr( $tcvalue );
                      $delete_slider                                    = false;
                      if ( isset( $_POST['delete_slider']))
                          $delete_slider                                = $_POST['delete_slider'];

                      //prevent saving if we delete
                      if (!empty( $new_slider_name) && $delete_slider != true) {
                          $new_slider_name                              = wp_filter_nohtml_kses( $tcvalue );
                          //remove spaces and special char
                          $new_slider_name                              = strtolower(preg_replace("![^a-z0-9]+!i", "-", $new_slider_name));

                          $tc_options['tc_sliders'][$new_slider_name]      = array( $post_ID);
                          //adds the new slider name in DB options
                          update_option( 'tc_theme_options' , $tc_options );
                        //associate the current post with the new saved slider

                        //looks for a previous slider entry and delete it
                        foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {

                          foreach ( $slider as $key => $tc_post) {
                             //clean empty values if necessary
                             if ( is_null( $tc_options['tc_sliders'][$slider_name][$key]))
                                unset( $tc_options['tc_sliders'][$slider_name][$key]);

                             //delete previous slider entries for this post
                             if ( $tc_post == $post_ID )
                                unset( $tc_options['tc_sliders'][$slider_name][$key]);
                            }
                          }

                          //update DB with clean option table
                          update_option( 'tc_theme_options' , $tc_options );

                          //push new post value for the new slider and write in DB
                          array_push( $tc_options['tc_sliders'][$new_slider_name], $post_ID);
                          update_option( 'tc_theme_options' , $tc_options );

                        }

                    break;

                    //post slider value
                    case 'post_slider_name':
                        //check if we display the attachment screen
                        if (!isset( $_POST['slider_check_field'])) {
                          break;
                        }
                        //we are in the attachment screen and we uncheck slider options checkbox
                        elseif ( $_POST['slider_check_field'] == 0) {
                          break;
                        }

                        //if we are in the slider creation case, the selected slider has to be the new one!
                        if (!empty( $new_slider_name))
                          break;

                        //check if we have something to save
                        $post_slider_name                   = esc_attr( $tcvalue );

                        //check if we have an input and if we are not in the slider creation case
                        if (!empty( $post_slider_name)) {

                           $post_slider_name                = wp_filter_nohtml_kses( $post_slider_name );
                            //looks for a previous slider entry and delete it.
                           //Important : we check if the slider has slides first!
                              foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {
                                foreach ( $slider as $key => $tc_post) {

                                  //clean empty values if necessary
                                  if ( is_null( $tc_options['tc_sliders'][$slider_name][$key])) {
                                      unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                  }

                                  //clean slides with no images
                                  $slide_img = wp_get_attachment_image( $tc_options['tc_sliders'][$slider_name][$key]);
                                  if (isset($slide_img) && empty($slide_img)) {
                                      unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                  }

                                 //delete previous slider entries for this post
                                 if ( $tc_post == $post_ID ) {
                                    unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                  }

                                }//end for each
                              }
                              //update DB with clean option table
                              update_option( 'tc_theme_options' , $tc_options );

                            //check if the selected slider is empty and set it as array
                            if( empty( $tc_options['tc_sliders'][$post_slider_name]) ) {
                              $tc_options['tc_sliders'][$post_slider_name] = array();
                            }

                            //push new post value for the slider and write in DB
                              array_push( $tc_options['tc_sliders'][$post_slider_name], $post_ID);
                              update_option( 'tc_theme_options' , $tc_options );
                        }//end if !empty( $post_slider_name)

                        //No slider selected
                        else {
                          //looks for a previous slider entry and delete it
                            foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {
                              foreach ( $slider as $key => $tc_post) {
                                 //clean empty values if necessary
                                 if ( is_null( $tc_options['tc_sliders'][$slider_name][$key]))
                                    unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                 //delete previous slider entries for this post
                                 if ( $tc_post == $post_ID )
                                    unset( $tc_options['tc_sliders'][$slider_name][$key]);
                              }
                            }
                            //update DB with clean option table
                            update_option( 'tc_theme_options' , $tc_options );
                        }
                      break;
                  }//end switch
               }//end foreach

              //POST META FIELDS
              //set up the fields array
              $tc_slider_fields = array(
                //posts & pages
                'post_slider_name'            => 'post_slider_key' ,
                'post_slider_check_field'     => 'post_slider_check_key' ,
                //attachments
                'slider_check_field'          => 'slider_check_key' ,
                'slide_title_field'           => 'slide_title_key' ,
                'slide_text_field'            => 'slide_text_key' ,
                'slide_color_field'           => 'slide_color_key' ,
                'slide_button_field'          => 'slide_button_key' ,
                'slide_link_field'            => 'slide_link_key'
                );

                //sanitize user input by looping on the fields
                foreach ( $tc_slider_fields as $tcid => $tckey) {
                    if ( isset( $_POST[$tcid])) {
                        switch ( $tckey) {
                          //different sanitizations
                          //the slider name custom field for a post/page
                          case 'post_slider_key' :
                             $mydata = esc_attr( $_POST[$tcid] );
                             //Does the selected slider still exists in options? (we first check if the selected slider is not empty)
                             if(!empty( $mydata) && !isset( $tc_options['tc_sliders'][$mydata]))
                                break;

                             //write in DB
                              add_post_meta( $post_ID, $tckey, $mydata, true) or
                                update_post_meta( $post_ID, $tckey , $mydata);
                          break;


                          //inserted/updated in all cases
                          case 'post_slider_check_key':
                          case 'slider_check_key':
                             $mydata = esc_attr( $_POST[$tcid] );
                             //write in DB
                              add_post_meta( $post_ID, $tckey, $mydata, true) or
                                update_post_meta( $post_ID, $tckey , $mydata);

                             //check if we are in the attachment screen AND slider unchecked
                              if( $tckey == 'slider_check_key' && esc_attr( $_POST[$tcid] ) == 0) {

                                  //if we uncheck the attachement slider, looks for a previous entry and delete it.
                                  //Important : we check if the slider has slides first!
                                  if ( isset( $tc_options['tc_sliders'])) {
                                    foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {
                                      foreach ( $slider as $key => $tc_post) {
                                         //clean empty values if necessary
                                         if ( is_null( $tc_options['tc_sliders'][$slider_name][$key]))
                                            unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                         //delete previous slider entries for this post
                                         if ( $tc_post == $post_ID )
                                            unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                      }
                                    }
                                  }
                                  //update DB with clean option table
                                  update_option( 'tc_theme_options' , $tc_options );

                              }//endif;

                          break;

                          case 'slide_text_key':
                          //check if we add this attachment to a slider for the first time : do we have custom fields defined in DB and are the input fields existing in the DOM (sent by Ajax)?

                                $mydata = sanitize_text_field( $_POST[$tcid] );
                                if (strlen( $mydata) > 250) {
                                $mydata = substr( $mydata,0,strpos( $mydata, ' ' ,250));
                                $mydata = esc_html( $mydata) . ' ...';
                                }
                                else {
                                  $mydata = esc_html( $mydata);
                                }
                                 //write in DB
                                add_post_meta( $post_ID, $tckey, $mydata, true) or
                                update_post_meta( $post_ID, $tckey , $mydata);

                            break;

                          default://for button, color, title and post link field (actually not a link but an id)
                          //check if we add this attachment to a slider for the first time : do we have custom fields defined in DB and are the input fields existing in the DOM (sent by Ajax)?

                               $mydata = sanitize_text_field( $_POST[$tcid] );
                               $default_button_length = apply_filters( 'tc_slide_button_length', 80 );
                               if (strlen( $mydata) > $default_button_length) {
                                $mydata = substr( $mydata,0,strpos( $mydata, ' ' ,$default_button_length));
                                $mydata = esc_attr( $mydata) . ' ...';
                                }
                                else {
                                  $mydata = esc_attr( $mydata);
                                }
                                //write in DB
                                add_post_meta( $post_ID, $tckey, $mydata, true) or
                                update_post_meta( $post_ID, $tckey , $mydata);

                            break;
                        }//end switchendif;
                    }//end if ( isset( $_POST[$tcid])) {
                }//end foreach
            }//function






  /*
  ----------------------------------------------------------------
  -------- AJAX CALL BACK FUNCTION (post and attachment) ---------
  ----------------------------------------------------------------
  */

  /**
   * Global slider ajax call back function : 1-Saves options and fields, 2-Renders
   * Used in post or attachment context => uses post_slider var to check the context
   * Works along with tc_ajax_slider.js
   * @package Customizr
   * @since Customizr 2.0
   */
     function tc_slider_cb() {

      $nonce = $_POST['SliderCheckNonce'];
      // check if the submitted nonce matches with the generated nonce we created earlier
      if ( ! wp_verify_nonce( $nonce, 'tc-slider-check-nonce' ) ) {
        die();
      }
        Try{
        //get the post_id with the hidden input field
        $tc_post_id         = $_POST['tc_post_id'];

        //save $_POST var in DB
        $this -> tc_slider_ajax_save( $tc_post_id);

        //check if we are in the post or attachment screen and select the appropriate rendering
        //we use the post_slider var defined in tc_ajax_slider.js
        if ( isset( $_POST['tc_post_type'])) {
          if( $_POST['tc_post_type'] == 'post' ) {
            $this -> tc_get_post_slider_infos( $tc_post_id);
          }
          else {
            $this -> tc_get_attachment_slider_infos( $tc_post_id);
          }
        }
        //echo $_POST['slider_id'];
       } catch (Exception $e){
          exit;
       }
       exit;
     }






      /**
       * Loads the necessary scripts and stylesheets to display slider options
       * @package Customizr
       * @since Customizr 1.0
       */
        function tc_slider_admin_scripts( $hook) {
        global $post;
        //load scripts only for creating and editing slides options in pages and posts
        if( ( 'media.php'  == $hook)) {
            wp_enqueue_script( 'jquery-ui-sortable' );
        }
        if( ( 'post-new.php' == $hook || 'post.php' == $hook || 'media.php' == $hook) )  {
            //ajax refresh for slider options
            wp_enqueue_script( 'tc_ajax_slider' , TC_BASE_URL.'inc/admin/js/tc_ajax_slider.min.js' , array( 'jquery' ), true );

            // Tips to declare javascript variables http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/#bad-ways
            wp_localize_script( 'tc_ajax_slider' , 'SliderAjax' , array(
            // URL to wp-admin/admin-ajax.php to process the request
            //'ajaxurl'          => admin_url( 'admin-ajax.php' ),
            // generate a nonce with a unique ID "myajax-post-comment-nonce"
            // so that you can check it later when an AJAX request is sent
            'SliderNonce' => wp_create_nonce( 'tc-slider-nonce' ),

            //
            'SliderCheckNonce' => wp_create_nonce( 'tc-slider-check-nonce' ),
            )
            );

            //iphone like button style and script
            wp_enqueue_style( 'iphonecheckcss' , TC_BASE_URL.'inc/admin/css/iphonecheck.css' );
            wp_enqueue_script( 'iphonecheck' , TC_BASE_URL.'inc/admin/js/jquery.iphonecheck.js' );

            //thickbox
            wp_admin_css( 'thickbox' );
            add_thickbox();

            //sortable stuffs
            wp_enqueue_style( 'sortablecss' , TC_BASE_URL.'inc/admin/css/tc_sortable.css' );

            //wp built-in color picker style and script
           //Access the global $wp_version variable to see which version of WordPress is installed.
            global $wp_version;

            //If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
            if ( 3.5 <= $wp_version ){
                //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker' );
                 // load the minified version of custom script
              wp_enqueue_script( 'cp_demo-custom' , TC_BASE_URL.'inc/admin/js/color-picker.js' , array( 'jquery' , 'wp-color-picker' ), true );
            }
            //If the WordPress version is less than 3.5 load the older farbtasic color picker.
            else {
                //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                wp_enqueue_style( 'farbtastic' );
                wp_enqueue_script( 'farbtastic' );
                // load the minified version of custom script
              wp_enqueue_script( 'cp_demo-custom' , TC_BASE_URL.'inc/admin/js/color-picker.js' , array( 'jquery' , 'farbtastic' ), true );
            }
        }//end post type hook check
      }




  /*
  ----------------------------------------------------------------
  ------------- ATTACHMENT FIELDS FILTER IF WP < 3.5 -------------
  ----------------------------------------------------------------
  */
      function tc_attachment_filter( $form_fields, $post = null) {
          $this -> tc_attachment_slider_box ( $post);
           return $form_fields;
      }


      function tc_attachment_save_filter( $post, $attachment ) {
          if ( isset( $_POST['tc_post_id']))
           $postid = $_POST['tc_post_id'];

          $this -> tc_slide_save( $postid );

          return $post;
      }
  }//end of class
endif;