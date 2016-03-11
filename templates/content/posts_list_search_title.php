<div class="<?php echo $posts_list_search_title_model -> title_wrapper_class ?>">
  <h1 class="<?php echo $posts_list_search_title_model -> title_class ?>">
    <?php echo $posts_list_search_title_model -> pre_title ?> <span><?php echo get_search_query() ?></span>
  </h1>
</div>
<div class="<?php echo $posts_list_search_title_model -> search_form_wrapper_class ?>">
  <?php get_search_form(); ?>
</div>
