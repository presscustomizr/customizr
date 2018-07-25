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
<?php czr_fn_echo( 'quote_text' ); ?>
  </p>
<?php if ( czr_fn_get_property( 'quote_source' )  ): ?>
    <footer><cite><?php czr_fn_echo( 'quote_source' ) ?></cite></footer>
<?php
    endif //czr_fn_get_property( 'quote_source' )
?>
</blockquote>

