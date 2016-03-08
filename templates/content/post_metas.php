POST METAS:
<?php if ( $post_metas_model -> tc_get_cat_list() ) : ?>
PUB : <?php echo $post_metas_model -> tc_get_publication_date(); ?>
<?php endif; ?>
<?php if ( $post_metas_model -> tc_get_cat_list() ) : ?>
 CATS : <?php echo $post_metas_model -> tc_get_cat_list(); ?>
<?php endif; ?>
<?php if ( $post_metas_model -> tc_get_tag_list() ) : ?>
 TAGS : <?php echo $post_metas_model -> tc_get_tag_list(); ?>
<?php endif; ?>
<?php if ( $post_metas_model -> tc_get_author() ) : ?>
 AUTHOR : <?php echo $post_metas_model -> tc_get_author(); ?>
<?php endif; ?>
<?php if ( $post_metas_model -> tc_get_cat_list() ) : ?>
 UPDATE : <?php echo $post_metas_model -> tc_get_update_date(); ?>
<?php endif; ?>
