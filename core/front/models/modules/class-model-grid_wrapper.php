<?php
class TC_grid_wrapper_model_class extends TC_article_model_class {
  public $is_first_of_row;
  public $is_last_of_row;

  public $is_loop_start;
  public $is_loop_end;

  //number of cols of the current section
  public $section_cols;

  //number of cols of the grid
  public $grid_cols;

  public $figure_class;
  public $has_heading_in_caption;
//  public $text;

  private $expanded_sticky = true;

  private $post_id;

  /* override */
  function __construct( $model ) {
    //Fires the parent constructor
    parent::__construct( $model );

    //inside the loop but before rendering set some properties
    add_action( $this -> hook, array( $this, 'set_this_properties' ), 0 );
  }


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $this -> post_id = TC_utils::tc_id();

    $element_class = array();  
    //wrapper classes based on the user options
    if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_shadow') ) )
      array_push( $element_class, 'tc-grid-shadow' );
    if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_bottom_border') ) )
      array_push( $element_class, 'tc-grid-border' );

    $model[ 'element_class' ]     = $element_class;
    $model[ 'grid_cols' ]         = $this -> tc_get_grid_cols();

    return $model;
  }



  function set_this_properties() {
    $element_wrapper        = $this -> tc_get_element_wrapper_properties();
    //figure class
    $figure_properties      = $this -> tc_get_figure_properties();

    $has_heading_in_caption = array(true);
    //section properties which refers to the section row wrapper
    $section_row_wrapper    = $this -> tc_get_section_row_wrapper_properties();
    $this -> tc_update( array_merge( $element_wrapper, $section_row_wrapper, $figure_properties, $has_heading_in_caption ) );

    //hack
    set_query_var( 'section_cols', $section_row_wrapper['section_cols'] );
  }

  /*
  *
  */
  function tc_get_figure_properties() {
    $has_thumb           = $this -> tc_grid_show_thumb();
    $figure_class        = array( $has_thumb ? 'has-thumb' : 'no-thumb' );

    //if 1 col layout or current post is the expanded => golden ratio should be disabled
    if ( ( '1' == $this -> tc_get_grid_cols() || $this -> tc_force_current_post_expansion() ) && ! wp_is_mobile() )
      array_push( $figure_class, 'no-gold-ratio' );
    return compact( 'figure_class', 'has_thumb' );
  }

  /*
  * post list wrapper
  */
  function tc_get_element_wrapper_properties() {
    global $wp_query;
    $is_loop_start = 0 == $wp_query -> current_post;
    $is_loop_end   = $wp_query -> current_post == $wp_query -> post_count ;

    return compact( 'is_loop_start', 'is_loop_end' );
  }


  /*
  * Wrap articles in a grid section
  */
  function tc_get_section_row_wrapper_properties() {
    global $wp_query;
    
    $current_post      = $wp_query -> current_post;
    $start_post        = $this -> expanded_sticky ? 1 : 0;
    $section_cols      = $this -> tc_get_grid_section_cols();

    $is_first_of_row = false;
    $is_last_of_row  = false;

    if ( $start_post == $current_post || 0 == ( $current_post - $start_post ) % $section_cols ) 
      $is_first_of_row = true;
   
    if ( $wp_query->post_count == ( $current_post + 1 ) || 0 == ( ( $current_post - $start_post + 1 ) % $section_cols ) )
      $is_last_of_row  = true;

    return compact( 'is_first_of_row', 'is_last_of_row', 'section_cols' );
  }



  /* retrieves number of cols option, and wrap it into a filter */
  private function tc_get_grid_cols() {
    if ( ! isset( $this -> grid_cols ) )
      $grid_cols = $this -> tc_set_grid_cols( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_columns') ), TC_utils::tc_get_layout( $this -> post_id , 'class' ) );
    else
      $grid_cols = $this -> grid_cols;       

    return apply_filters( 'tc_get_grid_cols', $grid_cols );
  }



  /* returns articles wrapper section columns */
  private function tc_get_grid_section_cols() {
    return apply_filters( 'tc_grid_section_cols',
      $this -> tc_force_current_post_expansion() ? '1' : $this -> tc_get_grid_cols()
    );
  }



  /*
  * @return bool
  * returns if the current post is the expanded one
  */
  private function tc_force_current_post_expansion(){
    global $wp_query;
    return ( $this -> expanded_sticky && 0 == $wp_query -> current_post );
  }



  /**
  * @override
  * Returns the classes for the post div.
  *
  * @param string|array $class One or more classes to add to the class list.
  * @param int $post_id An optional post ID.
  * @package Customizr
  * @since 3.0.10
  */
  function tc_get_post_class( $class = '', $post_id = null ) {
    $_class = sprintf( '%1$s tc-grid span%2$s',
      apply_filters( 'tc_grid_add_expanded_class', $this -> tc_force_current_post_expansion() ) ? 'expanded' : '',
      is_numeric( $this -> tc_get_grid_section_cols() ) ? 12 / $this -> tc_get_grid_section_cols() : 6
    );
     
    //Separates classes with a single space, collates classes for post DIV
    return 'class="' . join( ' ', get_post_class( $_class ) ) . '"';
  }


  /**
  * @return  boolean
  */
  private function tc_grid_show_thumb() {
    return TC_utils_thumbnails::$instance -> tc_has_thumb() && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_show_thumb' ) );
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );  
    foreach ( array('figure') as $property )
      $model -> {"{$property}_class"} = $this -> tc_stringify_model_property( "{$property}_class" );
  }


  /******************************
  VARIOUS HELPERS
  *******************************/
  /*
  * @return bool
  * check if we have to expand the first sticky post
  */
  private function tc_is_sticky_expanded( $query = null ){ 
    /* STUB */    
    return $this -> expanded_sticky;
  }


  /**
  * hook : tc_article_container_class
  * inside loop
  * add custom classes to the grid .article-container element
  */
  function tc_grid_container_set_classes( $_classes ) {
    array_push( $_classes, 'tc-post-list-grid' );
    if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_shadow') ) )
      array_push( $_classes, 'tc-grid-shadow' );
    if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_bottom_border') ) )
      array_push( $_classes, 'tc-grid-border' );
    return $_classes;
  }



  /**
  * Grid columns = fn(current-layout)
  * Returns the max possible grid column number for a given layout
  *
  * @param $_col_nb = string possible values : 1, 2, 3, 4
  * @param $_current_layout string of layout class like span4
  */
  function tc_set_grid_cols( $_col_nb, $_current_layout ) {
    $_map = apply_filters(
        'tc_grid_col_layout_map',
        array(
          'span12'  => '4',//no sidebars
          'span11'  => '4',
          'span10'  => '4',
          'span9'   => '3',//one sidebar right or left
          'span8'   => '3',
          'span7'   => '2',
          'span6'   => '2',//two sidebars
          'span5'   => '2',
          'span4'   => '1',
          'span3'   => '1',
          'span2'   => '1',
          'span1'   => '1',
        )
    );
    
    if ( ! isset($_map[$_current_layout]) )
      return $_col_nb;
    if ( (int) $_map[$_current_layout] >= (int) $_col_nb )
      return (string) $_col_nb;
    return (string) $_map[$_current_layout];
  }



  /**
  * @param (string) $col_layout
  * @return string
  *
  */
  private function tc_get_grid_column_height( $_cols_nb = '3' ) {
    $_h               = $this -> tc_grid_get_thumb_height();
    $_current_layout  = TC_utils::tc_get_layout( $this -> post_id , 'sidebar' );
    $_layouts         = array('b', 'l', 'r' , 'f');//both, left, right, full (no sidebar)
    $_key             = 3;//default value == full
    if ( in_array( $_current_layout, $_layouts ) )
      //get the key = position of requested size in the current layout
      $_key = array_search( $_current_layout , $_layouts );
    $_grid_col_height_map =  apply_filters(
        'tc_grid_col_height_map',
        array(        // 'b'  'l'  'r'  'f'
          '1' => array( 225 , 225, 225, $_h ),
          '2' => array( 225 , $_h, $_h, $_h ),
          '3' => array( 225 , 225, 225, 225 ),
          '4' => array( 165 , 165, 165, 165 )
        )
    );
    //are we ok ?
    if ( ! isset( $_grid_col_height_map[$_cols_nb] ) )
      return $_h;
    //parse the array to ensure that all values are <= user height
    foreach ( $_grid_col_height_map as $_c => $_heights ) {
      $_grid_col_height_map[$_c] = $this -> tc_set_max_col_height ( $_heights ,$_h );
    }
    $_h = isset( $_grid_col_height_map[$_cols_nb][$_key] ) ? $_grid_col_height_map[$_cols_nb][$_key] : $_h;
    return apply_filters( 'tc_get_grid_column_height' , $_h, $_cols_nb, $_current_layout );
  }



  /**
  * parse the array to ensure that all values are <= user height
  * @param (array) grid_col_height_map
  * @param  (num) user defined max height in pixel
  * @return string
  *
  */
  private function tc_set_max_col_height( $_heights ,$_h ) {
    $_return = array();
    foreach ($_heights as $_value) {
      $_return[] = $_value >= $_h ? $_h : $_value;
    }
    return $_return;
  }



  /******************************
  * HELPERS FOR INLINE CSS
  *******************************/
  /**
  * @param (string) $col_layout
  * @return css media query string
  * Returns the paragraph and title media queries for a given layout
  */
  private function tc_get_grid_font_css( $_col_nb = '3' ) {
    $_media_queries     = $this -> tc_get_grid_media_queries();//returns the simple array of media queries
    $_grid_font_sizes = $this -> tc_get_grid_font_sizes( $_col_nb );//return the array of sizes (ordered by @media queries) for a given column layout
    $_col_rules         = array();
    $_media_queries_css = '';
    //flatten the matrix
    foreach ($_media_queries as $key => $_med_query_sizes ) {
      $_size = $_grid_font_sizes[$key];//=> size like 'xxl'
      $_css_prop = array(
        'h' => $this -> tc_grid_build_css_rules( $_size , 'h' ),
        'p' => $this -> tc_grid_build_css_rules( $_size , 'p' )
      );
      
      $_rules = $this -> tc_grid_assign_css_rules_to_selectors( $_med_query_sizes , $_css_prop, $_col_nb );
      $_media_queries_css .= "
        @media {$_med_query_sizes} {{$_rules}}
      ";
    }
    return $_media_queries_css;
  }


  /**
  * @return simple array of media queries
  */
  private function tc_get_grid_media_queries() {
    return apply_filters( 'tc_grid_media_queries' ,  array(
             '(min-width: 1200px)', '(max-width: 1199px) and (min-width: 980px)', '(max-width: 979px) and (min-width: 768px)', '(max-width: 767px)', '(max-width: 480px)'
           ));
  }


  /**
  * Return the array of sizes (ordered by @media queries) for a given column layout
  * @param  $_col_nb string
  * @param  $_requested_media_size
  * @return array()
  * Note : When all sizes are requested (default case), the returned array can be filtered with the current layout param
  * Size array must have the same length of the media query array
  */
  private function tc_get_grid_font_sizes( $_col_nb = '3', $_requested_media_size = null ) {
    $_col_media_matrix = apply_filters( 'tc_grid_font_matrix' , array(
      //=> matrix col nb / media queries
      //            1200 | 1199-980 | 979-768 | 767   | 480
     '1' => array( 'xxxl', 'xxl'   , 'xl'    , 'xl'  , 'l' ),
     '2' => array( 'xxl' , 'xl'    , 'l'     , 'xl'  , 'l' ),
     '3' => array( 'xl'  , 'l'     , 'm'     , 'xl'  , 'l' ),
     '4' => array( 'l'   , 'm'     , 's'     , 'xl'  , 'l' )
     ));

    //if a specific media query is requested, return a string
    if ( ! is_null($_requested_media_size) ) {
      $_media_queries = $this -> tc_get_grid_media_queries();
      //get the key = position of requested size in the current layout
      $_key = array_search( $_requested_media_size, $_media_queries);
      return apply_filters(
        'tc_get_layout_single_font_size',
        isset($_col_media_matrix[$_col_nb][$_key]) ? $_col_media_matrix[$_col_nb][$_key] : 'xl'
      );
    }
    return apply_filters(
      'tc_get_grid_font_sizes',
      isset($_col_media_matrix[$_col_nb]) ? $_col_media_matrix[$_col_nb] : array( 'xl' , 'l' , 'm', 'l', 'm' ),
      $_col_nb,
      $_col_media_matrix,
      TC_utils::tc_get_layout( $this -> post_id , 'class' )
    );
  }

  /**
  * hook : 'tc_get_grid_font_sizes'
  * Updates the array of sizes for a given sidebar layout
  * @param  $_sizes array. ex : array( 'xl' , 'l' , 'm', 'l', 'm' )
  * @param  $_col_nb string. Ex: '2'
  * @param  $_col_media_matrix : array() matrix 5 x 4 => media queries / Col_nb
  * @param  $_current_layout string. Ex : 'span9'
  * @return array()
  */
  function tc_set_layout_font_size( $_sizes, $_col_nb, $_col_media_matrix, $_current_layout ) {
    //max possible font size key in the col_media_queries matrix for a given sidebar layout
    $_map = apply_filters(
      'tc_layout_font_size_map',
      array(
        'span12'  => '1',//no sidebars
        'span11'  => '1',
        'span10'  => '1',
        'span9'   => '2',//one sidebar right or left
        'span8'   => '2',
        'span7'   => '3',
        'span6'   => '4',//two sidebars
        'span5'   => '4',
        'span4'   => '4',
        'span3'   => '4',
        'span2'   => '4',
        'span1'   => '4',
      )
    );
    if ( ! isset($_map[$_current_layout]) )
      return $_sizes;
    if ( (int) $_col_nb >= (int) $_map[$_current_layout] )
      return $_sizes;
    $_new_key = $_map[$_current_layout];
    return $_col_media_matrix[$_new_key];
  }



  /**
  * @return css string
  * @param size string
  * @param selector type string
  * returns ratio of size / body size for a given selector type ( headings or paragraphs )
  */
  private function tc_get_grid_font_ratios( $_size = 'xl' , $_sel = 'h' ) {
    $_ratios =  apply_filters( 'tc_get_grid_font_ratios' , array(
        'xxxl' => array( 'h' => 2.10, 'p' => 1 ),
        'xxl' => array( 'h' => 1.86, 'p' => 1 ),
        'xl' => array( 'h' => 1.60, 'p' => 0.93 ),
        'l' => array( 'h' => 1.30, 'p' => 0.85 ),
        'm' => array( 'h' => 1.15, 'p' => 0.80 ),
        's' => array( 'h' => 1.0, 'p' => 0.75 )
    ));

    if ( isset($_ratios[$_size]) && isset($_ratios[$_size][$_sel]) )
      return $_ratios[$_size][$_sel];
    return 1;
  }



  /**
  * @return css string
  * @param $_media_query = string of current media query.
  * @param $_css_prop = array of css rules for paragraph and titles for a given column layout
  * @param $_col_nb = current column layout
  * Assigns css rules to predefined grid selectors for headings and paragraphs
  * adds the '1' column css if (OR) :
  * 1) there's a sticky post
  * 2) user layout is one column
  */
  private function tc_grid_assign_css_rules_to_selectors( $_media_query, $_css_prop, $_col_nb ) {
    $_css = '';
    //Add one column font rules if there's a sticky post
    if ( $this -> tc_is_sticky_expanded() || '1' == $_col_nb ) {
      $_size      = $this -> tc_get_grid_font_sizes( $_col_nb = '1', $_media_query );//size like xxl
      $_h_one_col = $this -> tc_grid_build_css_rules( $_size , 'h' );
      $_p_one_col = $this -> tc_grid_build_css_rules( $_size , 'p' );
      $_css .= "
          .tc-post-list-grid .grid-cols-1 .entry-title {{$_h_one_col}}
          .tc-post-list-grid .grid-cols-1 .tc-g-cont {{$_p_one_col}}
      ";
    }
    $_h = $_css_prop['h'];
    $_p = $_css_prop['p'];
    $_css .= "
        .tc-post-list-grid article .entry-title {{$_h}}
        .tc-post-list-grid .tc-g-cont {{$_p}}
    ";
    return $_css;
  }
  
  /**
  * @return css string
  * @param column layout (string)
  * adds the one column css if (OR) :
  * 1) there's a sticky post
  * 2) user layout is one column
  */
  private function tc_grid_get_figure_css( $_col_nb = '3' ) {
    $_height = $this -> tc_get_grid_column_height( $_col_nb );
    $_cols_class      = sprintf( 'grid-cols-%s' , $_col_nb );
    $_css = '';
    //Add one column height if there's a sticky post
    if ( $this -> tc_is_sticky_expanded() && '1' != $_col_nb ) {
      $_height_col_one = $this -> tc_get_grid_column_height( '1' );
      $_css .= ".grid-cols-1 figure {
            height:{$_height_col_one}px;
            max-height:{$_height_col_one}px;
            line-height:{$_height_col_one}px;
      }";
    }
    $_css .= "
      .{$_cols_class} figure {
            height:{$_height}px;
            max-height:{$_height}px;
            line-height:{$_height}px;
      }";
    return $_css;
  }

  /**
  * @return string
  * @param size string
  * @param selector type string
  * returns the font-size and line-height css rules
  */
  private function tc_grid_build_css_rules( $_size = 'xl', $_wot = 'h' ) {
    $_lh_ratio = apply_filters( 'tc_grid_line_height_ratio' , 1.28 ); //line-height / font-size
    $_ratio = $this -> tc_get_grid_font_ratios( $_size , $_wot );
    //body font size
    $_bs = esc_attr( TC_utils::$inst->tc_opt( 'tc_body_font_size') );
    $_bs = is_numeric($_bs) && 1 >= $_bs ? $_bs : 15;
    return sprintf( 'font-size:%spx;line-height:%spx;' ,
      ceil( $_bs * $_ratio ),
      ceil( $_bs * $_ratio * $_lh_ratio )
    );
  }

  /**
  * @return (number) customizer user defined height for the grid thumbnails
  */
  private function tc_grid_get_thumb_height() {
    $_opt = esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_thumb_height') );
    return ( is_numeric($_opt) && $_opt > 1 ) ? $_opt : 350;
  }


  /**
  * @return css string
  * hook : tc_user_options_style
  * @since Customizr 3.2.18
  */
  function tc_user_options_style_cb( $_css ){
    $_col_nb  = $this -> tc_get_grid_cols();
    //GENERATE THE FIGURE HEIGHT CSS
    $_current_col_figure_css  = $this -> tc_grid_get_figure_css( $_col_nb );
    //GENERATE THE MEDIA QUERY CSS FOR FONT-SIZES
    $_current_col_media_css   = $this -> tc_get_grid_font_css( $_col_nb );
    $_css = sprintf("%s\n%s\n%s\n",
        $_css,
        $_current_col_media_css,
        $_current_col_figure_css
    );
    return $_css;
  }

}
