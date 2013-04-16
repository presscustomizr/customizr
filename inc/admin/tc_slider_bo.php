<?php
if ( ! function_exists( 'tc_slider_type_register' ) ) :
add_action('init', 'tc_slide_type_register');
/**
 * Creates a Slider based on the custom post type slider entries
 * @package Customizr
 * @since Customizr 1.0 
 */
    function tc_slide_type_register() 
    {
      // Add new post type
      $labels = array(
        'name'                 => __('Slides', 'post type general name','customizr'),
        'singular_name'        => __('Slide', 'post type singular name','customizr'),
        'add_new'              => __('Add New', 'Slide','customizr'),
        'add_new_item'         => __('Add New Slide','customizr'),
        'edit_item'            => __('Edit Slide','customizr'),
        'new_item'             => __('New Slide','customizr'),
        'view_item'            => __('View Slide','customizr'),
        'search_items'         => __('Search Slides','customizr'),
        'not_found'            => __('No Slides found','customizr'),
        'not_found_in_trash'   => __('No Slides found in Trash','customizr'), 
        'parent_item_colon'    => ''
      );

      $args = array(
        'labels'               => $labels,
        'public'               => true,
        'show_ui'              => true,
        'capability_type'      => 'post',
        'exclude_from_search'  => true,
        'hierarchical'         => false,
        'rewrite'              => false,//array('slug'=>'slide','with_front'=>true),
        'query_var'            => true,
        'show_in_nav_menus'    => false,
        'supports'             => array('title','thumbnail'),
        'menu_icon'            => TC_BASE_URL.'inc/admin/img/slides.png'
      );
      
      register_post_type( 'slide' , $args );
      
       // Add new taxonomy, make it hierarchical (like categories)
      $labels_tax = array(
        'name'                  => __( 'Slider', 'customizr'),
        'singular_name'         => __( 'Slider', 'customizr'),
        'search_items'          => __( 'Search Slider','customizr'),
        'all_items'             => __( 'All Slider','customizr'),
        'parent_item'           => __( 'Parent Slider','customizr'),
        'parent_item_colon'     => __( 'Parent Slider:','customizr'),
        'edit_item'             => __( 'Edit Slider','customizr'), 
        'update_item'           => __( 'Update Slider','customizr'),
        'add_new_item'          => __( 'Add New Slider','customizr'),
        'new_item_name'         => __( 'New Slider','customizr'),
        'menu_name'             => __( 'Slider','customizr' )
      );  

      $args_tax = array(
        'hierarchical'          => true,
        'labels'                => $labels_tax,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => false//array( 'slug' => 'sliders' )
      );

      register_taxonomy( 'slider', array( 'slide' ), $args_tax);
    }
endif;



if ( ! function_exists( 'slide_edit_columns' ) ) :
/**
 * 
 * @package Customizr
 * @since Customizr 1.0
 */
add_filter("manage_edit-slide_columns", "slide_edit_columns");
  function slide_edit_columns($columns)
  {
    $newcolumns = array(
      "image"         => __("Slide Image", 'customizr' ),
      "text"          => __("Slide Text", 'customizr' ),
      "button_text"   => __("Button Text", 'customizr' ),
      "button_link"   => __("Button Link", 'customizr' ),
    );
    
    $columns= array_merge($columns, $newcolumns);
    
    return $columns;
  }
endif;

if ( ! function_exists( 'slide_custom_columns' ) ) :
add_action("manage_posts_custom_column",  "slide_custom_columns");
/**
 * 
 * @package Customizr
 * @since Customizr 1.0
 */
  function slide_custom_columns($column)
  {
    global $post;
    switch ($column)
    {
      case "image":
      if (has_post_thumbnail($post->ID)){
          echo get_the_post_thumbnail($post->ID, 'thumbnail');
        }
      break;
      
      case "text":
        echo esc_attr(get_post_meta( $post -> ID, $key = 'slide_text_key', $single = true ));
      break;

      case "button_text":
        echo esc_attr(get_post_meta( $post -> ID, $key = 'slide_button_key', $single = true ));
      break;

      case "button_link":
        $link_id = get_post_meta( $post -> ID, $key = 'slide_link_key', $single = true );
        if($link_id != null) {
          echo '<a href="'.get_edit_post_link( $link_id).'" title="'.get_the_title( $link_id ).'">'.get_the_title( $link_id ).'</a>';
        }
        else {
          _e( 'No link', 'customizr' );
        }
      break;
    }
  }
