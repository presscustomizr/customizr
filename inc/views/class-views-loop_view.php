<?php
/**
* Da loop
* Instanciated from the children on 'wp'
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.10
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_loop_view' ) ) :
  class TC_loop_view {
    static $instance;
    public $args;
    public $loop_name = '';
    public $query;
    public $loop_view;
    public $instance_id;
    public $render_on_hook = '__daloop';//this is the default hook declared in the index.php template

    function __construct( $_args ) {
      self::$instance =& $this;
      //store class args in a property
      $this -> args = $_args;
      //Gets the accessible non-static properties of the given object according to scope.
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $_args[ $key ] ) ) {
          $this->$key = $_args[ $key ];
        }
      }

      //set base class properties
      $this -> tc_set_loop_name_on_query_ready();

      //Actually renders the loop
      add_action( $this -> render_on_hook   , array($this, 'tc_render_loop') );
    }


    //hook 'wp'
    function tc_set_loop_name_on_query_ready() {
      $_args = $this -> args;
      //Sets the default wp_query
      //Can be overriden in the args
      global $wp_query, $wp_the_query;
      $this -> query = $wp_query;

      //what is the current loop name or id ?
      //Whall we add one ?
      //=> a hook suffix is added when $this -> query != wp_the_query
      //
      //Do we have a name or shall we use the id ?
      //=> we use the name property. If not set, the instance id is used instead (instance id is set on class instanciation in init.php )
      if ( $this -> query != $wp_the_query )
        $this -> loop_name = isset( $_args[ 'loop_name' ] ) ? $_args[ 'loop_name' ] : $this -> instance_id;
    }




    //hook : $this -> render_on_hook
    public function tc_render_loop() {
      ?>
        <?php do_action( "__before_article_container{$this -> loop_name}" , $this -> loop_name); ##hook of left sidebar?>

                <div id="content" class="<?php echo implode(' ', apply_filters( "tc_article_container_class" , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) ) ) ?>">

                    <?php do_action ( "__before_loop{$this -> loop_name}", $this -> loop_name);##hooks the heading of the list of post : archive, search... ?>

                        <?php if ( tc__f('__is_no_results') || is_404() ) : ##no search results or 404 cases ?>

                            <article <?php apply_filters( "__article_selectors{$this -> loop_name}" , $this -> loop_name) ?>>
                                <?php do_action( "__loop{$this -> loop_name}", $this -> loop_name); ?>
                            </article>

                        <?php endif; ?>

                        <?php if ( $this -> query -> have_posts() && ! is_404() ) : ?>
                            <?php while ( $this -> query -> have_posts() ) : ##all other cases for single and lists: post, custom post type, page, archives, search, 404 ?>
                                <?php $this -> query -> the_post(); ?>

                                <?php do_action ( "__before_article{$this -> loop_name}", $this -> loop_name) ?>
                                    <article <?php apply_filters( "__article_selectors{$this -> loop_name}", $this -> loop_name) ?>>
                                        <?php do_action( "__loop{$this -> loop_name}" , (string) $this -> loop_name); ?>
                                    </article>
                                <?php do_action ( "__after_article{$this -> loop_name}", $this -> loop_name) ?>

                            <?php endwhile; ?>

                        <?php endif; ##end if have posts ?>

                    <?php do_action ( "__after_loop{$this -> loop_name}", $this -> loop_name);##hook of the comments and the posts navigation with priorities 10 and 20 ?>

                </div><!--.article-container -->

           <?php do_action( "__after_article_container{$this -> loop_name}", $this -> loop_name); ##hook of left sidebar ?>
      <?php
    }

  }//end of class
endif;