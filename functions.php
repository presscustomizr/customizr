<?php
//Fire
require_once( get_template_directory() . '/core/init.php' );


// Fire Customizr
CZR();





/*//print the collection each time it's updated
add_action( 'collection_updated', function( $id, $model = null )  {
  ?>
    <pre>
      <?php print_r( 'COLLECTION UPDATED : ' . $id); ?>
    </pre>
  <?php
});*/


/*******************************************************
* SOME TEST VIEW CLASSES AND CALLBACKS
*******************************************************/
function callback_fn( $text1 = "default1", $text2 = "default2"  ) {
  ?>
    <h1>THIS IS RENDERED BY A CALLBACK FUNCTION WITH 2 OPTIONAL PARAMS : <?php echo $text1; ?> and <?php echo $text2; ?></h1>
  <?php
}


class TC_test_model_class extends TC_View {
  public $test_class_property = 'YOUPI';
  static $instance;

  function __construct( $model = array() ) {
    self::$instance =& $this;
    //Fires the parent constructor
    if ( ! isset(parent::$instance) )
      parent::__construct( $model );
  }

  /*public function tc_render() {
    ?>
      <h1>MY ID IS <span style="color:blue"><?php echo $this -> id ?></span>, AND I AM RENDERED BY THE VIEW CLASS</h1>
    <?php
  }*/
}


class TC_rendering {
  function callback_met( $text1 = "default1", $text2 = "default2"  ) {
    ?>
      <h1>THIS IS RENDERED BY A CALLBACK METHOD IN A CLASS, WITH 2 OPTIONAL PARAMS : <?php echo $text1; ?> and <?php echo $text2; ?></h1>
    <?php
  }
}



//CZR() -> collection -> tc_delete( '__body___30');


//CZR() -> collection -> tc_delete( 'joie');
add_action( 'wp' , 'register_test_views');

function register_test_views() {

  // CZR() -> collection -> tc_register(
  //   array(
  //     'hook'        => '__content__',
  //     'id'          => 'joie',
  //     'template'    => 'custom',
  //     'model_class'  => 'TC_test_model_class',
  //     'early_setup' => 'TC_test_early_setup',
  //     'children' => array(
  //       'child1' => array(
  //           'hook'        => 'in_custom_template',
  //           'html'        => '<h2 style="color:green">I AM A CHID VIEW</h2>'
  //       ),
  //       'child2' => array(
  //           'hook'        => 'in_custom_template',
  //           'html'        => '<h2 style="color:purple">I AM ANOTHER CHID VIEW</h2>'
  //       )
  //     )
  //   )
  // );


  // CZR() -> collection -> tc_register(
  //   array( 'hook' => '__content__', 'html' => '<h1>Yo Man this is some html to render</h1>' )
  // );
  // CZR() -> collection -> tc_register(
  //   array( 'hook' => '__content__', 'callback' => array( 'TC_rendering', 'callback_met'), 'html' => '<h1>YOOOOOO</h1>' )
  // );
  // CZR() -> collection -> tc_register(
  //   array( 'hook' => '__content__', 'callback' => 'callback_fn', 'cb_params' => array('custom1', 'custom2'), 'html' => '<h1>YAAAA</h1>' )
  // );

  //CZR() -> collection -> tc_change( 'joie', array('template' => '', 'html' => '<h1>Yo Man this is a changed view</h1>', 'model_class' => '') );
}

//register_test_views();



//Create a new test view
// CZR() -> collection -> tc_register(
//   array( 'hook' => '__after_header', 'template' => 'custom',  'html' => '<h1>Yo Man this some html to render 1</h1>' )
// );







/*add_action('__content__' , function() {
  ?>
    <pre>
      <?php print_r( CZR() -> collection -> tc_get() ); ?>
    </pre>
  <?php
}, 100);
*/




//@todo : tc_change does not work when the model is already instanciated
//=> shall remove the actions on "view_instanciated_{$this -> id}"

//@todo : children it would be good to add actions on pre_render_view, where we are in the parent's hook action.
//=> late check if new children have been registered
//=> if so, instanciate their views there
//
//@todo : the id could be based on the id, then template name, then hook_priority
//
//@todo : for logged in admin users, add a line of html comment before and after the view giving id, hook, priority
//
//@todo : move tc_apply_registered_changes_to_instance into the model ?
//
//@todo : pre_render_view stuffs














/*add_action('__after_footer' , function() {
  $args = array(
    'post_type' => array('post'),
    'post_status' => array('publish'),
    'posts_per_page'         => 10
  );

  tc_new(
    array('content' => array( array('inc/parts', 'loop_base') ) ),
    array(
      'name' => '',
      'query' => new WP_Query( $args )
    )
  );
});*/

/*add_action( 'parse_query' , function($query) {
  $test = array(
    'is_single' => $query -> is_single(),
    'is_home' => $query -> is_home()
  );

   if ( is_array() )
    array_walk_recursive(, function(&$v) { $v = htmlspecialchars($v); });
  ?>
    <pre>
      <br/><br/><br/><br/><br/><br/>
      <?php print_r($test); ?>
    </pre>
  <?php
  //wp_die();
});*/

// add_action('init' , function() {
//   $args = array(
//     'post_type' => array('post'),
//     'post_status' => array('publish'),
//     'posts_per_page'         => 3
//   );

//   tc_new(
//     array('content' => array( array('inc/parts', 'post_list') ) ),
//     array(
//       '_singleton' => false,
//       'loop_name' => 'custom-grid',
//       'query' => new WP_Query( $args ),
//       'render_on_hook' => '__after_header'
//     )
//   );
// });