endif;




if ( ! function_exists( 'slide_boxes' ) ) :
/**
 * Adds options metaboxes to slider custom post type
 * @package Customizr
 * @since Customizr 1.0
 */
/* Define the custom box */
add_action( 'add_meta_boxes', 'slide_boxes' );
  function slide_boxes() {
      $screens = array( 'slide' );
      foreach ($screens as $screen) {
          add_meta_box(
              'slide_help_sectionid',
              __( 'Slide Help', 'customizr' ),
              'slide_help_inner_custom_box',
              $screen
          );
          add_meta_box(
              'slide_sectionid',
              __( 'Slide Options', 'customizr' ),
              'slide_inner_custom_box',
              $screen
          );
      }
  }
endif;




if (!function_exists( 'slide_help_inner_custom_box' ) ) :
/**
 * Prints the box content
 * @package Customizr
 * @since Customizr 1.0
 */
    function slide_help_inner_custom_box( $post ) {
          ?>
          <div class="meta-box-item-content">
              <p><strong><?php _e('1 - Choose a featured image', 'customizr' ); ?></strong><br />
                <i><?php _e('Recommended minimum height : 500 px.','customizr') ?></i>
              </p>
              <p><strong><?php _e('2 - Set the options', 'customizr' ); ?></strong><br />
                <i><?php _e('All slide options are optional. Please note that you can hide the title if you only want to display an image.','customizr') ?></i>
              </p>
              <p><strong><?php _e('3 - Attach the slide to a slider', 'customizr' ); ?></strong><br />
                <i><?php _e('Check a slider (create one if needed). To be displayed on front office, a slide must be attached to a slider. Once created, the slider list is then available in all your posts or pages, and in the customizer screen for the home page.<br />','customizr') ?></i>
              </p>
          </div>
       <?php
    }
endif;




