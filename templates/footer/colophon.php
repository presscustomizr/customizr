<div class="colophon">
    <div class="container">
        <div class="<?php echo $colophon_model -> class ?>">
            <?php
                //colophon blocks actions priorities
                //renders blocks
                do_action( '__colophon__' );
            ?>
        </div><!-- .row-fluid -->
    </div><!-- .container -->
</div><!-- .colophon -->
