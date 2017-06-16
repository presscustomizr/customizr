<?php
/**
 * The template for displaying a blockquote
 *
 *
 * @package Customizr
 */
?>
<blockquote class="blockquote entry-quote <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo( 'element_attributes' ) ?>>
  <p>
<?php

    czr_fn_echo( 'quote_text' );

    if ( czr_fn_get_property( 'quote_source' )  ):
?>
    <cite><?php czr_fn_echo( 'quote_source' ) ?></cite>
<?php

    endif //czr_fn_get_property( 'quote_source' )

?>
  </p>
</blockquote>