if (!function_exists( 'slide_inner_custom_box' ) ) :
/**
 * Prints the box content
 * @package Customizr
 * @since Customizr 1.0
 */
    /* Prints the box content */
    function slide_inner_custom_box( $post ) {
          // Use nonce for verification
          wp_nonce_field( plugin_basename( __FILE__ ), 'slide_noncename' );
          
          //title check field setup
          $title_id       = 'slide_title_field';
          $title_value    = get_post_meta( $post -> ID, $key = 'slide_title_key', $single = true );

          //text_field setup : sanitize and limit length
          $text_id        = 'slide_text_field';
          $text_value     = get_post_meta( $post -> ID, $key = 'slide_text_key', $single = true );
          if (strlen($text_value) > 250) {
            $text_value = substr($text_value,0,strpos($text_value,' ',250));
            $text_value = esc_textarea($text_value) . ' ...';
          }
          else {
            $text_value = esc_textarea($text_value);
          }

           //Color field setup
          $color_id       = 'slide_color_field';
          $color_value    = get_post_meta( $post -> ID, $key = 'slide_color_key', $single = true );
          
          //button field setup
          $button_id      = 'slide_button_field';
          $button_value   = get_post_meta( $post -> ID, $key = 'slide_button_key', $single = true );
           if (strlen($button_value) > 80) {
            $button_value = substr($button_value,0,strpos($button_value,' ',80));
            $button_value = esc_textarea($button_value) . ' ...';
          }
          else {
            $button_value = esc_textarea($button_value);
          }

          //link field setup
          $link_id        = 'slide_link_field';
          $link_value     = get_post_meta( $post -> ID, $key = 'slide_link_key', $single = true );

          //retrieve post, pages and custom post types (if any) and generate the ordered select list for the button link
          $post_types=get_post_types(array('public' => true));
          $excludes = array('attachment','slide');

          foreach ($post_types as $t) {
              if (!in_array($t, $excludes)) {
               //get the posts a tab of types
               $tc_all_posts[$t] = get_posts(  array(
                  'numberposts'    =>  100,
                  'orderby'      =>  'date',
                  'order'        =>  'DESC',
                  'post_type'      =>  $t,
                  'post_status'    =>  'publish' )
                );
              }
            };
          ?>
          <input type="hidden" name="tc_hidden_flag" value="true" />
          <div class="meta-box-item-title">
              <h4><?php _e('Show title', 'customizr' ); ?></h4>
                <label for="<?php echo $title_id; ?>">
              </i><?php _e('The title displayed on front office is the main slide title above. You can choose to hide it here if you just need an image slide.','customizr') ?></i>
            </label>
          </div>
          <div class="meta-box-item-content">
            <input name="<?php echo $title_id; ?>" type="hidden" value="0"/>
            <input name="<?php echo $title_id; ?>" id="<?php echo $title_id; ?>" type="checkbox" class="iphonecheck" value="1"<?php if ($title_value==null || $title_value==1) {echo 'checked="checked"';} else {echo '';} ?>/>
          </div>
          <div class="meta-box-item-title">
              <h4><?php _e('Description text (below the title, 250 car. max length)', 'customizr' ); ?></h4>
          </div>
          <div class="meta-box-item-content">
              <textarea name="<?php echo $text_id; ?>" id="<?php echo $text_id; ?>" style="width:50%"><?php echo $text_value; ?></textarea>
          </div>
          
           <div class="meta-box-item-title">
              <h4><?php _e("Title and text color", 'customizr' );  ?></h4>
          </div>
          <div class="meta-box-item-content">
              <input id="<?php echo $color_id; ?>" name="<?php echo $color_id; ?>" value="<?php echo $color_value; ?>"/>
              <div id="colorpicker"></div>
          </div>

           <div class="meta-box-item-title">
              <h4><?php _e('Button text (80 car. max length)', 'customizr' ); ?></h4>
          </div>
          <div class="meta-box-item-content">
              <input class="widefat" name="<?php echo $button_id; ?>" id="<?php echo $button_id; ?>" value="<?php echo $button_value; ?>" style="width:50%">
          </div>

          <div class="meta-box-item-title">
              <h4><?php _e("Choose a linked page or post (among the last 100).", 'customizr' ); ?></h4>
          </div>
          <div class="meta-box-item-content">
              <select name="<?php echo $link_id; ?>" id="<?php echo $link_id; ?>">
                <?php //no link option ?>
                <option value="" <?php if(isset($link_value) && $link_value == null) {echo 'selected=selected';} ?>> <?php _e( 'No link', 'customizr' ); ?></option>
                <?php foreach($tc_all_posts as $type) : ?>
                    <?php foreach ($type as $key => $item) : ?>
                  <option value="<?php echo $item -> ID; ?>" <?php if(isset($link_value) && $link_value == $item -> ID) {echo 'selected=selected';} ?>>{<?php echo $item -> post_type;?>}&nbsp;<?php echo $item -> post_title; ?></option>
                    <?php endforeach; ?>
               <?php endforeach; ?>
              </select><br />
          </div>
          
         <?php //check if there is at least on slider name created
           $have_sliders = get_terms( 'slider', 'number=1&orderby=count&hide_empty=0' );
           $slide_id = $post -> ID;
            $any_slider_check = false;
            if($have_sliders)
              $any_slider_check = true;

            //get the selected slider object
            $have_slider = wp_get_post_terms( $slide_id, 'slider');

            $slide_slider_check = false;
            if($have_slider)
              $slide_slider_check = true;

            //Beginning of the slides table rendering
          ?>
          <div id="tc_slider_infos">
              <?php if(!$any_slider_check) {
                 echo '<div style="width:99%; padding: 5px;">';
                    echo '<p class="description">'.__("You haven't create any slider yet. Click on the button to add you first slider. Once created, just add any slides you need to it.", "customizr" ).'<br/><br/><a class="button-primary" href="'.admin_url( 'edit-tags.php?taxonomy=slider&post_type=slide').'" target="_blank">'.__('Create a slider','customizr').'</a></p>
                </div>';
              }
              elseif(!$slide_slider_check) {//check if there is at least one slide attached to the selected slider 
                echo '<div style="width:99%; padding: 5px;" class="updated below-h2">';
                    echo '<p>'.__("This slide has not yet been attached to any slider. Check at least one slider in the right sidebar of this screen and refresh page.", "customizr" ).'</p>
                </div>';
              }
              ?>
            </div><!--#tc_slider_infos-->
       <?php
    }
endif;





