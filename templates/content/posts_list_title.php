<?php
/**
 * The template for displaying the list of posts titles (archives, categories, search results ..)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

/* Case we're displaying the search results */
if ( 'search_results' == tc_get('context') ) :

?>
<div class="row-fluid" <?php tc_echo('element_attributes') ?>>
  <div class="<?php tc_echo( 'title_wrapper_class' ) ?>">
    <h1 class="<?php tc_echo( 'title_class' ) ?>">
      <?php tc_echo( 'pre_title' ) ?> <span><?php echo get_search_query() ?></span>
    </h1>
  </div>
  <div class="<?php tc_echo( 'search_form_wrapper_class' ) ?>">
    <?php get_search_form() ?>
  </div>
</div>
<?php

else :
/* All other cases */
?>
<h1 class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?> ><?php tc_echo( 'pre_title' ) ?> <?php tc_echo( 'title' ) ?></h1>
<?php

endif;
