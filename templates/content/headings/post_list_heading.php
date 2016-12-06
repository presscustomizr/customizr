<?php
/**
* The template for displaying the list of posts titles (archives, categories, )
*/
?>
<header class="row page__header image__header archive-header" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container header-content">
    <div class="header-content-inner col-xs-12">
      <h1 class="archive-title">
        <?php
          if( (bool) $pre_title = czr_fn_get( 'pre_title' ) )
            echo "{$pre_title}&nbsp;";
          czr_fn_echo( 'title' );
        ?>
      </h1>
      <?php
      global $wp_query;
      if ( $wp_query->found_posts ):
      ?>
        <span>
          <?php printf( _n('%s post', '%s posts', $wp_query->found_posts, 'customizr' ), $wp_query->found_posts ) ?>
        </span>
      <?php
      endif
      ?>
    </div>
    <?php if ( (bool) $description = czr_fn_get( 'description' ) )  : ?>
    <div class="header-content-bottom">
      <div class="archive-meta col-xs-12">
        <?php echo $description ?>
      </div>
    </div>
    <?php endif ?>
  </div>
</header>