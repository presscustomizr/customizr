<?php
class TC_attachment_content_model_class extends TC_post_page_content_model_class {
  public $gallery;
  public $has_gallery;
  public $link_url;
  public $link_rel;
  public $attachment_size;
  public $attachment_class;

  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );
    //inside the loop but before rendering set some properties
    add_action( $model['hook'], array( $this, 'on_this_rendering' ), 0 );
  }

  function on_this_rendering() {
    global $post;

    $gallery     = '';
    $has_gallery = false;
    $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit' , 'post_type' => 'attachment' , 'post_mime_type' => 'image' , 'order' => 'ASC' , 'orderby' => 'menu_order ID' ) ) );

    //did we activate the fancy box in customizer?
    $tc_fancybox = esc_attr( TC_utils::$inst->tc_opt( 'tc_fancybox' ) );

    if ( 0 == $tc_fancybox ) { //fancy box not checked!
      /**
      * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
      * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
      */
      foreach ( $attachments as $k => $attachment )
        if ( $attachment->ID == $post->ID )
            break;
    
      $k++;

      // If there is more than 1 attachment in a gallery
      if ( count( $attachments ) > 1 ) {
        if ( isset( $attachments[ $k ] ) ) {
          // get the URL of the next image attachment
          $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
        }
        else {
          // or get the URL of the first image attachment
          $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
        }
      }
      else {
        // or, if there's only 1 image, get the URL of the image
        $next_attachment_url = wp_get_attachment_url();
      }

      $link_url          = esc_url( $next_attachment_url );
      $link_rel          = 'attachment';
      $attachemnt_class  = 'attachment';
    } else {// if fancybox option checked 
    
      $attachment_infos       = wp_get_attachment_image_src( $post->ID , 'large' );
      $attachment_src         = $attachment_infos[0];
      $attachment_class       = 'grouped_elements';

      $link_url    =  esc_url( $attachment_src );
      $link_rel    = "tc-fancybox-group{$post->ID}";

      foreach ( $attachments as $k => $attachment ) { //get all related galery attachement for lightbox navigation excluding the current one
        if ( $attachment -> ID == $post -> ID )
          continue;

        $rel_attachment_infos       = wp_get_attachment_image_src( $attachment->ID , 'large' );
        $rel_attachment_src         = $rel_attachment_infos[0];

        $gallery                    = sprintf( '%1$s<a href="%2$s" title="%3$s" class="grouped_elements" rel="tc-fancybox-group%4$s"></a>',
                                      $gallery,
                                      esc_url( $rel_attachment_src ),
                                      !empty( $attachment -> post_excerpt ) ? $attachment -> post_escerpt : $attachment -> post_title,
                                      $post -> ID
                                    );
      }
    }//end else
  
    $attachment_size = apply_filters( 'tc_customizr_attachment_size' , array( 960, 960 ) );
    $has_gallery     = ! empty ( $gallery );

    //update the model
    $this -> tc_update( compact( 'gallery', 'attachment_size', 'link_url', 'link_rel', 'has_gallery', 'attachment_class' ) );
  }
}  
