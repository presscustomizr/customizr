<?php
/**
 * The template for displaying the headings in post lists and singular
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

/* Case we're displaying the headings of the list of posts, archives, categories, tags, search ,, */
?>
<header class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php
    //do_action( '__headings_posts_list__' )
    if ( tc_has('posts_list_title') ) { tc_render_template('content/post-lists/posts_list_title'); }
    elseif ( tc_has('posts_list_search_title') ) { tc_render_template('content/post-lists/posts_list_title', 'posts_list_search_title' ); }

    if ( tc_has('posts_list_description') ) { tc_render_template('content/post-lists/posts_list_description'); }
    if ( tc_has('author_description') ) { tc_render_template('content/authors/author_info', 'author_description'); }
  ?>
  <hr class="featurette-divider headings post-lists">
</header>
