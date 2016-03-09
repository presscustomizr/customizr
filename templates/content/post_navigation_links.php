<?php if ( $post_navigation_links_model -> prev_link ): ?>
<li class="previous">
  <span class="nav-previous">
  <?php echo  $post_navigation_links_model -> prev_link ?>
  </span>
</li>
<?php endif; ?>
<?php if ( $post_navigation_links_model -> next_link ): ?>
<li class="next">
  <span class="nav-next">
  <?php echo  $post_navigation_links_model -> next_link ?>
  </span>
</li>
<?php endif; ?>
