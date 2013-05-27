<?php
/**
 * The template part for displaying the posts header
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>

<header class="entry-header">
<?php //bubble color computation
    $nbr = get_comments_number();
    $style = ($nbr == 0) ? 'style="color:#ECECEC" ':'';
 ?>

<?php if(!in_array(get_post_format(), array( 'aside', 'status', 'link','quote'))) : ?>
	
	<?php if ( is_single() ) : ?>
		<?php 
			if ( comments_open() ) {
				if ( !post_password_required() ) {
					printf('<h1 class="entry-title format-icon">%1$s %2$s</h1>',
					get_the_title(),
					'<span class="comments-link">
		  				<a href="'.get_permalink().'#comments" title="'.__('Comment(s) on ','customizr').get_the_title().'"><span '.$style.' class="fs1 icon-bubble"></span><span class="inner">'.get_comments_number().'</span></a>
		  			</span>'
		  			);
		  		}
		  	}
		  	else {
		  		printf('<h1 class="entry-title format-icon">%1$s</h1>',
					get_the_title()
					);
		  	}
		?>
		

	<?php else : // case for all posts lists : index, archive, search... ?>
		
		<?php 
			printf('<h2 class="entry-title format-icon">%1$s %2$s</h2>',
				'<a href="'.get_permalink().'" title="'.esc_attr( sprintf( __( 'Permalink to %s', 'customizr' ), the_title_attribute( 'echo=0' ) ) ).'" rel="bookmark">'.((get_the_title() == null) ? __('{no title} Read the post &raquo;','customizr'):get_the_title()).'</a>',
				'<span class="comments-link"><span '.$style.' class="fs1 icon-bubble"></span><span class="inner">'.get_comments_number().'</span></span>'
				)
		?>	

	<?php endif;//end if is_single() ?>

	<div class="entry-meta">
	   	<?php //meta not displayed on home page, only in archive or search pages
	   		if ( !is_home() || !is_front_page() ) { 
	   			get_template_part( 'parts/metas');
			}

			if ( is_single() ) {
			   	edit_post_link( __( 'Edit', 'customizr' ), '<span class="edit-link btn btn-inverse btn-mini">', '</span>' );
			}
		?>
	</div><!-- .entry-meta -->
<?php endif;//end if post format in array ?>
</header><!-- .entry-header -->
