<?php
/**
 * The template for displaying the list of posts titles (archives, categories, search results ..)
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */

/* Case we're displaying the search results */
if ( 'search_results' == czr_fn_get( 'context' ) ) :

?>
<div class="row-fluid" <?php czr_fn_echo('element_attributes') ?>>
  <div class="<?php czr_fn_echo( 'title_wrapper_class' ) ?>">
    <h1 class="<?php czr_fn_echo( 'title_class' ) ?>">
      <?php czr_fn_echo( 'pre_title' ) ?> <span><?php echo get_search_query() ?></span>
    </h1>
  </div>
  <div class="<?php czr_fn_echo( 'search_form_wrapper_class' ) ?>">
    <?php get_search_form() ?>
  </div>
</div>
<?php

else :
/* All other cases */
?>
<h1 class="<?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?> ><?php czr_fn_echo( 'pre_title' ) ?> <?php czr_fn_echo( 'title' ) ?></h1>
<?php

endif;
