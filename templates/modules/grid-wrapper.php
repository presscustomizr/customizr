<hr>
<section class="row" style="border:1px solid white; padding:3px">
  <h2>This is an example of secondary loop</h2>
  <p>This grid is displayed with the following code</p>
  <pre>
    array( 'hook' => '__content__', 'template' => 'modules/grid-wrapper', 'priority' => 20 ),
    array( 'hook' => 'in_grid_wrapper', 'id' => 'secondary_loop', 'template' => 'loop', 'query' => array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'ignore_sticky_posts' => 1 ) ),
    array( 'hook' => 'in_secondary_loop', 'template' => 'modules/grid-item' ),
  </pre>
  <?php do_action('in_grid_wrapper'); ?>
</section>
