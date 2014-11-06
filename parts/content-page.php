<?php
/**
 * The template part for displaying page content
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>

<header>
    <h1 class="format-icon">
     <?php the_title(); ?>
    	<?php edit_post_link( __( 'Edit', 'customizr' ), '<span class="edit-link btn btn-inverse btn-mini">', '</span>' ); ?>
    </h1>
</header>
<hr class="featurette-divider">
<div class="entry-content">
	<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'customizr' ) ); ?>
</div>
   <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'customizr' ), 'after' => '</div>' ) ); ?>
<footer class="entry-meta">
</footer><!-- .entry-meta -->
