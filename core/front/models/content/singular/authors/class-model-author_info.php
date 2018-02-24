<?php
class CZR_author_info_model_class extends CZR_Model {
    public $authors_id; //array
    public $authors_number;//int


    /*
    * Fired just before the view is rendered
    * @hook: pre_rendering_view_{$this -> id}, 9999
    */
    /*
    * Each time this model view is rendered setup the relevant model properties
    */
    //In the wp loop
    function czr_fn_setup_late_properties() {
        $author_id       = get_the_author_meta( 'ID' );
        $authors_id      = apply_filters( 'tc_post_author_id', array( $author_id ) );
        $authors_id      = is_array( $authors_id ) ? $authors_id: array( $author_id );
        //author candidates must have a bio to be displayed
        $authors_id      = array_filter( $authors_id, 'czr_fn_get_author_meta_description_by_id' );
        $authors_number  = count( $authors_id );

        //update the model
        $this -> czr_fn_update( compact(
            'authors_id',
            'authors_number'
        ) );
    }
}