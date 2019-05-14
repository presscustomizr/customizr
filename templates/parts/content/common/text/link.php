<?php
/**
 * The template for displaying a link
 *
 *
 * @package Customizr
 */
?>
<?php

      if ( czr_fn_get_property( 'link_url' ) ) :

?>
<p class="<?php czr_fn_echo( 'element_class' ) ?> entry-link" <?php czr_fn_echo( 'element_attributes') ?>>
  <a class="czr-format-link" target="_blank" href="<?php czr_fn_echo( 'link_url' ) ?>"><?php czr_fn_echo( 'link_title' ) ?></a>
</p>
<?php

      endif //czr_fn_get_property( 'link_url' )

?>

