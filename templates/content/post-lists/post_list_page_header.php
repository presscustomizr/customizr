<?php
/**
* The template for displaying the list of posts titles (archives, categories, )
*/
?>
<div class="row page__header image-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container header-content">
    <div class="header-content-inner">
      <h1 class="header-title">
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
  </div>
</div>