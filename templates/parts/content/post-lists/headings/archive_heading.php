<?php
/**
* The template for displaying the list of posts titles (archives, categories, )
*/
?>
<header class="row page__header image__header archive-header" <?php czr_fn_echo('element_attributes') ?>>
  <?php
    //blog page, maybe render its featured image
    if ( is_home() || is_post_type_archive() ) :
        czr_fn_render_template( 'content/common/media',
                    array(
                      'model_args' => array(
                        'media_type'               => 'wp_thumb',
                        'has_permalink'            => false,
                        'has_lightbox'             => false,
                        'post_id'                  => czr_fn_get_id()
                      )
                    )
        );
    endif;
  ?>
  <div class="container header-content">
    <div class="header-content-inner">
      <h1 class="archive-title">
        <?php
          if( (bool) $pre_title = czr_fn_get_property( 'pre_title' ) )
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
      <?php if ( (bool) $description = czr_fn_get_property( 'description' ) )  : ?>
      <div class="header-content-bottom">
        <div class="archive-meta">
          <?php echo $description ?>
        </div>
      </div>
      <?php endif ?>
    </div>
  </div>
</header>