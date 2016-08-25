<?php
/**
 * The template for displaying the header search form and button
 */
?>
<li class="primary-nav__search" <?php czr_fn_echo('element_attributes') ?>>
  <a class="desktop_search__link"><span class="sr-only">Search</span><i class="icn-search"></i></a>
  <div class="search-form__container">
    <form action="http://test.presscustomizr.com/" method="get" class="czr-form search-form">
      <div class="form-group">
        <label for="email">Search<i class="icn-search"></i></label>
        <input id="sidebar-search" class="form-control" name="search" type="text" value="" aria-describedby="search form" title="Search presscustomizr.com for ...">
       </div>
    </form>
  </div>
</li>