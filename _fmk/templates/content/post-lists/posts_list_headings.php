<?php
/**
 * The template for displaying the headings in list of posts:
 * archives, categories, tags, search ,, titles
 *
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="<?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php
    //do_action( '__headings_posts_list__' )
    if ( czr_fn_has('posts_list_title') ) { czr_fn_render_template('content/post-lists/posts_list_title'); }
    elseif ( czr_fn_has('posts_list_search_title') ) { czr_fn_render_template('content/post-lists/posts_list_title', 'posts_list_search_title' ); }

    if ( czr_fn_has('posts_list_description') ) { czr_fn_render_template('content/post-lists/posts_list_description'); }
    if ( czr_fn_has('author_description') ) { czr_fn_render_template('content/authors/author_info', 'author_description'); }
  ?>
  <hr class="featurette-divider headings post-lists">
</header>
