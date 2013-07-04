<?php
/**
* Content actions and filters
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_content {

    function __construct () {
    	//base content template called by the loop
        add_action  ( '__content' 				, array( $this , 'tc_display_content' ));

        //posts templates and filter
        add_action  ( '__post' 					, array( $this , 'tc_content_post' ));
 		add_action  ( '__post_header' 			, array( $this , 'tc_content_post_header' ));
 		add_action  ( '__list_header' 			, array( $this , 'tc_content_list_header' ));
		add_action  ( '__post_metas' 			, array( $this , 'tc_content_post_metas' ));
 		add_action  ( '__post_footer' 			, array( $this , 'tc_content_post_footer' ));
 		add_action  ( '__post_thumbnail' 		, array( $this , 'tc_content_post_thumbnail' ));

 		add_filter  ( '__category_list'  		, array( $this , 'tc_content_category_list' ));
        add_filter  ( '__tag_list'       		, array( $this , 'tc_content_tag_list' ));
        add_filter  ( '__thumbnail'          	, array( $this , 'tc_has_thumbnail' ));

 		//pages templates
        add_action  ( '__page'					, array( $this , 'tc_content_page' ));

        //other templates
        add_action  ( '__attachment'			, array( $this , 'tc_content_attachment' ));
		add_action  ( '__404'					, array( $this , 'tc_content_404' ));
		add_action  ( '__no_result'				, array( $this , 'tc_content_no_result' ));
    }




    /**
	 * The base template for displaying content for all type of posts
	 *
	 * @package Customizr
	 * @since Customizr 1.0
	 */
    function tc_display_content() {
    	//get the post object
		global $post;
		//initialize the class alternative index
		global $tc_i;
		//initialize the content class
		global $content_class;

		//classes for content and thumbnail
		$thumb_class  				= 'span4';
		$content_class  			= ( tc__f( '__thumbnail' ) == true && !is_single()) ? 'span8' : 'span12';

		?>

		<?php if ( is_page()) : //pages ?>
			
			<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
			    <?php  	do_action( '__page' );   ?>
			</article><!-- #page -->

		<?php elseif (is_attachment()) : ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php do_action( '__attachment' ); ?>
			</article><!-- #post -->

		<?php elseif (is_404()) : ?>

			<article id="post-0" class="post error404 no-results not-found row-fluid">
				<?php do_action( '__404' ); ?>
			</article><!-- #post-0 -->

		<?php elseif (is_search() && !$post) : ?>
			
			<article id="post-0" class="post error404 no-results not-found row-fluid">
				<?php do_action( '__no_result' );?>
			</article><!-- #post-0 -->

		<?php else : // posts ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'row-fluid' ); ?>>
				<?php
				  if( $tc_i%2 == 0) {

				      if (tc__f( '__thumbnail' ) == true) {

				        	do_action( '__post_thumbnail' , $thumb_class );
				    	}

				      do_action( '__post' );
				    }

				    else {

				      do_action( '__post' );

				      if (tc__f( '__thumbnail' ) == true) {

				        	do_action( '__post_thumbnail' , $thumb_class );
				    	}
				    }

				?>
			</article><!-- #post -->

			<?php if(!is_single()) : ?>
			  <hr class="featurette-divider">
			<?php endif; ?>

		<?php endif; ?>
		<?php
	}






	/**
	 * The default template for displaying post content. Used for both single and index/archive/search.
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_content_post() {
		?>
		<?php global $content_class ?>

		<div class="tc-content <?php echo $content_class; ?>">
			
			<?php do_action( '__post_header' ); ?>
			
			<?php //bubble color computation
			    $nbr 					= get_comments_number();
			    $style 					= ( $nbr == 0) ? 'style="color:#AFAFAF" ':'';
		 	?>
		 	
				<?php if ( is_single() || is_page())
			        echo '<hr class="featurette-divider">';
			        ?>
				<?php if ( !is_single()) : //for lists of posts?> 
					<?php //display an icon for div if there is no title
							$icon_class = in_array(get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' )) ? 'format-icon':'';
						?>
					<?php if (!get_post_format()) :  // Only display Excerpts for lists of posts with format different than quote, status, link, aside ?>
						
						<div class="entry-summary">
							<?php the_excerpt(); ?>
						</div><!-- .entry-summary -->
					
					<?php elseif ( in_array(get_post_format(), array( 'image' , 'gallery' ))) : ?>
						
						<div class="entry-content">
							<p class="format-icon"></p>
						</div><!-- .entry-content -->
					
					<?php else : ?>
					
						<div class="entry-content <?php echo $icon_class ?>">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
						</div><!-- .entry-content -->

					<?php endif; //!is_single() ?>

				<?php else : ?>
					
						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
						</div><!-- .entry-content -->

				<?php endif; ?>
			
			<?php do_action( '__post_footer' ) ?>
				
		</div>
		<?php
	}





	/**
	 * The template part for displaying the posts header
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_content_post_header() {
	?>
		<header class="entry-header">
		<?php //bubble color computation
		    $nbr 						= get_comments_number();
		    $style 						= ( $nbr == 0) ? 'style="color:#ECECEC" ':'';
		 ?>

		<?php if(!in_array(get_post_format(), array( 'aside' , 'status' , 'link' , 'quote' ))) : ?>
			
			<?php if ( is_single() ) : ?>
				<?php 
					if ( comments_open() ) {
						if ( !post_password_required() ) {
							printf( '<h1 class="entry-title format-icon">%1$s %2$s</h1>' ,
							get_the_title(),
							'<span class="comments-link">
				  				<a href="'.get_permalink().'#comments" title="'.__( 'Comment(s) on ' , 'customizr' ).get_the_title().'"><span '.$style.' class="fs1 icon-bubble"></span><span class="inner">'.get_comments_number().'</span></a>
				  			</span>'
				  			);
				  		}
				  	}
				  	else {
				  		printf( '<h1 class="entry-title format-icon">%1$s</h1>' ,
							get_the_title()
							);
				  	}
				?>
				

			<?php else : // case for all posts lists : index, archive, search... ?>
				
				<?php 
					if ((get_the_title() != null)) {
						printf( '<h2 class="entry-title format-icon">%1$s %2$s</h2>' ,
							'<a href="'.get_permalink().'" title="'.esc_attr( sprintf( __( 'Permalink to %s' , 'customizr' ), the_title_attribute( 'echo=0' ) ) ).'" rel="bookmark">'.((get_the_title() == null) ? __( '{no title} Read the post &raquo;' , 'customizr' ):get_the_title()).'</a>' ,
							'<span class="comments-link"><span '.$style.' class="fs1 icon-bubble"></span><span class="inner">'.get_comments_number().'</span></span>'
						);
					}
				?>	

			<?php endif;//end if is_single() ?>

			<div class="entry-meta">

			   	<?php //meta not displayed on home page, only in archive or search pages
			   		if ( !is_home() || !is_front_page() ) { 
			   			do_action( '__post_metas' );
					}

					if ( is_single() ) {
					   	edit_post_link( __( 'Edit' , 'customizr' ), '<span class="edit-link btn btn-inverse btn-mini">' , '</span>' );
					}
				?>

			</div><!-- .entry-meta -->

		<?php endif;//end if post format in array ?>

		</header><!-- .entry-header -->
		<?php
	}






	 function tc_content_list_header() {
    /**
     * The template part for displaying additional header for posts list.
     *
     * @package Customizr
     * @since Customizr 1.0
     */
    ?>
    <?php if (is_404()) : ?>
      <header class="entry-header">

        <h1 class="entry-title"><?php _e( 'Ooops, page not found' , 'customizr' ); ?></h1>

      </header>

    <?php elseif ( is_search()) : ?>

      <header class="search-header">

        <h1 class="page-title">
          <?php 
            printf( __( '%1sSearch Results for: %2s' , 'customizr' ), 
            have_posts() ? '' :  __( 'No' , 'customizr' ).'&nbsp;' ,
            '<span>' . get_search_query() . '</span>' );
          ?>
        </h1>

      </header>

    <?php elseif ( is_author()) : ?>

    <?php
    /* Get the user ID. */
    $user_id = get_query_var( 'author' );
    ?>
      <header class="archive-header">

        <h1 class="archive-title"><?php printf( __( 'Author Archives: %s' , 'customizr' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( $user_id ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' , $user_id ) ) . '" rel="me">' . get_the_author_meta( 'display_name' , $user_id ) . '</a></span>' ); ?></h1>
      
      </header><!-- .archive-header -->

    <?php elseif ( is_category()) : ?>

      <header class="archive-header">

        <h1 class="archive-title"><?php printf( __( 'Category Archives: %s' , 'customizr' ), '<span>' . single_cat_title( '' , false ) . '</span>' ); ?></h1>

        <?php if ( category_description() ) : // Show an optional category description ?>
          <div class="archive-meta"><?php echo category_description(); ?></div>
        <?php endif; ?>
     
      </header><!-- .archive-header -->

    <?php elseif ( is_tag()) : ?>

      <header class="archive-header">
        
        <h1 class="archive-title"><?php printf( __( 'Tag Archives: %s' , 'customizr' ), '<span>' . single_tag_title( '' , false ) . '</span>' ); ?></h1>

      <?php if ( tag_description() ) : // Show an optional tag description ?>
        <div class="archive-meta"><?php echo tag_description(); ?></div>
      <?php endif; ?>
      
      </header><!-- .archive-header -->

    <?php elseif ( is_archive()) : ?>

      <header class="archive-header">

        <h1 class="archive-title"><?php
          if ( is_day() ) :
            printf( __( 'Daily Archives: %s' , 'customizr' ), '<span>' . get_the_date() . '</span>' );
          elseif ( is_month() ) :
            printf( __( 'Monthly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'F Y' , 'monthly archives date format' , 'customizr' ) ) . '</span>' );
          elseif ( is_year() ) :
            printf( __( 'Yearly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'Y' , 'yearly archives date format' , 'customizr' ) ) . '</span>' );
          else :
            _e( 'Archives' , 'customizr' );
          endif;
        ?></h1>

      </header><!-- .archive-header -->

    <?php endif; ?>

    <?php if(!is_single() && !is_page() && (!is_home() || !is_front_page())) : ?>
      <hr class="featurette-divider">
    <?php endif; ?>
    
    <?php
    }






	/**
	 * The template part for displaying entry metas
	 *
	 * @package Customizr
	 * @since Customizr 1.0
	 */
	function tc_content_post_metas() {
			$categories_list 	= tc__f( '__category_list' );

			$tag_list 			= tc__f( '__tag_list' );

			$date 				= sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>' ,
					esc_url( get_permalink() ),
					esc_attr( get_the_time() ),
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date() )
			);

			$author 			= sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author() ) ),
					get_the_author()
			);

			// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
			if ( $tag_list ) {
				$utility_text 	= __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
				} elseif ( $categories_list ) {
				$utility_text 	= __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
				} else {
				$utility_text 	= __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
			}

			printf(
				$utility_text,
				$categories_list,
				$tag_list,
				$date,
				$author
			);
	}





	 /**
     * Template for displaying the category list
     *
     *
     * @package Customizr
     * @since Customizr 3.0 
     *
     */
    function tc_content_category_list() {
      $postcats 				= get_the_category();
        if ( $postcats) {
          $html 				= '';
          foreach( $postcats as $cat) {
            $html 				.= '<a class="btn btn-mini" href="'.get_category_link( $cat->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $cat->name ) ) . '">';
              $html 				.= ' '.$cat->cat_name.' ';
            $html 				.= '</a>';
          }
          //$html .= '</div>';
         return $html;
        }
      }





    /**
     * Template for displaying the tag list
     *
     *
     * @package Customizr
     * @since Customizr 3.0 
     *
     */
     function tc_content_tag_list() {
      $posttags 				= get_the_tags();
        if ( $posttags) {
          $html 				= '';
          foreach( $posttags as $tag) {
            $html 				.= '<a class="btn btn-mini btn-info" href="'.get_tag_link( $tag->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $tag->name ) ) . '">';
               $html 				.= ' '.$tag->name.' ';
            $html 				.= '</a>';
          }
          //$html .= '</div>';
         return $html;
        }
     }




	/**
	 * The template part for displaying the posts footer
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_content_post_footer() {
	?>
		<footer class="entry-meta">

			<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
				
				<div class="author-info">

					<div class="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'tc_author_bio_avatar_size' , 68 ) ); ?>
					</div><!-- .author-avatar -->
					
					<div class="author-description">

						<h2><?php printf( __( 'About %s' , 'customizr' ), get_the_author() ); ?></h2>
						<p><?php the_author_meta( 'description' ); ?></p>
						
						<div class="author-link">
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
								<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>' , 'customizr' ), get_the_author() ); ?>
							</a>
						</div><!-- .author-link	-->

					</div><!-- .author-description -->

				</div><!-- .author-info -->

			<?php endif; ?>

		</footer><!-- .entry-meta -->
		<?php
	}



	 /**
	  * check if thumbnail or image linked to post
	  *
	  * @package Customizr
	  * @since Customizr 3.0
	  */
	  function tc_has_thumbnail() {
	      //handle the no search results and 404 error cases
	      global $post;
	      if(!$post)
	        return false;

	      //look for attachements
	      $tc_args 	= array(
			        'numberposts'     	=>  1,
			        'post_type'       	=>  'attachment' ,
			        'post_status'     	=>  null,
			        'post_parent'     	=>  get_the_ID(),
			        'post_mime_type'  	=>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' )
	        ); 
	      $attachments 					= get_posts( $tc_args);

	      $has_thumb 					= false;

	      if (has_post_thumbnail() || $attachments) {
	          $has_thumb 				= true;
	      }
	      return $has_thumb;
	  }




	  /**
      * Template to display thumbnail
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_content_post_thumbnail( $thumb_class) {
        //handle the no search results and 404 error cases
        global $post;
        if(!$post)
          return false;
        
        //define the default thumb size
        $tc_thumb_size 					= 'tc-thumb';

        //define the default thumnail if has thumbnail
        if (has_post_thumbnail()) {
            $tc_thumb_id 				= get_post_thumbnail_id();

            //check if tc-thumb size exists for attachment and return large if not
            $image 						= wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);
            if (null == $image[3]) {
              $tc_thumb_size 			= 'medium';
             }
            $tc_thumb 					= get_the_post_thumbnail( get_the_ID(),$tc_thumb_size);
            //get height and width
            $tc_thumb_height 			= $image[2];
            $tc_thumb_width 			= $image[1];
        }
          //check if there is a thumbnail and if not uses the first attached image
        else {
           //look for attachements
           $tc_args = array(
		              'numberposts'     		=>  1,
		              'post_type'       		=>  'attachment' ,
		              'post_status'     		=>  null,
		              'post_parent'     		=>  get_the_ID(),
		              'post_mime_type'  		=>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' )
              );

          $attachments 				= get_posts( $tc_args);

          if ( $attachments) {
            foreach ( $attachments as $attachment) {
               //check if tc-thumb size exists for attachment and return large if not
              $image 				= wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
              if (false == $image[3]) {
                $tc_thumb_size 		= 'medium';
               }
              $tc_thumb 			= wp_get_attachment_image( $attachment->ID, $tc_thumb_size);
              //get height and width
              $tc_thumb_height 		= $image[2];
              $tc_thumb_width 		= $image[1];
            }
          }
        }

        //handle the case when the image dimensions are too small
        $no_effect_class 			= '';
        if (isset( $tc_thumb) && ( $tc_thumb_width < 270)) {
          $no_effect_class 			= 'no-effect';
        }

        //render the thumbnail
        if ( isset( $tc_thumb) && !is_single()) {
              $html 			= '<div class="tc-thumbnail '.$thumb_class.'">';
                 $html 			.= '<div class="thumb-wrapper '.$no_effect_class.'">';
                    $html 			.=  '<a class="round-div '.$no_effect_class.'" href="'.get_permalink( get_the_ID() ).'" title="'.get_the_title( get_the_ID()).'"></a>';
                    //$html 		.= '<div class="round-div"></div>';
                      $html 			.= $tc_thumb;
                $html 			.= '</div>';
              $html 			.= '</div><!--.span4-->';
            echo $html; 
        }
      }





	/**
	 * The template part for displaying page content
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	
	function tc_content_page() {
		?>
		<header>
		     <?php if(!is_front_page()) : ?>
			     <?php 
			     	printf( '<h1 class="entry-title format-icon">%1$s %2$s</h1>' ,
						get_the_title(),
						(is_user_logged_in()) ? '<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="'.get_edit_post_link().'" title="'.__( 'Edit page' , 'customizr' ).'">'.__( 'Edit page' , 'customizr' ).'</a></span>' : ''
					); 
				?>
				<hr class="featurette-divider">
			 <?php endif; ?>
		</header>

		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
		</div>

		   <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>

		<footer class="entry-meta">

		</footer><!-- .entry-meta -->
		<?php
	}
	



	/**
	 * The template part for displaying no search results
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_content_no_result() {
		?>
		<?php global $content_class ?>

		<div class="tc-content <?php echo $content_class; ?> format-quote">

			<div class="entry-content format-icon">

			 	<blockquote><p><?php _e( 'Success is the ability to go from one failure to another with no loss of enthusiasm...' , 'customizr' ) ?></p>
			 	<cite><?php _e( 'Sir Winston Churchill' , 'customizr' ) ?></cite>
			 	</blockquote>
				<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.' , 'customizr' ); ?></p>
				<?php get_search_form(); ?>
			</div>

			<hr class="featurette-divider">

		</div><!--content -->
		<?php
	}




	/**
	 * The template part for displaying attachment content
	 * Inspired from Twenty Twelve WP Theme
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_content_attachment() {
		global $post;
		?>
		<header class="entry-header">

			<h1 class="entry-title"><?php the_title(); ?></h1>

			<footer class="entry-meta">
				<?php
					$metadata = wp_get_attachment_metadata();
					printf( __( '<span class="meta-prep meta-prep-entry-date">Published </span> <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%8$s</a>.' , 'customizr' ),
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date() ),
						esc_url( wp_get_attachment_url() ),
						$metadata['width'],
						$metadata['height'],
						esc_url( get_permalink( $post->post_parent ) ),
						esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
						get_the_title( $post->post_parent )
					);
				?>
				<?php edit_post_link( __( 'Edit' , 'customizr' ), '<span class="edit-link">' , '</span>' ); ?>
			</footer><!-- .entry-meta -->

			<nav id="image-navigation" class="navigation" role="navigation">

				<span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'customizr' ) ); ?></span>
				<span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'customizr' ) ); ?></span>

			</nav><!-- #image-navigation -->

		</header><!-- .entry-header -->

		<div class="entry-content">

			<div class="entry-attachment">

				<div class="attachment">
						<?php
						/**
						* Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
						* or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
						*/
						$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit' , 'post_type' => 'attachment' , 'post_mime_type' => 'image' , 'order' => 'ASC' , 'orderby' => 'menu_order ID' ) ) );
						foreach ( $attachments as $k => $attachment ) :
						if ( $attachment->ID == $post->ID )
						break;
						endforeach;

						$k++;
						// If there is more than 1 attachment in a gallery
						if ( count( $attachments ) > 1 ) :
						if ( isset( $attachments[ $k ] ) ) :
						// get the URL of the next image attachment
						$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
						else :
						// or get the URL of the first image attachment
						$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
						endif;
						else :
						// or, if there's only 1 image, get the URL of the image
						$next_attachment_url = wp_get_attachment_url();
						endif;
						?>

					<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
					$attachment_size = apply_filters( 'customizr_attachment_size' , array( 960, 960 ) );
					echo wp_get_attachment_image( $post->ID, $attachment_size );
					?></a>

					<?php if ( ! empty( $post->post_excerpt ) ) : ?>
					<div class="entry-caption">
						<?php the_excerpt(); ?>
					</div>
					<?php endif; ?>

				</div><!-- .attachment -->

			</div><!-- .entry-attachment -->

		</div><!-- .entry-content -->
		<?php
	}





	function tc_content_404() {
	/**
	 * The template part for displaying error 404 page content
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	
	?>

		<?php global $content_class ?>

		<div class="tc-content <?php echo $content_class; ?> format-quote">

			<div class="entry-content format-icon">

			 	<blockquote><p><?php _e( 'Speaking the Truth in times of universal deceit is a revolutionary act.' , 'customizr' ) ?></p>
			 	<cite><?php _e( 'George Orwell' , 'customizr' ) ?></cite>
			 	</blockquote>

				<p><?php _e( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' ); ?></p>

				<?php get_search_form(); ?>

			</div>

			<hr class="featurette-divider">
			
		</div><!--content -->
	<?php
	}


}//end of class




	