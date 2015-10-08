<?php
/**
* Da loop
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.10
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_loop_base' ) ) :
  class TC_loop_base {
    static $instance;
    public $_hook_suffix = '';
    public $query;
    public $loop_view;
    public $name;
    public $instance_id;
    public $render_on_hook = '__daloop';//this is the default hook declared in the index.php template

    function __construct( $_args ) {
      self::$instance =& $this;
      //Sets the default wp_query
      //Can be overriden in the args
      global $wp_query, $wp_the_query;
      $this -> query = $wp_query;

      //Gets the accessible non-static properties of the given object according to scope.
      $keys = array_keys( get_object_vars( $this ) );
      foreach ( $keys as $key ) {
        if ( isset( $_args[ $key ] ) ) {
          $this->$key = $_args[ $key ];
        }
      }

      //what is the current hook suffix ?
      //Whall we add one ?
      //=> a hook suffix is added when $this -> query != wp_the_query
      //
      //What is this suffix ?
      //=> we use the name property. If not set, the instance id is used instead (instance id is set on class instanciation in init.php )
      if ( $this -> query != $wp_the_query )
        $this -> _hook_suffix = isset( $_args[ 'name' ] ) ? $_args[ 'name' ] : $this -> instance_id;

      //Actually renders the loop
      add_action( "{$this -> render_on_hook}"     , array($this, 'tc_render_loop') );

      //if Main query, check the context on query ready
      //and instanciate the relevant class(es)
      add_action( 'wp', array( $this, 'tc_instanciate_relevant_views') );
    }


    //hook : wp
    function tc_instanciate_relevant_views() {
      if ( $this -> tc_is_single_post() )
        tc_new( array('content' => array( array('inc/parts', 'post') ) ) );
    }



    public function tc_render_loop() {
      global $wp_query
      ?>
        <?php do_action( "__before_article_container{$this -> _hook_suffix}" ); ##hook of left sidebar?>

                <div id="content" class="<?php echo implode(' ', apply_filters( "tc_article_container_class{$this -> _hook_suffix}" , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) ) ) ?>">

                    <?php do_action ("__before_loop{$this -> _hook_suffix}");##hooks the heading of the list of post : archive, search... ?>

                        <?php if ( tc__f('__is_no_results') || is_404() ) : ##no search results or 404 cases ?>

                            <article <?php tc__f("__article_selectors{$this -> _hook_suffix}") ?>>
                                <?php do_action( "__loop{$this -> _hook_suffix}" ); ?>
                            </article>

                        <?php endif; ?>

                        <?php if ( $this -> query -> have_posts() && ! is_404() ) : ?>
                            <?php while ( $this -> query -> have_posts() ) : ##all other cases for single and lists: post, custom post type, page, archives, search, 404 ?>
                                <?php $this -> query -> the_post(); ?>

                                <?php do_action ("__before_article{$this -> _hook_suffix}") ?>
                                    <article <?php tc__f("__article_selectors{$this -> _hook_suffix}") ?>>
                                        <?php do_action( "__loop{$this -> _hook_suffix}" ); ?>
                                    </article>
                                <?php do_action ("__after_article{$this -> _hook_suffix}") ?>

                            <?php endwhile; ?>

                        <?php endif; ##end if have posts ?>

                    <?php do_action ("__after_loop{$this -> _hook_suffix}");##hook of the comments and the posts navigation with priorities 10 and 20 ?>

                </div><!--.article-container -->

           <?php do_action( "__after_article_container{$this -> _hook_suffix}"); ##hook of left sidebar ?>
      <?php
    }







    /***************************************************************************************************************
    * HELPERS : CONTEXT CHECKER
    ***************************************************************************************************************/
    /**
    * Single post view controller
    * @return  boolean
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_is_single_post() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      return apply_filters( 'tc_show_single_post_content',
        isset($post)
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && is_singular()
        && ! tc__f( '__is_home_empty')
      );
    }

  }//end of class
endif;