if (!function_exists( 'slide_save_postdata' ) ) :
add_action( 'save_post', 'slide_save_postdata' );
/**
 * When the post is saved, saves our custom data
 * @package Customizr
 * @since Customizr 1.0
 */
    function slide_save_postdata( $post_id ) {
      // verify if this is an auto save routine. 
      // If it is our form has not been submitted, so we dont want to do anything
      
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;

      //handle the case when the custom post is quick edited => otherwise, all custom metas fields are cleared out
      if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
          return;

      // verify this came from the our screen and with proper authorization,
      // because save_post can be triggered at other times

      if ( isset($_POST['slide_noncename']) && !wp_verify_nonce( $_POST['slide_noncename'], plugin_basename( __FILE__ ) ) )
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
      $tc_slide_fields = array(
          'slide_title_field'   => 'slide_title_key',
          'slide_text_field'    => 'slide_text_key',
          'slide_button_field'  => 'slide_button_key',
          'slide_link_field'    => 'slide_link_key',
          'slide_color_field'   => 'slide_color_key'
          );

      //if saving in a custom table, get post_ID
     if ( isset($_POST['post_ID'])) {
        $post_ID = $_POST['post_ID'];
        //sanitize user input by looping on the fields
        foreach ($tc_slide_fields as $tcid => $tckey) {

            $mydata = sanitize_text_field( $_POST[$tcid] );
            //Limit number of car. for text and button
            switch ($tcid) {
              case 'slide_text_field':
                  if (strlen($mydata) > 250) {
                  $mydata = substr($mydata,0,strpos($mydata,' ',250));
                  $mydata = esc_textarea($mydata) . ' ...';
                  }
                  else {
                    $mydata = esc_textarea($mydata);
                  }
                break;
              
              case 'slide_button_field':
                  if (strlen($mydata) > 80) {
                  $mydata = substr($mydata,0,strpos($mydata,' ',80));
                  $mydata = esc_textarea($mydata) . ' ...';
                  }
                  else {
                    $mydata = esc_textarea($mydata);
                  }
                break;
            }
            
            add_post_meta($post_ID, $tckey, $mydata, true) or
              update_post_meta($post_ID, $tckey , $mydata);
           }
        }
    }
endif;



if (!function_exists( 'tc_save_slide_check' ) ) :
add_action( 'admin_notices', 'tc_save_slide_check' );
/**
 * Add admin notifications : check the slide post on save and display error message if needed
 * @package Customizr
 * @since Customizr 1.0
 */

    function tc_save_slide_check($message, $errormsg = false) {
       global $post;
       $active_status = array('publish','future','draft','private','pending');
       if(isset($post) && $post -> post_type == 'slide' && in_array($post -> post_status, $active_status) ) {
         $slide_id = $post -> ID;

          //get the selected slider object
          $have_slider = wp_get_post_terms( $slide_id, 'slider');

          //if the slide has not been attached to any slider
          if (empty($have_slider)) {
             echo '<div class="error"><p>'.__('This slide has not been attached to any slider! You must check a slider name (create one if needed) in the "Slider Names" box of this screen, and update the slide.','customizr').'</p></div>';
          }

          //if the slide has no image
          if (!has_post_thumbnail( $slide_id )) {
              echo '<div class="error"><p>'.__('This slide has no image! Choose a featured image in the dedicated box of this screen.','customizr').'</p></div>';
          }
       }
    }
endif;




if ( ! function_exists( 'post_slider_box' ) ) :
add_action( 'add_meta_boxes', 'post_slider_box' );
/**
 * Adds a box to the main column on the Post and Page edit screens
 * @package Customizr
 * @since Customizr 1.0
 */
    function post_slider_box() {
        $screens = array( 'page', 'post' );
        foreach ($screens as $screen) {
            add_meta_box(
                'slider_sectionid',
                __( 'Slider Options', 'customizr' ),
                'post_slider_inner_custom_box',
                $screen
            );
        }
    }
endif;


