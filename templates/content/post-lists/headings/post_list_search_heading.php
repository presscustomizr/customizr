<?php
/**
* The template for displaying the list of posts titles (archives, categories, )
*/
?>
<header class="row page__header image__header archive-header" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container header-content">
    <div class="header-content-inner">
       <h1 class="archive-title"><?php echo __( 'Search results for:', 'customizr' ) . ' ' . get_search_query() ?></h1>
       <?php
        global $wp_query;
        if ( $wp_query->found_posts ):
        ?>
        <span>
          <?php printf( _n('%s result', '%s results', $wp_query->found_posts, 'customizr' ), $wp_query->found_posts ) ?>
        </span>
        <?php
        endif
        ?>
    </div>
  </div>
</header>