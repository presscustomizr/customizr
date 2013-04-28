<?php
/**
 * The base template for displaying content for all type of posts
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>

<?php
	//get the post object
	global $post;
	//initialize the class alternative index
	global $tc_i;
	//get theme options array
	global $tc_theme_options;
	//initialize the content class
	global $content_class;
	$thumb_class = '';

	if ($tc_theme_options['tc_current_screen_layout']['class'] != 'span12') {
		$content_class  = 'span12';
		$thumb_class  = 'span12';
	}
	else {
		$content_class  = (tc_post_thumbnail($thumb_class) != false && !is_single()) ? 'span8' : 'span12';
		$thumb_class  = 'span4';
	}
?>
<?php if(is_page()) : //pages ?>
	
	<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
	    <?php get_template_part( 'parts/content', 'page' ); ?>
	</article><!-- #page -->

<?php elseif (is_attachment()) : ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php get_template_part( 'parts/content', 'attachment' ); ?>
	</article><!-- #post -->

<?php elseif (is_404()) : ?>

	<article id="post-0" class="post error404 no-results not-found row-fluid">
		<?php get_template_part( 'parts/content', '404' ); ?>
	</article><!-- #post-0 -->

<?php elseif (is_search() && !$post) : ?>
	<article id="post-0" class="post error404 no-results not-found row-fluid">
		<?php get_template_part( 'parts/content', 'no-results' ); ?>
	</article><!-- #post-0 -->

<?php else : // posts ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('row-fluid'); ?>>
		<?php
		  if ($tc_theme_options['tc_current_screen_layout']['class'] == 'span12') {
		    if($tc_i%2 == 0) {
		      if (tc_post_thumbnail($thumb_class))
		        echo tc_post_thumbnail($thumb_class);
		      get_template_part( 'parts/content', get_post_format() );
		    }
		    else {
		      get_template_part( 'parts/content', get_post_format() );
		      if (tc_post_thumbnail($thumb_class))
		        echo tc_post_thumbnail($thumb_class);
		    }
		  }
		  else {
		      get_template_part( 'parts/content', get_post_format() );
		      if (tc_post_thumbnail($thumb_class))
		        echo tc_post_thumbnail($thumb_class);
		  }
		?>
	</article><!-- #post -->

	<?php if(!is_single()) : ?>
	  <hr class="featurette-divider">
	<?php endif; ?>

<?php endif; ?>