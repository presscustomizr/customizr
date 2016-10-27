<?php
/**
* The template for displaying the list of posts titles (archives, categories, )
*/
?>
<div class="row page__header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container header-content">
    <div class="header-content-inner">
       <h1 class="header-title"><?php echo __( 'Search results for:', 'customizr' ) . ' ' . get_search_query() ?></h1>
       <span><?php global $wp_query; echo $wp_query->found_posts . ' ' . __('posts', 'customizr' ) ?></span>
    </div>
  </div>
</div>