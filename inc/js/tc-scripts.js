jQuery(document).ready(function () {
    ! function ($) {
        function g($) {
            ($.which > 0 || "mousedown" === $.type || "mousewheel" === $.type) && f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g)
        }

        //fancybox with localized script variables
        var b = TCParams.FancyBoxState,
            c = TCParams.FancyBoxAutoscale;
        1 == b && $("a.grouped_elements").fancybox({
            transitionIn: "elastic",
            transitionOut: "elastic",
            speedIn: 200,
            speedOut: 200,
            overlayShow: !1,
            autoScale: 1 == c ? "true" : "false",
            changeFade: "fast",
            enableEscapeButton: !0
        });

        //replace title by img alt field
        1 == b && $('a[rel*=tc-fancybox-group]').each( function() {
            var title = $(this).find('img').prop('title');
            var alt = $(this).find('img').prop('alt');
            if (typeof title !== 'undefined' && 0 != title.length) {
                $(this).attr('title',title);
            }
            else if (typeof alt !== 'undefined' &&  0 != alt.length) {
                $(this).attr('title',alt);
            }
        });

        //Slider with localized script variables
        var d = TCParams.SliderName,
            e = TCParams.SliderDelay;
            j = TCParams.SliderHover;

        if (0 != d.length) {
            if (0 != e.length && !j) {
                $("#customizr-slider").carousel({
                    interval: e,
                    pause: "false"
                });
            } else if (0 != e.length) {
                $("#customizr-slider").carousel({
                    interval: e
                });
            } else {
                $("#customizr-slider").carousel();
            }
        }

        //Smooth scroll but not on bootstrap buttons. Checks if php localized option is active first.
        var SmoothScroll = TCParams.SmoothScroll;

        if ('easeOutExpo' == SmoothScroll) {
            $('a[href^="#"]').not('[class*=edd], .carousel-control, [data-toggle="modal"], [data-toggle="dropdown"], [data-toggle="tooltip"], [data-toggle="popover"], [data-toggle="collapse"], [data-toggle="tab"]').click(function () {
                var anchor_id = $(this).attr("href");
                if ('#' != anchor_id) {
                    $('html, body').animate({
                        scrollTop: $(anchor_id).offset().top
                    }, 700, SmoothScroll);
                }
                return false;
            });
        }

        //Stop the viewport animation if user interaction is detected
        var f = $("html, body");
        $(".back-to-top").on("click", function ($) {
            f.on("scroll mousedown DOMMouseScroll mousewheel keyup", g), f.animate({
                scrollTop: 0
            }, 1e3, function () {
                f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g)
            }), $.preventDefault()
        }),


        //Detects browser with CSS
        // Chrome is Webkit, but Webkit is also Safari. If browser = ie + strips out the .0 suffix
        $.browser.chrome ? $("body").addClass("chrome") : $.browser.webkit ? $("body").addClass("safari") : ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version ) && $("body").addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, '')),
        
        //Adds version if browser = ie
        $("body").hasClass("ie") && $("body").addClass($.browser.version);
    
        

        $(".widget-front, article").hover(function () {
            $(this).addClass("hover")
        }, function () {
            $(this).removeClass("hover")
        });

        $(".widget li").hover(function () {
            $(this).addClass("on")
        }, function () {
            $(this).removeClass("on")
        });

        $("article.attachment img").delay(500).animate({
                opacity: 1
            }, 700, function () {}
        );

        //Change classes of the comment reply and edit to make the whole button clickable (no filters offered in WP to do that)
        if ( TCParams.HasComments ) {
           //edit
           $('cite p.edit-link').each(function() {
                $(this).removeClass('btn btn-success btn-mini');
           });
           $('cite p.edit-link > a').each(function() {
                $(this).addClass('btn btn-success btn-mini');
           });
           //reply
           $('.comment .reply').each(function() {
                $(this).removeClass('btn btn-small');
           });
           $('.comment .reply .comment-reply-link').each(function() {
                $(this).addClass('btn btn-small');
           });
        }


        $(window).on( 'load' , function () {

            //Detect layout and reorder content divs
            var LeftSidebarClass    = TCParams.LeftSidebarClass || '.span3.left.tc-sidebar',
                RightSidebarClass   = TCParams.RightSidebarClass || '.span3.right.tc-sidebar',
                wrapper             = $('#main-wrapper .container[role=main] > .column-content-wrapper'),
                content             = $("#main-wrapper .container .article-container"),
                left                = $("#main-wrapper .container " + LeftSidebarClass),
                right               = $("#main-wrapper .container " + RightSidebarClass);

            function BlockPositions() {
                //15 pixels adjustement to avoid replacement before real responsive width
                WindowWidth = $(window).width();
                if ( WindowWidth > 767 - 15 ) {
                    //$(window).width();
                    if ( $(left).length ) {
                        $(left).detach();
                        $(content).detach();
                        $(wrapper).append($(left)).append($(content));
                    }
                    if ( $(right).length ) {
                        $(right).detach();
                        $(wrapper).append($(right));
                    }
                } else {
                    if ( $(left).length ) {
                         $(left).detach();
                        $(content).detach();
                        $(wrapper).append($(content)).append( $(left) );
                    }
                    if ( $(right).length ) {
                        $(right).detach();
                        $(wrapper).append($(right));
                    }
                }
            }//end function*/

            //Enable reordering if option is checked in the customizer.
            if ( 1 == TCParams.ReorderBlocks ) {
                //trigger the block positioning only when responsive
                WindowWidth = $(window).width();
                if ( WindowWidth <= 767 - 15 ) {
                    BlockPositions();
                }

                $(window).resize(function () {
                    setTimeout(BlockPositions, 200);
                });
            }
            

            function CenterSlides(){
                var container_width = $('.carousel .carousel-inner').width(),
                    container_height = $('.carousel .carousel-inner').height();
                
                // do something only if the new container's width differs of 50px from our last adjustment
                if ( Math.abs( last_adj_container_width - container_width ) < 50 )
                    return;
                
                last_adj_container_width = container_width;
                
                $images.each(function () {
                    
                    // this will let us know the real img height
                    var ratio = container_width / $(this).attr("width");
                    var real_img_height = ratio * $(this).attr("height");
                    
                    // if our image has an height smaller than the container
                    // stretch it (h & w) proportionally to reach the container height
                    // and center it horizontally
                    if ( real_img_height < container_height ){
                        // set the image height as the container height
                        $(this).css("height", container_height);
                        // this will let us know the new image width
                        var img_ratio = container_height / real_img_height;
                        var new_img_width = img_ratio * container_width;
                        // set it
                        $(this).css("width", new_img_width);
                        $(this).css("max-width", new_img_width);

                        // center it horizontally
                        var pos_left = ( (container_width - new_img_width ) / 2 );
                        $(this).css("left", pos_left);
                        
                        // reset v-center flag and margin-top
                        if ( $(this).hasClass("v-center") ){
                            $(this).removeClass("v-center")
                            .css("top", "0px");
                        }

                        // add h-center class flag
                        $(this).addClass("h-center");

                    } else { // center it vertically
                            // this covers also the case real_img_height == container_height
                            // a differentiation here looks like pratically useless

                        // reset margin-left, width, height and h-center flag
                        if ( $(this).hasClass("h-center") ){
                            $(this).css("width", "100%")
                            .css("max-width", "100%")
                            .css("left", "0px")
                            .css("height", "auto")
                            .removeClass("h-center")
                        }
                        // center it vertically
                        var pos_top = ( container_height - real_img_height ) / 2 ;
                        $(this).css("top", pos_top);
                        // add v-center class flag
                        $(this).addClass("v-center");
                    }// end if-else                                      
                });// end imgs each function
            }// end CenterSlides
        
             //Enable slides centering if option is checked in the customizer.
            if ( 1 == TCParams.CenterSlides ) {
                //adds a specific class to the carousel when automatic centering is enabled
                $('#customizr-slider .carousel-inner').addClass('center-slides-enabled');
                var $images = $('.carousel .item .carousel-image > img'),
                    last_adj_container_width = 0;

                CenterSlides();
    
                $(window).resize(function(){
                     setTimeout(CenterSlides, 200);
                });
            }
        });//end on load

    }(window.jQuery)
});