if ( ! function_exists( 'post_slider_inner_custom_box' ) ) :
/**
 * Prints the box content
 * @package Customizr
 * @since Customizr 1.0
 */
  function post_slider_inner_custom_box( $post ) {
        // Use nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'post_slider_noncename' );

        // The actual fields for data entry
        // Use get_post_meta to retrieve an existing value from the database and use the value for the form
        //slider name setup
        $name_id        = 'slider_name_field';
        $name_value     = get_post_meta( $post -> ID, $key = 'slider_name_key', $single = true );
        
        //Delay field setup
        $delay_id      = 'slider_delay_field';
        $delay_value   = get_post_meta( $post -> ID, $key = 'slider_delay_key', $single = true );

        //Layout field setup
        $layout_id      = 'slider_layout_field';
        $layout_value   = get_post_meta( $post -> ID, $key = 'slider_layout_key', $single = true );

        //get the slider names and generate the select list
        $slider_names = get_terms( 'slider', 'orderby=count&hide_empty=0' );

        ?>
        
        <div class="meta-box-item-title">
            <h4><?php _e("Choose a slider", 'customizr' ); ?></h4>
        </div>
        <div class="meta-box-item-content">
          <select name="<?php echo $name_id; ?>" id="<?php echo $name_id; ?>">
              <?php //no slider option ?>
              <option value="" <?php if(isset($name_value) && $name_value == null) {echo 'selected=selected';} ?>> <?php _e( 'No slider selected', 'customizr' ); ?></option>
              <?php foreach($slider_names as $tc_name) : ?>
                <option value="<?php echo $tc_name -> term_id; ?>" <?php if(isset($name_value) && $name_value == $tc_name -> term_id) {echo 'selected=selected';} ?>><?php echo $tc_name -> name; ?></option>
             <?php endforeach; ?>
          </select>
            <br />
            <?php tc_get_slider_infos($name_value) ?>  

        </div>

        <div class="meta-box-item-title">
            <h4><?php _e("Delay between each slides in milliseconds (default : 5000 ms)", 'customizr' ); ?></h4>
        </div>
        <div class="meta-box-item-content">
            <input name="<?php echo $delay_id ; ?>" id="<?php echo $delay_id; ?>" value="<?php if (empty($delay_value)) { echo '5000';} else {echo $delay_value;} ?>"/>
        </div>

        <div class="meta-box-item-title">
            <h4><?php _e("Slider Layout : set the slider in full width", 'customizr' );  ?></h4>
        </div>
        <div class="meta-box-item-content">
            <input name="<?php echo $layout_id; ?>" type="hidden" value="0"/>
            <input name="<?php echo $layout_id; ?>" id="<?php echo $layout_id; ?>" type="checkbox" class="iphonecheck" value="1"<?php if ($layout_value==null || $layout_value==1) {echo 'checked="checked"';} else {echo '';} ?>/>
        </div>

      <?php
    }
endif;



