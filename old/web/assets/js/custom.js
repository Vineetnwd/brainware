/*
 *
 *		CUSTOM.JS
 *
 */

(function($){
	
	"use strict";
	
	// DETECT TOUCH DEVICE //
	function is_touch_device() {
		return !!('ontouchstart' in window) || ( !! ('onmsgesturechange' in window) && !! window.navigator.maxTouchPoints);
	}
	
	
	// SHOW/HIDE MOBILE MENU //
	function show_hide_mobile_menu() {
		
		$("#mobile-menu-button").on("click", function(e) {
            
			e.preventDefault();
			
			$("#mobile-menu").slideToggle(300);
			
        });	
		
	}
	
	
	// MOBILE MENU //
	function mobile_menu() {
		
		if ($(window).width() < 992) {
			
			if ($("#menu").length > 0) {
			
				if ($("#mobile-menu").length < 1) {
					
					$("#menu").clone().attr({
						id: "mobile-menu",
						class: ""
					}).insertAfter("#header");
					
					$("#mobile-menu .megamenu > a").on("click", function(e) {
                        
						e.preventDefault();
						
						$(this).toggleClass("open").next("div").slideToggle(300);
						
                    });
					
					$("#mobile-menu .dropdown > a").on("click", function(e) {
                        
						e.preventDefault();
						
						$(this).toggleClass("open").next("ul").slideToggle(300);
						
                    });
				
				}
				
			}
				
		} else {
			
			$("#mobile-menu").hide();
			
		}
		
	}
	
	
	// HEADER SEARCH //
	function header_search() {	
		
		$(".search a").on("click", function(e) { 
	
			e.preventDefault();
			
			if(!$(".search a").hasClass("open")) {
			
				$("#search-form-container").addClass("open-search-form").fadeIn(300)
				
			} else {
				
				$("#search-form-container").removeClass("open-search-form").fadeOut(100);
			
			}
			
		});
		
		$("#search-form").after('<a class="search-form-close" href="#">x</a>');
		
		$("#search-form-container a.search-form-close").on("click", function(event){
			
			event.preventDefault();
			$("#search-form-container").removeClass("open-search-form").fadeOut(100);
			
		});
		
	}
	
	
	// STICKY //
	function sticky() {
		
		var sticky_point = $("#header-top").innerHeight() + $("#header").innerHeight() + 20;
		
		$("#header").clone().attr({
			id: "header-sticky",
			class: ""
		}).insertAfter("header");
		
		$(window).scroll(function(){
			
			if ($(window).scrollTop() > sticky_point) {  
				$("#header-sticky").slideDown(300).addClass("header-sticky");
				$("#header .menu ul, #header .menu .megamenu-container").css({"visibility": "hidden"});
			} else {
				$("#header-sticky").slideUp(100).removeClass("header-sticky");
				$("#header .menu ul, #header .menu .megamenu-container").css({"visibility": "visible"});
			}
			
		});
		
	}
	
 
	// PROGRESS BARS // 
	function progress_bars() {
		
		$(".progress .progress-bar:in-viewport").each(function() {
			
			if (!$(this).hasClass("animated")) {
				$(this).addClass("animated");
				$(this).animate({
					width: $(this).attr("data-width") + "%"
				}, 2000);
			}
			
		});
		
	}


	// CHARTS //
	function pie_chart() {
		
		if (typeof $.fn.easyPieChart !== 'undefined') {
		
			$(".pie-chart:in-viewport").each(function() {
				
				$(this).easyPieChart({
					animate: 1500,
					lineCap: "square",
					lineWidth: $(this).attr("data-line-width"),
					size: $(this).attr("data-size"),
					barColor: $(this).attr("data-bar-color"),
					trackColor: $(this).attr("data-track-color"),
					scaleColor: "transparent",
					onStep: function(from, to, percent) {
						$(this.el).find(".pie-chart-percent .value").text(Math.round(percent));
					}
				});
				
			});
			
		}
		
	}
	
	// COUNTER //
	function counter() {
		
		if (typeof $.fn.jQuerySimpleCounter !== 'undefined') {
		
			$(".counter .counter-value:in-viewport").each(function() {
				
				if (!$(this).hasClass("animated")) {
					$(this).addClass("animated");
					$(this).jQuerySimpleCounter({
						start: 0,
						end: $(this).attr("data-value"),
						duration: 2000
					});	
				}
			
			});
			
		}
	}
	
	
	// LOAD MORE PROJECTS //
	function load_more_projects() {
	
		var number_clicks = 0;
		
		$(".load-more").on("click", function(e) {
			
			e.preventDefault();
			
			if (number_clicks == 0) {
				$.ajax({
					type: "POST",
					url: $(".load-more").attr("href"),
					dataType: "html",
					cache: false,
					msg : '',
					success: function(msg) {
						$(".isotope").append(msg);	
						$(".isotope").imagesLoaded(function() {
							$(".isotope").isotope("reloadItems").isotope();
							$(".fancybox").fancybox({
								prevEffect: 'none',
								nextEffect: 'none'
							});
							$(".portfolio-item").hoverdir();
						});
						number_clicks++;
						$(".load-more").html("Nothing to load");
					}
				});
			}
			
		});
		
	}
	
	
	// SHOW/HIDE SCROLL UP //
	function show_hide_scroll_top() {
		
		if ($(window).scrollTop() > $(window).height()/2) {
			$("#scroll-up").fadeIn(300);
		} else {
			$("#scroll-up").fadeOut(300);
		}
		
	}
	
	
	// SCROLL UP //
	function scroll_up() {				
		
		$("#scroll-up").on("click", function() {
			$("html, body").animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
	}
	
	
	// SMOOTH SCROLLING //
	function smooth_scrolling() {
		
		$("#page-header .go-to-section").on("click", function() {
			
			if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			
				if (target.length) {
					$('html,body').animate({
						scrollTop: target.offset().top - 120
					}, 1000);
				
					return false;
				
				}
			
			}
			
		});
		
	}
	
	
	// MULTILAYER PARALLAX //
	function multilayer_parallax() {
		
		$(".multilayer-parallax .parallax-layer").each(function(){
			
			var x = parseInt($(this).attr("data-x"), 10),
				y = parseInt($(this).attr("data-y"), 10);
			
			$(this).css({
				"left": x + "%", 
				"top": y + "%"
			});
			
		});

	}
	
	
	// OVERLAPPING //
	function overlapping() {
		
		if ($(window).width() > 767) {
		
			$(".overlap-section ul li").each(function(){
				
				var x = parseInt($(this).css("top"), 10);
				$(this).next("li").css({"top": x + 100 + "px"});
				
			});
			
			$(".overlap-section").css({"min-height": $(".overlap-section ul").height() + parseInt($(".overlap-section ul li:last-child").css("top"), 10) + "px"});
		
		}
		
	}
	
	
	// FULL SCREEN //
	function full_screen() {

		if ($(window).width() > 767) {
			$(".full-screen").css("height", $(window).height() + "px");
		} else {
			$(".full-screen").css("height", "auto");
		}

	}
	
	
	// HEADLINE ANIMATION //
	function headline_animation() {
		
		headline_animation = new WOW({
			boxClass: 'headline',
			animateClass: 'headline-animation',
			offset: 150,
			mobile: false,
			live: true
		});
		
		headline_animation.init();
		
	}
	
	
	// ANIMATIONS //
	function animations() {
		
		animations = new WOW({
			boxClass: 'wow',
			animateClass: 'animated',
			offset: 100,
			mobile: false,
			live: true
		});
		
		animations.init();
		
	}
	
	
	// PROCESS STEPS //
	function process_steps() {
		
		$(".process-steps .step a").on("click", function(e) {
			
			e.preventDefault();
			$(this).toggleClass("opened").next(".step-details").slideToggle(300);
			
		});
		
		$(".process-steps .step").each(function() {
			
			if ($(this).hasClass("active")) {
				$(this).find("a").addClass("opened").next(".step-details").slideDown(300);
			}
			
		});
		
	}
	
	
	// DOCUMENT READY //
	$(document).ready(function(){
		
		// STICKY //
		sticky();
		
		
		// MENU //
		if (typeof $.fn.superfish !== 'undefined') {
			
			$(".menu").superfish({
				delay: 500,
				animation: {
					opacity: 'show',
					height: 'show'
				},
				speed: 'fast',
				autoArrows: true
			});
			
		}
		
		
		// SHOW/HIDE MOBILE MENU //
		show_hide_mobile_menu();
		
		
		// MOBILE MENU //
		mobile_menu();
		
		
		// HEADER SEARCH //
		header_search();
		
		
		// FANCYBOX //
		if (typeof $.fn.fancybox !== 'undefined') {
		
			$(".fancybox").fancybox({
				prevEffect: 'none',
				nextEffect: 'none'
			});
		
		}
		
		// REVOLUTION SLIDER //
		if (typeof $.fn.revolution !== 'undefined') {
			
			$(".rev_slider").revolution({
				sliderType: "standard",
				sliderLayout: "auto",
				delay: 9000,
				navigation: {
					arrows:{
						style: "custom",
						enable: true,
						hide_onmobile: true,
						hide_onleave: false,
						hide_delay: 200,
						hide_delay_mobile: 1200,
						hide_under: 0,
						hide_over: 9999,
						tmp: '',
						left: {
							h_align: "left",
							v_align: "center",
							h_offset: 20,
							v_offset: 0
						 },
						 right: {
							h_align: "right",
							v_align: "center",
							h_offset: 20,
							v_offset: 0
						 }
					},
					bullets:{
						style: "custom",
						enable: true,
						hide_onmobile: false,
						hide_onleave: false,
						hide_delay: 200,
						hide_delay_mobile: 1200,
						hide_under: 0,
						hide_over: 9999,
						tmp: '', 
						direction: "horizontal",
						space: 10,       
						h_align: "center",
						v_align: "bottom",
						h_offset: 0,
						v_offset: 40
					},
					touch:{
						touchenabled: "on",
						swipe_treshold: 75,
						swipe_min_touches: 1,
						drag_block_vertical: false,
						swipe_direction: "horizontal"
					}
				},			
				parallax:{
					type: "mouse",
					levels: [5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85],
					origo: "enterpoint",
					speed: 400,
					bgparallax: "false",
					disable_onmobile: "off"
				},
				gridwidth: 1170,
				gridheight: 650		
			});
			
		}
	
	
		// OWL Carousel //
		if (typeof $.fn.owlCarousel !== 'undefined') {
			
			// LATEST POSTS SLIDER //
			var latest_posts_slider = $(".owl-carousel.latest-posts-slider").owlCarousel({
				singleItem: true,
				slideSpeed: 200,
				autoPlay: true,
				stopOnHover: true,
				navigation: false,
				navigationText: false,
				pagination: false,
				paginationNumbers: false,
				mouseDrag: true,
				touchDrag: true,
				transitionStyle: false
			});
			
			$(".next").on("click", function(){
				latest_posts_slider.trigger('owl.next');
			});
			
			$(".prev").on("click", function(){
				latest_posts_slider.trigger('owl.prev');
			});
			
			// IMAGES SLIDER //
			$(".owl-carousel.images-slider").owlCarousel({
				singleItem: true,
				slideSpeed: 200,
				autoPlay: true,
				stopOnHover: true,
				navigation: false,
				navigationText: false,
				pagination: true,
				paginationNumbers: false,
				mouseDrag: true,
				touchDrag: true,
				transitionStyle: false
			});
			
			// PORTFOLIOS SLIDER //
			$(".owl-carousel.portfolios-slider").owlCarousel({
				items: 4,
				itemsDesktop: [1199,4],
				itemsDesktopSmall: [991,3],
				itemsTablet: [767,2],
				itemsMobile: [479,1],
				slideSpeed: 500,
				autoPlay: true,
				stopOnHover: true,
				navigation: false,
				navigationText: false,
				pagination: true,
				paginationNumbers: false,
				mouseDrag: true,
				touchDrag: true,
				transitionStyle: false
			});
			
			// TESTIMONIALS SLIDER //
			var testimonials_slider = $(".owl-carousel.testimonials-slider").owlCarousel({
				singleItem: true,
				slideSpeed: 200,
				autoPlay: true,
				stopOnHover: true,
				navigation: false,
				navigationText: false,
				pagination: true,
				paginationNumbers: false,
				mouseDrag: true,
				touchDrag: true,
				transitionStyle: "fade"
			});
			
			$(".next").on("click", function(){
				testimonials_slider.trigger('owl.next');
			});
			
			$(".prev").on("click", function(){
				testimonials_slider.trigger('owl.prev');
			});
			
			// LOGOS SLIDER //
			$(".owl-carousel.logos-slider").owlCarousel({
				items: 5,
				itemsDesktop: [1199,5],
				itemsDesktopSmall: [991,4],
				itemsTablet: [767,2],
				itemsMobile: [479,1],
				slideSpeed: 500,
				autoPlay: true,
				stopOnHover: true,
				navigation: true,
				navigationText: false,
				pagination: false,
				paginationNumbers: false,
				mouseDrag: true,
				touchDrag: true,
				transitionStyle: false
			});
		
		}
		
		
		// GOOGLE MAPS //
		if (typeof $.fn.gmap3 !== 'undefined') {

            $('.map').each(function() {

                var data_zoom = 15,
                    data_height,
                    data_popup = false;

                if ($(this).attr('data-zoom') !== undefined) {
                    data_zoom = parseInt($(this).attr('data-zoom'), 10);
                }

                if ($(this).attr('data-height') !== undefined) {
                    data_height = parseInt($(this).attr('data-height'), 10);
                }

                if ($(this).attr('data-address-details') !== undefined) {

                    data_popup = true;

                    var infowindow = new google.maps.InfoWindow({
                        content: $(this).attr('data-address-details')
                    });

                }


                $(this)
                    .gmap3({
                        address: $(this).attr('data-address'),
                        zoom: data_zoom,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        scrollwheel: false
                    })
                    .marker([
                        {address: $(this).attr('data-address')}
                    ])
                    .on({
                        click: function(marker, event) {
                            if (data_popup) {
                                infowindow.open(marker.get('map'), marker);
                            }
                        }
                    });

                $(this).css('height', data_height + 'px');

            });

        }
		
		
		// ISOTOPE //
		if ((typeof $.fn.imagesLoaded !== 'undefined') && (typeof $.fn.isotope !== 'undefined')) {
		
			$(".isotope").imagesLoaded( function() {
				
				var container = $(".isotope");
				
				container.isotope({
					itemSelector: '.isotope-item',
					layoutMode: 'masonry',
					transitionDuration: '0.8s'
				});
				
				$(".filter li a").on("click", function() {
					$(".filter li a").removeClass("active");
					$(this).addClass("active");
		
					var selector = $(this).attr("data-filter");
					container.isotope({
						filter: selector
					});
		
					return false;
				});
		
				$(window).resize(function() {
					container.isotope();
				});
				
			});
			
		}
		
		
		// LOAD MORE PORTFOLIO ITEMS //
		load_more_projects();
		
		
		// PLACEHOLDER //
		if (typeof $.fn.placeholder !== 'undefined') {
			
			$("input, textarea").placeholder();
			
		}
		
		
		// CONTACT FORM VALIDATE & SUBMIT //
		// VALIDATE //
		if (typeof $.fn.validate !== 'undefined') {
		
			$("#contact-form").validate({
				rules: {
					name: {
						required: true
					},
					email: {
						required: true,
						email: true
					},
					subject: {
						required: true
					},
					message: {
						required: true,
						minlength: 10
					}
				},
				messages: {
					name: {
						required: "Please enter your name!"
					},
					email: {
						required: "Please enter your email!",
						email: "Please enter a valid email address"
					},
					subject: {
						required: "Please enter the subject!"
					},
					message: {
						required: "Please enter your message!"
					}
				},
					
				// SUBMIT //
				submitHandler: function(form) {
					var result;
					$(form).ajaxSubmit({
						type: "POST",
						data: $(form).serialize(),
						url: "assets/php/send.php",
						success: function(msg) {
							
							if (msg == 'OK') {
								result = '<div class="alert alert-success">Your message was successfully sent!</div>';
								$("#contact-form").clearForm();
							} else {
								result = msg;
							}
							
							$("#alert-area").html(result);
		
						},
						error: function() {
		
							result = '<div class="alert alert-danger">There was an error sending the message!</div>';
							$("#alert-area").html(result);
		
						}
					});
				}
			});
			
		}
		
		
		// MULTILAYER PARALLAX //
		multilayer_parallax();
		
		
		// PARALLX //
		if (typeof $.fn.stellar !== 'undefined') {
		
			if (!is_touch_device()) {
				
				$(window).stellar({
					horizontalScrolling: false,
					verticalScrolling: true,
					responsive: true
				});
				
			}
		
		}
		
		
		// SHOW/HIDE SCROLL UP
		show_hide_scroll_top();
		
		
		// SCROLL UP //
		scroll_up();
		
		
		// SMOOTH SCROLLING //
		smooth_scrolling();
		
		
		// YOUTUBE PLAYER //
		if (typeof $.fn.mb_YTPlayer !== 'undefined') {
			
			$(".youtube-player").mb_YTPlayer();
		
		}
		
		
		// COUNTDOWN //
		if (typeof $.fn.countdown !== 'undefined') {
			
			$(".countdown").countdown('2020/12/01 00:00', function(event) {
				$(this).html(event.strftime(
					'<div><div class="counter">%-D</div> <span>Days</span></div>' +
					'<div><div class="counter">%-H</div> <span>Hours</span></div>' +
					'<div><div class="counter">%-M</div> <span>Minutes</span></div>' +
					'<div><div class="counter">%S</div> <span>Seconds</span></div>'
				));
			});
		
		}
		
		
		// HOVERDIR //
		if (typeof $.fn.hoverdir !== 'undefined') {
			
			$(".portfolio-item").hoverdir();
		
		}
		
		// OVERLAPPING //
		overlapping();
		
		
		// FULL SCREEN //
		full_screen();
		
		
		// HEADLINE ANIMATIONS //
		headline_animation();
		
		
		// ANIMATIONS //
		animations();
		
		
		// PROCESS STEPS //
		process_steps();
		
	});

	
	// WINDOW SCROLL //
	$(window).scroll(function() {
		
		progress_bars();
		pie_chart();
		counter();
		show_hide_scroll_top();
		
	});
	
	
	// WINDOW RESIZE //
	$(window).resize(function() {

		mobile_menu();
		overlapping();
		full_screen();
		
	});
	
})(window.jQuery);