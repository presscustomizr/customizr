<?php
/**    
*  Content Picker lib
*  
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>, Rocco Aliberti <rocco@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME, Rocco Aliberti
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/* content picker ajax callback and resources */
if ( ! class_exists( 'TC_Content_Picker' ) ) :
  class TC_Content_Picker {
    static $instance;

    private $context;
    function __construct () {

      self::$instance =& $this;
      
      $this -> context = apply_filters('tc_content_picker_context', class_exists('WP_Customize_Control') ? 'customize_controls' : 'admin');

      //control scripts and style
      add_action ( $this -> context . '_enqueue_scripts'	   , array( $this , 'tc_customize_controls_js_css' ));
      add_action ( $this -> context . '_print_footer_scripts'  , array( $this, 'tc_print_content_picker_template' ) );

      // Picker ajax callback
      add_action ( 'wp_ajax_tc_get_content_list'			   , array( $this , 'tc_ajax_content_list' ), 0 );

    }


    function tc_ajax_content_list() {
        if ( isset( $_POST['TCCPnonce_name']) )
            check_ajax_referer( $_POST['TCCPnonce_name'], 'TCCPnonce' );
        else
            wp_die(0);

		$args = array();

		if ( isset( $_POST['search'] ) )
			$args['s'] = wp_unslash( $_POST['search'] );
        if ( isset( $_POST['ListType'] ) )
            $args['list_type'] = $_POST['ListType'];

		$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

		//require(ABSPATH . WPINC . '/class-wp-editor.php');
		$results = $this -> tc_content_picker_query( $args );

		if ( ! isset( $results ) )
			wp_die( 0 );

		echo json_encode( $results );
		echo "\n";

		wp_die();
	}
	/**
	 * Performs post queries for internal linking. => inspired from wp_link_query
	 *
	 * @since 3.1.0
	 *
	 * @param array $args Optional. Accepts 'pagenum' and 's' (search) arguments.
	 * @return array Results.
	 */
	public function tc_content_picker_query( $args = array() ) {
        $pts 							= get_post_types( array( 'public' => true ), 'objects' );
 		$pt_names 					    = array_keys( $pts );

        if ( isset($args['list_type']) && in_array( $args['list_type'], $pt_names ) )
          $pt_names                     = $args['list_type'];

		$query = array(
			'post_type' 				=> $pt_names,
			'suppress_filters' 			=> true,
			'update_post_term_cache' 	=> false,
			'update_post_meta_cache' 	=> false,
			'post_status' 				=> 'publish',
			'posts_per_page' 			=> 20,
		);

		$args['pagenum'] 				= isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

		if ( isset( $args['s'] ) )
			$query['s'] = $args['s'];

		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		/**
		 * Filter the link query arguments.
		 *
		 * Allows modification of the link query arguments before querying.
		 *
		 * @see WP_Query for a full list of arguments
		 *
		 * @since 3.7.0
		 *
		 * @param array $query An array of WP_Query arguments.
		 */
		$query = apply_filters( 'wp_link_query_args', $query );

		// Do main query.
		$get_posts = new WP_Query;
		$posts = $get_posts->query( $query );
		// Check if any posts were found.
		if ( ! $get_posts->post_count )
			return false;

		// Build results.
		$results = array();
		foreach ( $posts as $post ) {
			if ( 'post' == $post->post_type )
				$info = mysql2date( __( 'Y/m/d' ), $post->post_date );
			else
				$info = $pts[ $post->post_type ]->labels->singular_name;

			$title  	= trim( esc_html( strip_tags( get_the_title( $post ) ) ) );
			$title 		= ( strlen($title) > 30 ) ? substr( $title , 0 , 30) . ' ...' : $title;
			$results[] 	= array(
				'ID' 			=> $post->ID,
				'title' 		=> $title,
				'permalink' 	=> get_permalink( $post->ID ),
				'info' 			=> $info,
				'thumbnail' 	=> has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail( $post->ID, array(20 , 20) ) : ''
			);
		}

		/**
		 * Filter the link query results.
		 *
		 * Allows modification of the returned link query results.
		 *
		 * @since 3.7.0
		 *
		 * @see 'wp_link_query_args' filter
		 *
		 * @param array $results {
		 *     An associative array of query results.
		 *
		 *     @type array {
		 *         @type int    $ID        Post ID.
		 *         @type string $title     The trimmed, escaped post title.
		 *         @type string $permalink Post permalink.
		 *         @type string $info      A 'Y/m/d'-formatted date for 'post' post type,
		 *                                 the 'singular_name' post type label otherwise.
		 *     }
		 * }
		 * @param array $query  An array of WP_Query arguments.
		 */
		return apply_filters( 'tc_post_picker_query', $results, $query );
    }

    /**
     * Add script to controls
     * Dependency : customize-controls located in wp-includes/script-loader.php
     * Hooked on customize_controls_enqueue_scripts located in wp-admin/customize.php
     * @package Customizr
     * @since Customizr 3.3.16
     */
    function tc_customize_controls_js_css() {
        $is_customize = 'customize_controls' == $this -> context;

        wp_enqueue_style(
            'tc-content-picker-style',
            sprintf('%1$s/inc/admin/css/tc_content_picker%2$s.css' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
            $is_customize ? array( 'customize-controls' ) : array(),
            '',
            $media = 'all'
        ); 
        wp_enqueue_script(
            'tc-content-picker',
            sprintf('%1$s/inc/admin/js/tc_content_picker%2$s.js' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
            array( $is_customize ? 'customize-controls' : 'jquery' , 'underscore'),
            '',
            true
        );
        wp_localize_script(
            'tc-content-picker',
            'TCCPParams',
            apply_filters('tccp_params',
                array(
                  'TCCPaction'    => 'tc_get_content_list',
                  'TCCPNonceName' => 'tc_content_picker_nonce',
                  'TCCPNonce'     => wp_create_nonce('tc_content_picker_nonce'),
                  'TCCPL10n'      => array(
                      'select'         => __('Select'),
                      'noMatchesFound' => __('No matches found'),
                      'noTitle'        => __('(no title)')
                  )
                )
            )
        );
    }
    /*
    * Renders the underscore templates
    * callback of 'customize_controls_print_footer_scripts'
    *@since v3.3.16
    */
    function tc_print_content_picker_template() {
      ?>
		<script type="text/template" id="TCContentPicker">
			<div class="tc-cp-link-wrap">
				<div class="tc-cp-link">
					<div class="link-selector">
						<input type="hidden" id="id-field" type="text" name="id-field" />
						<div class="search-panel">
							<p class="howto"><?php _e( 'Click to select your content. Pick one your most recent post/page or use the search field below.' ); ?></p>
							<div class="link-search-wrapper">
								<label>
									<span class="search-label"><?php _e( 'Search' ); ?></span>
									<input type="search" id="search-field" class="link-search-field" autocomplete="off" />
									<span class="spinner"></span>
								</label>
							</div>
							<div class="search-results query-results" tabindex="0">
								<ul></ul>
								<div class="river-waiting">
									<span class="spinner"></span>
								</div>
							</div>
							<div class="most-recent-results query-results" tabindex="0">
								<div class="query-notice query-notice-message">
									<em class="query-notice-default"><?php _e( 'No search term specified. Showing recent items.' ); ?></em>
									<em class="query-notice-hint screen-reader-text"><?php _e( 'Search or use up and down arrow keys to select an item.' ); ?></em>
								</div>
								<ul></ul>
								<div class="river-waiting">
									<span class="spinner"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="submitbox">
						<div class="wp-link-cancel">
							<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</script>
    <?php
    }
  }//end TC_content_picker
endif;

if ( ! class_exists( 'TC_Customize_Content_Picker' ) && class_exists('WP_Customize_Control') ) :
    
  class TC_Customize_Content_Picker extends WP_Customize_Control {

    public $type = 'page';

    public function render_content()  {
      do_action( '__before_setting_control' , $this -> id );


      if (!empty( $this->title)) : ?>
        <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>
          <label>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php $this -> tc_print_content_picker_control() ?>
            <?php if(!empty( $this -> notice)) : ?>
              <span class="tc-notice"><?php echo $this -> notice ?></span>
            <?php endif; ?>
          </label>
      <?php
    }
 
    private function tc_print_content_picker_control() {
      printf('<input class="tc-cp" %1$s %2$s data-display_title="%3$s" value="%4$s">',
        call_user_func( array( $this, 'get'.'_'.'link' ) ),
        $this -> type ? "data-list_type= $this->type" : '',
        $this -> tc_get_selected( $this -> value() ),
        $this -> value()
      );
    }
    
    
    private function tc_get_selected( $value ){
      if ( 0 != $value )
        /* Here we should make some check on if is a taxonomy */
        $title = get_the_title($value);
      else
        $title = '&#45; ' . __('Select') . ' &#45';

      return $title;
    }
  }//end TC_Customize_Content_Picker
endif;
