<?php
if ( ! function_exists( 'post_layout_box' ) ) :
add_action( 'add_meta_boxes', 'post_layout_box' );
/**
 * Add a layout metabox to pages and posts
 * @package Customizr
 * @since Customizr 1.0
 */
    function post_layout_box() {//id, title, callback, post_type, context, priority, callback_args
        $screens = array( 'page', 'post' );
        foreach ($screens as $screen) {
            add_meta_box(
                'layout_sectionid',
                __( 'Layout Options', 'customizr' ),
                'post_layout_inner_custom_box',
                $screen,
                'side',
                'high'
            );
        }
    }
endif;





if ( ! function_exists( 'post_layout_inner_custom_box' ) ) :
/**
 * Prints the box content 
 * @package Customizr
 * @since Customizr 1.0
 */
    function post_layout_inner_custom_box( $post ) {
          // Use nonce for verification
          wp_nonce_field( plugin_basename( __FILE__ ), 'post_layout_noncename' );

          // The actual fields for data entry
          // Use get_post_meta to retrieve an existing value from the database and use the value for the form
          //Layout name setup
          $layout_id        = 'layout_field';

          $layout_value     = get_post_meta( $post -> ID, $key = 'layout_key', $single = true );

          //Layouts select list array
          $layouts = array (
              'r' => __('Right sidebar','customizr'),
              'l' => __('Left sidebar','customizr'),
              'b' => __('Two sidebars','customizr'),
              'f' => __('Full Width','customizr'),
            );
          //by default we apply the global default layout
            $tc_sidebar_default_layout = tc_get_options('tc_sidebar_global_layout');
          if ($post->post_type == 'post')
            $tc_sidebar_default_layout = tc_get_options('tc_sidebar_post_layout');
          if ($post->post_type == 'page')
            $tc_sidebar_default_layout = tc_get_options('tc_sidebar_page_layout');

          //check if the 'force default layout' option is checked
          $force_layout = tc_get_options('tc_sidebar_force_layout');


          ?>
          <div class="meta-box-item-content">
            <?php if($layout_value == null) : ?>
              <p><?php printf(__('Default %1$s layout is set to : %2$s', 'customizr'), $post -> post_type == 'page' ? __('pages','customizr'):__('posts','customizr'),'<strong>'.$layouts[$tc_sidebar_default_layout].'</strong>') ?></p>
            <?php endif; ?>

            <?php if ($force_layout == 1) :?>
            <div style="width:99%; padding: 5px;">
              <p><i><?php _e('You have checked the <i>"Force global default layout for all posts and pages"</i>, you must unchecked this option to enable a specific layout for this post.', 'customizr' ); ?></i></p>
              <p><a class="button-primary" href="<?php echo admin_url( 'customize.php'); ?>" target="_blank"><?php _e('Change layout options','customizr') ?></a></p>
            </div>
            
            <?php else : ?>
                <i><?php printf(__('You can define a specific layout for %1$s by using the pre-defined left and right sidebars. The default layouts can be defined in the WordPress customizer screen %2$s.<br />','customizr'),
                  $post -> post_type == 'page' ? __('this page','customizr'):__('this post','customizr'),
                  '<a href="'.admin_url( 'customize.php').'" target="_blank">'.__('here','customizr').'</a>'
                  ); ?>
                </i>
                <h4><?php printf(__('Select a specific layout for %1$s', 'customizr' ),
                $post -> post_type == 'page' ? __('this page','customizr'):__('this post','customizr')); ?></h4>
                <select name="<?php echo $layout_id; ?>" id="<?php echo $layout_id; ?>">
                <?php //no layout selected ?>
                  <option value="" <?php if(isset($layout_value) && $layout_value == null) {echo 'selected=selected';} ?>> <?php printf(__( 'Default layout %1s', 'customizr' ),
                       '('.$layouts[$tc_sidebar_default_layout].')'
                       );
                    ?></option>
                  <?php foreach($layouts as $key => $l) : ?>
                    <option value="<?php echo $key; ?>" <?php if(isset($layout_value) && $layout_value == $key) {echo 'selected=selected';} ?>><?php echo $l; ?></option>
                 <?php endforeach; ?>
                </select>
           <?php endif; ?>
        </div>
          
        <?php
    }
endif;





if ( ! function_exists( 'post_layout_save_postdata' ) ) :
add_action( 'save_post', 'post_layout_save_postdata' );
/**
 * When the post is saved, saves our custom data 
 * @package Customizr
 * @since Customizr 1.0
 */
    function post_layout_save_postdata( $post_id ) {
      // verify if this is an auto save routine. 
      // If it is our form has not been submitted, so we dont want to do anything
      
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;

      // verify this came from the our screen and with proper authorization,
      // because save_post can be triggered at other times

      if ( isset($_POST['post_layout_noncename']) && !wp_verify_nonce( $_POST['post_layout_noncename'], plugin_basename( __FILE__ ) ) )
          return;
      
      // Check permissions
      if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) 
      {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
      }
      else
      {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
      }

      // OK, we're authenticated: we need to find and save the data

      //set up the fields array
      $tc_slider_fields = array(
          'layout_field'     => 'layout_key',
          );

      //if saving in a custom table, get post_ID
     if ( isset($_POST['post_ID'])) {
        $post_ID = $_POST['post_ID'];
        //sanitize user input by looping on the fields
        foreach ($tc_slider_fields as $tcid => $tckey) {

            $mydata = sanitize_text_field( $_POST[$tcid] );

            // Do something with $mydata 
            // either using 
            add_post_meta($post_ID, $tckey, $mydata, true) or
              update_post_meta($post_ID, $tckey , $mydata);
            // or a custom table (see Further Reading section below)
           }
        }
    }
endif;
