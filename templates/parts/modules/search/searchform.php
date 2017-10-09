<?php
/**
 * The template for the html5 search form
 */
?>
<div class="search-form__container <?php czr_fn_echo('element_class')?>" <?php czr_fn_echo('element_attributes')?>>
  <form action="<?php echo esc_url(home_url( '/' )); ?>" method="get" class="czr-form search-form">
    <div class="form-group czr-focus">
      <?php $sf_id = uniqid() ?>
      <label for="s-<?php echo $sf_id ?>" id="lsearch-<?php echo $sf_id ?>"><span><?php _ex( 'Search', 'label', 'customizr') ?></span><i class="icn-search"></i><i class="icn-close"></i></label>
      <input id="s-<?php echo $sf_id ?>" class="form-control czr-search-field" name="s" type="text" value="<?php echo get_search_query() ?>" aria-describedby="lsearch-<?php echo $sf_id ?>" title="<?php echo esc_attr_x( 'Search &hellip;', 'title', 'customizr') ?>">
    </div>
  </form>
</div>