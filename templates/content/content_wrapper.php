<section>
  <h1>I AM THE TEMPLATE OF THE VIEW <span style="color:blue"><?php echo $content_wrapper_model -> id ?></span></h1>

  <?php echo $content_wrapper_model -> content_layout ?>

  <p style="text-align:center"><strong style="font-size:50px;font-family:arial">CONTENT</strong></p>
  <?php do_action( '__content__'); ?>

</section>