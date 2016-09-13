<?php
/**
 * The template for the html5 search form
 */
?>
<div class="search-form__container">
  <form action="<?php echo esc_url(home_url( '/' )); ?>" method="get" class="czr-form search-form">
    <div class="form-group">
      <?php $label_id = rand() ?>
      <label class="screen-reader-text" for="s" id="search-<?php echo $label_id ?>"><?php _ex( 'Search', 'label', 'customizr') ?><i class="icn-search"></i></label>
      <input class="form-control" name="s" type="text" value="<?php echo get_search_query() ?>" aria-describedby="search-<?php echo $label_id ?>" title="<?php echo esc_attr_x( 'Search &hellip;', 'title', 'customizr') ?>">
    </div>
  </form>
</div>