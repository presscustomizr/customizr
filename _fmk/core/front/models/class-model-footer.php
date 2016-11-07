<?php
class CZR_cl_footer_model_class extends CZR_cl_Model {

  function czr_fn_setup_children() {
        $children = array(
          //sticky footer
          array(
            'id'          => 'footer_push',
            'model_class' => 'footer/footer_push'
          ),
         //footer widgets help block
          array(
            'hook'        => '__before_inner_footer',
            'id'          => 'footer_widgets_help_block',
            'template'    => 'modules/help_block',
            'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/footer_widgets_help_block' )
          )

        );

        return $children;
  }
}//end of class
