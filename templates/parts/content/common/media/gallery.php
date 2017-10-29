<?php
/**
 * The template for displaying a gallery
 *
 *
 * @package Customizr
 */
?>
<?php
      $gallery_items = czr_fn_get_property( 'gallery_items' );
?>
<div class="czr-gallery czr-carousel" <?php czr_fn_echo( 'element_attributes' ) ?>>
<?php
        if ( count( $gallery_items ) > 1 ) :
            czr_fn_carousel_nav();
        endif;

?>
  <div class="carousel carousel-inner" <?php czr_fn_echo( 'carousel_inner_attributes' ) ?>>
<?php
        foreach ( $gallery_items as $gallery_item ) :
?>
    <div class="carousel-cell">
        <?php echo $gallery_item ?>
    </div>
<?php
        endforeach;
?>
    </div>
<?php
    if ( czr_fn_get_property( 'has_lightbox' ) ) :
        czr_fn_post_action( $link = '#', $class = 'expand-img-gallery' );
    endif;
?>
</div>
