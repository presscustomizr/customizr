<?php
class CZR_attachment_image_content_model_class extends CZR_Model {
    //bools
    private $prepend_attachment_callback_on;
    public $defaults = array( 'attachment_size' => array( 960, 960 ) );

    public $attachment_gallery;
    public $attachment_size;
    public $attachment_class;
    public $attachment_caption;
    public $attachment_link_url;
    public $attachment_link_attributes;


    /*
    * Fired just before the view is rendered
    * @hook: pre_rendering_view_{$this -> id}, 9999
    */
    /*
    * Each time this model view is rendered setup the relevant model properties
    */
    function czr_fn_setup_late_properties() {

        //just before the view is rendered we want to remove the 'prepend_attachment' filter callback that wp adds to the_content filter
        //if not removed by anything else, and, in this case re-add it after the view is rendered
        //see: this->czr_fn_reset_late_properties
        //
        //WP by itself removes this filter callback as well when a theme has an attachment wp template see wp-includes/template-loader.php
        $this->prepend_attachment_callback_on = has_filter( 'the_content', 'prepend_attachment' );
        if ( $this->prepend_attachment_callback_on ) {
            remove_filter( 'the_content', 'prepend_attachment' );
        }

        $this->czr_fn_setup_attachment_content();
    }


    function czr_fn_setup_attachment_content() {

        global $post;
        $attachment_gallery     = '';

        //when the image has been attached to no posts the $post->parent_id value is 0 and the following
        $attachments = array_values(
            get_children( array(
                'post_parent' => $post->post_parent,
                'post_status' => 'inherit',
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => 'ASC',
                'orderby' => 'menu_order ID'
        ) ) );

        //did we activate the lighbox in customizer?
        $lightbox_on = czr_fn_is_checked( 'tc_fancybox' );

        //Attachment image caption
        $attachment_caption = !empty( $post->post_excerpt ) ? $post->post_excerpt : null;

        if ( !$lightbox_on ) { //lightbox not checked!
            /**
            * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
            * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
            */
            foreach ( $attachments as $k => $attachment ) {
                if ( $attachment->ID == $post->ID )
                    break;
            }

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
              $next_attachment_url   = wp_get_attachment_url();
            }

            $attachment_link_url        = esc_url( $next_attachment_url );
            $attachment_class           = 'attachment';
            $attachment_link_attributes = 'rel="attachment"';

        }
        else {// if lightbox option checked

            $attachment_info            = wp_get_attachment_image_src( $post->ID , 'large' );
            $attachment_src             = $attachment_info[0];
            $attachment_class           = 'grouped_elements';
            $attachment_link_url        =  esc_url( $attachment_src );
            $attachment_link_attributes = 'data-lb-type="grouped-post" rel="gallery"';

            foreach ( $attachments as $k => $attachment ) { //get all related gallery attachement for lightbox navigation excluding the current one
                if ( $attachment -> ID == $post -> ID )
                    continue;

                $rel_attachment_info        = wp_get_attachment_image_src( $attachment->ID , 'large' );
                $rel_attachment_src         = $rel_attachment_info[0];
                $attachment_gallery         = sprintf( '%1$s<a href="%2$s" title="%3$s" %4$s></a>',
                                              $attachment_gallery,
                                              esc_url( $rel_attachment_src ),
                                              !empty( $attachment->post_excerpt ) ? esc_attr( $attachment->post_excerpt ) : the_title_attribute( array( 'echo' =>false, 'post' => $attachment ) ),
                                              $attachment_link_attributes
                                            );
            }

        }//end else


        $attachment_size            = apply_filters( 'czr_customizr_attachment_size' , $this->defaults['attachment_size'] );

        //update the model
        $this -> czr_fn_update( compact(
            'attachment_gallery',
            'attachment_size',
            'attachment_class',
            'attachment_caption',
            'attachment_link_url',
            'attachment_link_attributes'
        ) );
    }


    /*
    * Fired just after the view is rendered
    * @hook: post_rendering_view_{$this -> id}, 9999
    */
    function czr_fn_reset_late_properties() {
        //just before the view is rendered, re-add prepend_attachment_callback_on to the_content filter when required
        if ( $this->prepend_attachment_callback_on )
            add_filter( 'the_content', 'prepend_attachment', 10 );
    }
}