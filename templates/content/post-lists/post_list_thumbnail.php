<?php
/**
 * The template for displaying the thumbnails in post lists (alternate layout) contexts
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<section class="tc-thumbnail <?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
 <div class="<?php tc_echo( 'thumb_wrapper_class' ) ?>">
  <?php

    /* Case rectangular thumbnail */
    if ( 'rectangular' == tc_get('type') ) :

  ?>
   <a class="<?php tc_echo( 'link_class' ) ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>">
     <?php tc_echo( 'thumb_img' ) ?>
   </a>
 <?php

    /* Case standard thumbnail (rounded/square) */
    else :

 ?>
   <div class="round-div"></div>
   <a class="<?php tc_echo( 'link_class' ) ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
   <?php tc_echo( 'thumb_img' ) ?>
 <?php

      endif;

 ?>
 </div>
</section>
