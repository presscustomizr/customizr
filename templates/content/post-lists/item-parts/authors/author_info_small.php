<?php
/**
* The template for displaying the author info small
*
*/
?>
<div class="author-info" <?php czr_fn_echo('element_attributes') ?>>
  <?php
    echo get_avatar( get_the_author_meta( 'user_email' ), 48 );
    czr_fn_echo( 'author', 'post_metas' );
  ?>
</div>