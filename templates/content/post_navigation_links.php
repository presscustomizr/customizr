<ul class="pager">
  <?php if ( tc_get( 'prev_link' ) ): ?>
  <li class="previous">
    <span class="nav-previous">
    <?php echo  tc_get( 'prev_link' ) ?>
    </span>
  </li>
  <?php endif; ?>
  <?php if ( tc_get( 'next_link' ) ): ?>
  <li class="next">
    <span class="nav-next">
    <?php echo  tc_get( 'next_link' ) ?>
    </span>
  </li>
  <?php endif ?>
</ul>
