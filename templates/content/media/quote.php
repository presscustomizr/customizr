<?php
/**
 * The template for displaying a blockquote
 *
 *
 * @package Customizr
 */
?>
<?php

      if ( czr_fn_get( 'quote_text' ) ) :

?>
<blockquote class="blockquote entry-quote"<?php czr_fn_echo( 'element_attributes' ) ?>>
  <p><?php czr_fn_echo( 'quote_text' ) ?></p>
<?php
      if ( czr_fn_get( 'quote_source' )  ) :
?>
  <footer class="blockquote-footer"><cite><?php czr_fn_echo( 'quote_source' ) ?></cite></footer>
<?php
      endif //czr_fn_get( 'quote_source' )
?>
</blockquote>
<?php

      endif //czr_fn_get( 'quote_text' )

?>

