<?php
class CZR_cl_related_post_item_model_class extends CZR_cl_model {
  public  $has_post_media;
  public  $media_col;
  public  $content_col;
  public  $article_selectors;

  function czr_fn_get_article_selectors() {
    return czr_fn_get_the_post_list_article_selectors( array('col-xs-12', 'col-md-6', 'grid-item') );
  }
}//end class
