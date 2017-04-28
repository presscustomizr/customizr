<?php
/**
 * The template for displaying the header of a single page
 * In loop
 *
 * @package Customizr
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-header-inner">
    <?php

    if ( get_the_title() ) :

    ?>
    <h1 class="entry-title"><?php the_title() ?></h1>
    <?php

    endif;
    if ( czr_fn_has('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
        czr_fn_edit_button( array( 'link'  => $edit_post_link ) );
    ?>
    <div class="header-bottom entry-meta">
      <div class="post-info">
        <?php
          czr_fn_comment_info();
        ?>
      </div>
    </div>
  </div>
</header>