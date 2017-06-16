<?php
/**
* The template for displaying the list of posts titles (archives, categories, )
*/
?>
<header class="archive-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="archive-header-inner">
    <?php if ( czr_fn_get_property('title' ) ) : ?>
    <h1 class="archive-title">
      <?php
        if( (bool) $pre_title = czr_fn_get_property( 'pre_title' ) )
          echo "{$pre_title}&nbsp;";
        czr_fn_echo( 'title' );
      ?>
    </h1>
    <?php endif;

      global $wp_query;
      if ( $wp_query->found_posts ):
      ?>
      <div class="header-bottom">
        <span>
          <?php printf( _n('%s post', '%s posts', $wp_query->found_posts, 'customizr' ), $wp_query->found_posts ) ?>
        </span>
      </div>
      <?php
      endif
      ?>
      <?php if ( (bool) $description = czr_fn_get_property( 'description' ) )  : ?>
      <div class="archive-header-description">
        <div class="archive-meta">
          <?php echo $description ?>
        </div>
      </div>
      <?php else : ?>
        <hr class="featurette-divider">
      <?php endif ?>
  </div>
</header>