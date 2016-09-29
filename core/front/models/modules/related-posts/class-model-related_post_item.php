<?php
class CZR_cl_related_post_item_model_class extends CZR_cl_model {
  public  $has_post_media;
  public  $media_col;
  public  $content_col;
  public  $article_selectors;
  public  $only_thumb;

  function czr_fn_setup_late_properties() {
    $this -> czr_fn_update(   array(
      'has_post_media'    => czr_fn_get('has_post_media'),
      'media_col'         => czr_fn_get('media_col'),
      'content_col'       => czr_fn_get('content_col'),
      'article_selectors' => czr_fn_get_the_post_list_article_selectors( array('col-xs-12', 'col-md-6', 'grid-item') ),
      'only_thumb'        => true
    ) );
  }

}//end class
