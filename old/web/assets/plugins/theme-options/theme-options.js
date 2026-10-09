/*
 *
 *		STYLE-SWITHER.JS
 *
 */

$(document).ready(function() {

    var theme_options_content = ' \
		<h4>Change Theme Options</h4> \
		<a href="#"></a> \
		<br> \
		<h5>Change Color</h5> \
		<div class="colors clearfix"> \
			<a id="default" href="#" data-style="default"></a> \
			<a id="brown" href="#" data-style="brown"></a> \
			<a id="grey" href="#" data-style="grey"></a> \
			<a id="purple" href="#" data-style="purple"></a> \
			<a id="blue" href="#" data-style="blue"></a> \
		</div><!-- colors --> \
		<h5>Change Layout</h5> \
		<div class="layout clearfix"> \
			<a class="wide" href="#" data-style="wide"><img src="assets/plugins/theme-options/images/wide.png" alt="">Wide</a> \
			<a class="boxed" href="#" data-style="boxed"><img src="assets/plugins/theme-options/images/boxed.png" alt="">Boxed</a> \
		</div><!-- layout --> \
		<div class="boxed-bg"> \
			<h5>Change Bg Pattern</h5> \
			<div class="patterns clearfix"> \
				<a class="bg-pattern-1" href="#" data-style="bg-pattern-1"></a> \
				<a class="bg-pattern-2" href="#" data-style="bg-pattern-2"></a> \
				<a class="bg-pattern-3" href="#" data-style="bg-pattern-3"></a> \
				<a class="bg-pattern-4" href="#" data-style="bg-pattern-4"></a> \
				<a class="bg-pattern-5" href="#" data-style="bg-pattern-5"></a> \
			</div><!-- pattern --> \
			<h5>Change Bg Color</h5> \
			<div class="patterns clearfix"> \
				<a class="bg-pattern-6" href="#" data-style="bg-pattern-6"></a> \
				<a class="bg-pattern-7" href="#" data-style="bg-pattern-7"></a> \
				<a class="bg-pattern-8" href="#" data-style="bg-pattern-8"></a> \
				<a class="bg-pattern-9" href="#" data-style="bg-pattern-9"></a> \
				<a class="bg-pattern-10" href="#" data-style="bg-pattern-10"></a> \
			</div><!-- pattern --> \
		</div><!-- boxed-bg --> \
	\ ';


    var select_demos = ' \
		<h4>Submit Enquiry</h4> \
		<a href="#"></a> \
		<br> \
		<div class="owl-carousel demos-slider"> \
			<div class="item"> \
				<a href="../startup-business/index.html"> \
					<h5><small>Demo</small> 4 Business </h5> \
					<img src="assets/plugins/theme-options/images/demo-1.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-wedding/index.html"> \
					<h5><small>Demo</small> 4 Wedding </h5> \
					<img src="assets/plugins/theme-options/images/demo-9.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-app/index.html"> \
					<h5><small>Demo</small> 4 App </h5> \
					<img src="assets/plugins/theme-options/images/demo-10.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-medical/index.html"> \
					<h5><small>Demo</small> 4 Medical </h5> \
					<img src="assets/plugins/theme-options/images/demo-7.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-catering/index.html"> \
					<h5><small>Demo</small> 4 Catering </h5> \
					<img src="assets/plugins/theme-options/images/demo-8.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-auto/index.html"> \
					<h5><small>Demo</small> 4 Auto Shop </h5> \
					<img src="assets/plugins/theme-options/images/demo-2.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="index.html"> \
					<h5><small>Demo</small> 4 Construction</h5> \
					<img src="assets/plugins/theme-options/images/demo-3.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-transport/index.html"> \
					<h5><small>Demo</small> 4 Transport</h5> \
					<img src="assets/plugins/theme-options/images/demo-4.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-gardening/index.html"> \
					<h5><small>Demo</small> 4 Gardening</h5> \
					<img src="assets/plugins/theme-options/images/demo-5.png" alt=""> \
				</a> \
			</div><!-- item --> \
			<div class="item"> \
				<a href="../startup-cleaning/index.html"> \
					<h5><small>Demo</small> 4 Cleaning</h5> \
					<img src="assets/plugins/theme-options/images/demo-6.png" alt=""> \
				</a> \
			</div><!-- item --> \
		</div><!-- demos-slider --> \
	\ ';


    $("#theme-options").prepend(theme_options_content);
    $("#select-demos").prepend(select_demos);

    $("#theme-options > a").on("click", function(e) {

        e.preventDefault();
        $("#theme-options").toggleClass("open");

    });

    $("#select-demos > a").on("click", function(e) {

        e.preventDefault();
        $("#select-demos").toggleClass("open");

    });


    var link = $('link[data-style="styles"]');
    var startup_auto_colors = $.cookie('startup_auto_colors'),
        startup_auto_layout = $.cookie('startup_auto_layout'),
        startup_auto_bg_pattern = $.cookie('startup_auto_bg_pattern');

    if ($.cookie('startup_auto_layout') == "wide") {
        $(".boxed-bg").hide();
    } else {
        $(".boxed-bg").show();
    }

    if (!($.cookie('startup_auto_colors'))) {

        $.cookie('startup_auto_colors', 'default', 365);
        startup_auto_colors = $.cookie('startup_auto_colors');
        $('#theme-options .colors a[data-style="' + startup_auto_colors + '"]');

    } else {

        link.attr('href', 'assets/css/alternative-styles/' + startup_auto_colors + '.css');
        $("#logo img").attr('src', 'assets/images/logo-' + startup_auto_colors + '.png');
        $("#footer-bottom img").attr('src', 'assets/images/logo-2-' + startup_auto_colors + '.png');
        $('#theme-options .colors a[data-style="' + startup_auto_colors + '"]');

    };

    if (!($.cookie('startup_auto_layout'))) {

        $.cookie('startup_auto_layout', 'wide', 365);
        startup_auto_layout = $.cookie('startup_auto_layout');
        $("body").addClass(startup_auto_layout);
        $('#theme-options .layout a[data-style="wide"]');

    } else {

        if (startup_auto_layout == "boxed") {

            $("body").addClass(startup_auto_layout);
            $("body").removeClass("wide");

        } else {

            $("body").addClass(startup_auto_layout);
            $("body").removeClass("boxed bg-pattern-1 bg-pattern-2 bg-pattern-3 bg-pattern-4 bg-pattern-5 bg-pattern-6 bg-pattern-7 bg-pattern-8 bg-pattern-9 bg-pattern-10");

        };

    };

    if ((startup_auto_layout == "boxed") && $.cookie('startup_auto_bg_pattern')) {

        $("body").removeClass("bg-pattern-1 bg-pattern-2 bg-pattern-3 bg-pattern-4 bg-pattern-5 bg-pattern-6 bg-pattern-7 bg-pattern-8 bg-pattern-9 bg-pattern-10 wide");
        $("body").addClass(startup_auto_bg_pattern);

    } else {

        if (startup_auto_layout == "boxed") {

            $("body").removeClass("bg-pattern-1 bg-pattern-2 bg-pattern-3 bg-pattern-4 bg-pattern-5 bg-pattern-6 bg-pattern-7 bg-pattern-8 bg-pattern-9 bg-pattern-10");

        } else {

            $("body").removeClass("bg-pattern-1 bg-pattern-2 bg-pattern-3 bg-pattern-4 bg-pattern-5 bg-pattern-6 bg-pattern-7 bg-pattern-8 bg-pattern-9 bg-pattern-10 boxed");

        }

    };


    // CHANGE COLOR //
    $('#theme-options .colors a').on('click', function(e) {

        var $this = $(this),
            startup_auto_colors = $this.data('style');

        e.preventDefault();

        link.attr('href', 'assets/css/alternative-styles/' + startup_auto_colors + '.css');
        $("#logo img").attr('src', 'assets/images/logo-' + startup_auto_colors + '.png');
        $("#footer-bottom img").attr('src', 'assets/images/logo-2-' + startup_auto_colors + '.png');
        $.cookie('startup_auto_colors', startup_auto_colors, 365);

    });

    // BOXED LAYOUT //
    $('#theme-options .layout a.boxed').on('click', function(e) {

        e.preventDefault();

        $("body").addClass("boxed");
        $("body").removeClass("wide");
        $.cookie('startup_auto_layout', 'boxed', 365);

        if ($.cookie('startup_auto_bg_pattern')) {

            var startup_auto_bg_pattern = $.cookie('startup_auto_bg_pattern');

            $("body").removeClass("bg-pattern-1 bg-pattern-2 bg-pattern-3 bg-pattern-4 bg-pattern-5 bg-pattern-6 bg-pattern-7 bg-pattern-8 bg-pattern-9 bg-pattern-10");
            $("body").addClass(startup_auto_bg_pattern);

        }

        document.location.reload();

    });

    // WIDE LAYOUT
    $('#theme-options .layout a.wide').on('click', function(e) {

        e.preventDefault();

        $("body").addClass("wide");
        $("body").removeClass("boxed bg-pattern-1 bg-pattern-2 bg-pattern-3 bg-pattern-4 bg-pattern-5 bg-pattern-6 bg-pattern-7 bg-pattern-8 bg-pattern-9 bg-pattern-10");
        $.cookie('startup_auto_layout', 'wide', 365);

        document.location.reload();

    });

    // CHANGE PATTERNS //
    $('#theme-options .patterns a').on('click', function(e) {

        var $this = $(this),
            startup_auto_bg_pattern = $this.data('style');

        e.preventDefault();

        $("body").removeClass("bg-pattern-1 bg-pattern-2 bg-pattern-3 bg-pattern-4 bg-pattern-5 bg-pattern-6 bg-pattern-7 bg-pattern-8 bg-pattern-9 bg-pattern-10 wide");
        $("body").addClass(startup_auto_bg_pattern);
        $("#theme-options select").val("boxed");
        $.cookie('startup_auto_bg_pattern', startup_auto_bg_pattern, 365);

    });



    // DEMOS SLIDER //
    $(".owl-carousel.demos-slider").owlCarousel({
        items: 3,
        itemsDesktop: [1199, 3],
        itemsDesktopSmall: [991, 3],
        itemsTablet: [767, 3],
        itemsMobile: [479, 3],
        slideSpeed: 500,
        autoPlay: false,
        stopOnHover: true,
        navigation: true,
        navigationText: false,
        pagination: false,
        paginationNumbers: false,
        mouseDrag: true,
        touchDrag: true,
        transitionStyle: false
    });


});