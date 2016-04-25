<?php
/**
 * The template for displaying the thumbnails in post lists (alternate layout) contexts
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<section class="tc-thumbnail <?php czr_echo( 'element_class' ) ?>" <?php czr_echo('element_attributes') ?>>
 <div class="<?php czr_echo( 'thumb_wrapper_class' ) ?>">
  <?php

    /* Case rectangular thumbnail */
    if ( 'rectangular' == czr_get('type') ) :

  ?>
   <a class="<?php czr_echo( 'link_class' ) ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>">
     <?php czr_echo( 'thumb_img' ) ?>
   </a>
 <?php

    /* Case standard thumbnail (rounded/square) */
    else :

 ?>
   <div class="round-div"></div>
   <a class="<?php czr_echo( 'link_class' ) ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
   <?php czr_echo( 'thumb_img' ) ?>
 <?php

      endif;

 ?>
 </div>
</section>
