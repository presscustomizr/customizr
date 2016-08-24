<?php

?>
  <li class="primary-nav__search" <?php czr_fn_echo('element_attributes') ?>>
    <a class="desktop_search__link"><span class="sr-only">Search</span><i class="icn-search"></i></a>
       <div class="search-form__container">
         <form method="get" class="search__form" action="#">
            <label>
              <span class="screen-reader-text sr-only">Search...</span>
              <input type="search" class="search-field" placeholder="Search …" value="" name="s">
              <i class="icn-search"></i>
            </label>
        <input type="submit" class="search-submit" value="Search">
      </form>
    </div>
  </li>