if ( ! function_exists( 'post_slider_save_postdata' ) ) :
add_action( 'save_post', 'post_slider_save_postdata' );
/**
 * When the post is saved, saves our custom data 
 * @package Customizr
 * @since Customizr 1.0
 */
    function post_slider_save_postdata( $post_id ) {
      // verify if this is an auto save routine. 
      // If it is our form has not been submitted, so we dont want to do anything
      
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;

      // verify this came from the our screen and with proper authorization,
      // because save_post can be triggered at other times

      if ( isset($_POST['post_slider_noncename']) && !wp_verify_nonce( $_POST['post_slider_noncename'], plugin_basename( __FILE__ ) ) )
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
          'slider_name_field'     => 'slider_name_key',
          'slider_delay_field'    => 'slider_delay_key',
          'slider_layout_field'   => 'slider_layout_key',
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



if ( ! function_exists( 'tc_get_slider_infos' ) ) :
/**
 * Display slider table
 * @package Customizr
 * @since Customizr 1.0
 */
    function tc_get_slider_infos ($name_value) {
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

        //check if there is at least one slide attached to the selected slider name
        $have_slides_in_slider = get_posts(  array(
          'numberposts'    =>  10,//up to 10 slides to show in admin
          //'offset'      =>  0,
          'tax_query' => array(
                            array(
                              'taxonomy' => 'slider',
                              'field' => 'id',
                              'terms' => $name_value
                            )
                          ),
          'post_type'      =>  'slide',
          'post_status'    =>  'publish' )
        );
        $slider_slide_check = false;
        if($have_slides_in_slider)
          $slider_slide_check = true;

          //Beginning of the slides table rendering
          echo  '<div id="tc_slider_infos">';
            if(!$slider_check) {
               echo '<div style="width:99%; padding: 5px;">';
                  echo '<p class="description">'.__("You haven't create any slider yet. Click on the button to add you first slider. Once created, just add any slides you need to it.", "customizr" ).'<br/><br/><a class="button-primary" href="'.admin_url( 'edit-tags.php?taxonomy=slider&post_type=slide').'" target="_blank">'.__('Create a slider','customizr').'</a></p>
              </div>';
            }
            elseif($name_value != null && !$slider_slide_check) {//check if there is at least one slide attached to the selected slider 
              echo '<div style="width:99%; padding: 5px;" class="updated below-h2">';
                  echo '<p>'.__("This slider has no slides attached. Click on the button to attach slides to it.", "customizr" ).'&nbsp;<a class="button-primary" href="'.admin_url( 'edit.php?post_type=slide').'" target="_blank">'.__('Attach slides','customizr').'</a></p>
              </div>';
            }
            elseif ($name_value != null) {
              //construction du tableau des slides
              if($have_slides_in_slider) {
                ?>
                <table class="wp-list-table widefat fixed media" cellspacing="0">
                  <thead>
                      <tr>
                        <th scope="col"><?php _e('Slide Image','customizr') ?></th>
                        <th scope="col"><?php _e('Title','customizr') ?></th>
                        <th scope="col" style="width: 35%"><?php _e('Slide Text','customizr') ?></th>
                        <th scope="col"><?php _e('Button Text','customizr') ?></th>
                        <th scope="col"><?php _e('Button Link','customizr') ?></th>
                        <th scope="col"><?php _e('Edit','customizr') ?></th>
                      </tr>
                    </thead>
                  <tbody id="the-list">
                    <?php
                    foreach ($have_slides_in_slider as $tc_slide) { 
                      //set up variables
                      $id = $tc_slide -> ID;
                      $slide_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id = $id ), 'thumbnail');
                      $slide_url = $slide_src[0];
                      $title        = get_the_title( $id );
                      $text         = get_post_meta( $id, $key = 'slide_text_key', $single = true );
                      $text_color   = get_post_meta( $id, $key = 'slide_color_key', $single = true );
                      $button_text  = get_post_meta( $id, $key = 'slide_button_key', $single = true );
                      $button_link  = get_post_meta( $id, $key = 'slide_link_key', $single = true );

                      //check if $text_color is set and create an html style attribute
                      $color_style ='';
                      if($text_color != null) {
                        $color_style = 'style="color:'.$text_color.'"';
                      }

                      ?>
                      <tr valign="middle">
                        <td style="vertical-align:middle" class="column-icon">
                            <?php if($slide_url != null) : ?>
                              <img width="100" height="100" src="<?php echo $slide_url; ?>" class="attachment-80x60" alt="Hydrangeas">     
                            <?php else : ?>
                              <div style="height:100px;width:100px;background:#eee;text-align:center;line-height:100px;vertical-align:middle">
                                <?php _e('No Image Selected','customizr'); ?>
                              </div>
                            <?php endif; ?>
                        </td>
                        <td style="vertical-align:middle" class="">
                            <?php if($title != null) : ?>
                              <p <?php echo $color_style ?>><strong><?php echo $title ?></strong></p>
                            <?php endif; ?>    
                        </td>
                        <td style="vertical-align:middle" class="">
                             <?php if($text != null) : ?>
                              <p <?php echo $color_style ?> class="lead"><?php echo $text ?></p>
                            <?php endif; ?>      
                        </td>
                        <td style="vertical-align:middle" class="">
                            <?php if($button_text != null) : ?>
                              <p class="btn btn-large btn-primary"><?php echo $button_text; ?></a>
                            <?php endif; ?>   
                        </td>
                         <td style="vertical-align:middle" class="">
                            <?php if($button_link != null) : ?>
                              <p class="btn btn-large btn-primary" href="<?php echo get_permalink($button_link); ?>"><?php echo get_the_title($button_link); ?></p>
                            <?php endif; ?>   
                        </td>
                         <td style="vertical-align:middle" class="">
                            <a class="button-primary" href="<?php echo admin_url( 'post.php?post='.$id.'&action=edit') ?>" target="_blank"><?php _e('Edit this slide','customizr')?></a>
                        </td>
                      </tr>
                      <?php
                    }//end foreach
                 echo '</tbody></table><br/>';
              }//end if $have_slides_in_slider

             //we display an edit link to the slider name
              echo '<a class="button-primary" href="'.admin_url( 'edit.php?slider='.$slider -> slug.'&post_type=slide').'" title="'.__( 'Edit Slider :','customizr' ).$slider -> name.'" target="_blank">'.__( 'Edit Slider : ', 'customizr' ).'<strong>'.$slider -> name.'</strong></a>';
            
            }//end if name_value !=null
       echo '</div>';//#tc_slider_infos
    }
