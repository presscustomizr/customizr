<?php
/**
 * The template for displaying the header search form (mobile) and button
 */
?>
<li class="primary-nav__search" <?php czr_fn_echo('element_attributes') ?>>
  <a class="desktop_search__link hidden-md-down"><span class="sr-only">Search</span><i class="icn-search"></i></a>
  <div class="hidden-lg-up">
    <?php get_search_form() ?>
  </div>
</li>