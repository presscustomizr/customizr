<?php
/**
 * The template for displaying a gallery
 *
 *
 * @package Customizr
 */
?>
<?php
      $gallery_items = czr_fn_get( 'the_gallery_items' );

      if ( $gallery_items ) :

?>
<div class="czr-carousel" <?php czr_fn_echo( 'element_attributes' ) ?>>
<?php
        if ( count( $gallery_items ) > 1 ) :
          czr_fn_render_template( 'modules/carousel_nav' );
        endif;

?>
  <div class="carousel carousel-inner">
<?php
        foreach ( $gallery_items as $gallery_item ) :
?>
    <div class="carousel-cell"><img class="gallery-img wp-post-image" src="<?php esc_attr_e( $gallery_item['src'] ) ?>" data-mfp-src="<?php esc_attr_e( $gallery_item['data-mfp-src'] ) ?>" alt="<?php esc_attr_e( $gallery_item['alt'] ) ?>" /></div>
<?php
        endforeach;
?>
    </div>
</div>
<?php

      endif; //gallery_items

?>