endif;




if ( ! function_exists( 'tc_slider_admin_scripts' ) ) :
add_action('admin_enqueue_scripts', 'tc_slider_admin_scripts');
/**
 * Loads the necessary scripts and stylesheets to display slider options
 * @package Customizr
 * @since Customizr 1.0
 */
    function tc_slider_admin_scripts($hook) {
      global $post;
      //load scripts only for creating and editing slides options in pages and posts
      if( ('post-new.php' == $hook || 'post.php' == $hook) && 'slide' != $post->post_type)  {
          //ajax refresh for slider options
          wp_enqueue_script( 'tc_ajax_slider', TC_BASE_URL.'inc/admin/js/tc_ajax_slider.js', array( 'jquery'), true );
          
          // Tips to declare javascript variables http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/#bad-ways
          wp_localize_script( 'tc_ajax_slider', 'SliderAjax', array(
          // URL to wp-admin/admin-ajax.php to process the request
          //'ajaxurl'          => admin_url( 'admin-ajax.php' ),
          // generate a nonce with a unique ID "myajax-post-comment-nonce"
          // so that you can check it later when an AJAX request is sent
          'SliderAjaxNonce' => wp_create_nonce( 'tc-ajax-slider-nonce' ),
          )
          );

          //iphone like button style and script
          wp_enqueue_style('admincss', TC_BASE_URL.'inc/admin/css/iphonecheck.css');
          wp_enqueue_script('iphonecheck', TC_BASE_URL.'inc/admin/js/jquery.iphonecheck.js');
      }


      //load scripts only for creating and editing slides
      elseif (('post-new.php' == $hook || 'post.php' == $hook) && 'slide' == $post->post_type) {
          //wp built-in color picker style and script
         //Access the global $wp_version variable to see which version of WordPress is installed.
          global $wp_version;
       
          //If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
          if ( 3.5 <= $wp_version ){
              //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
              wp_enqueue_style( 'wp-color-picker' );
              wp_enqueue_script( 'wp-color-picker' );
               // load the minified version of custom script
            wp_enqueue_script( 'cp_demo-custom', TC_BASE_URL.'inc/admin/js/color-picker.js', array( 'jquery', 'wp-color-picker' ), true );
          }
          //If the WordPress version is less than 3.5 load the older farbtasic color picker.
          else {
              //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
              wp_enqueue_style( 'farbtastic' );
              wp_enqueue_script( 'farbtastic' );
              // load the minified version of custom script
            wp_enqueue_script( 'cp_demo-custom', TC_BASE_URL.'inc/admin/js/color-picker.js', array( 'jquery', 'farbtastic' ), true );
          }

          //iphone like button style and script
          wp_enqueue_style('admincss', TC_BASE_URL.'inc/admin/css/iphonecheck.css');
          wp_enqueue_script('iphonecheck', TC_BASE_URL.'inc/admin/js/jquery.iphonecheck.js');
      }//end post type hook check
    }
endif;





if ( ! function_exists( 'tc_slider_action_call_back' ) ) :
add_action('wp_ajax_tc_slider_action', 'tc_slider_action_call_back');
/**
 * Slider table call back function
 * @package Customizr
 * @since Customizr 1.0
 */
   function tc_slider_action_call_back() {
    $nonce = $_POST['SliderAjaxNonce'];
    // check to see if the submitted nonce matches with the generated nonce we created earlier
    if ( ! wp_verify_nonce( $nonce, 'tc-ajax-slider-nonce' ) )
      die();
      Try{
      //echo $_POST['slider_id'];
      tc_get_slider_infos ($name_value = $_POST['slider_id']);
      //echo $_POST['slider_id'];
     } catch (Exception $e){  
        exit;  
     }
     exit;
   };
endif;