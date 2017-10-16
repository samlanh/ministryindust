/*
    Template Name: Dazimaxx Premium Multipurpose HTML Template
    Description: This is html5 template
    Author: HasTech
    Version: 1.0
*/
/*================================================
[  Table of contents  ]
================================================
	01. Sticky Menu
	02. Owl Carousel
	03. jQuery MeanMenu
    04. Slick Carousel
    05. Image Load & Isoptope
	06. Counter Up
	07. ScrollUp
	08. Wow js
	09. Price Slider Activate
 
======================================
[ End table content ]
======================================*/

(function ($) {
	"use strict";
    
/*------------------------------------
    01. Sticky Menu
-------------------------------------- */  
    var windows = $(window);
    var stick = $(".header-sticky");
	windows.on('scroll',function() {    
		var scroll = windows.scrollTop();
		//if (scroll < 245) {
			//stick.removeClass("sticky");
		//}else{
			//stick.addClass("sticky");
		//}
	}); 
    
/*----------------------------------------
    02. Owl Carousel
---------------------------------------- */
/*----------------------------------------
    Slider Carousel
---------------------------------------- */
    $(".slider-wrapper").owlCarousel({
        loop:true,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        smartSpeed: 2500,
        items:1,
        dotData:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });
/*--------------------------------
    Testimonial Carousel
--------------------------------- */
	$('.testimonial-carousel').owlCarousel({
		loop:true,
		margin:0,
        dots: true,
		nav:false,
		animateOut: 'slideOutDown',
		animateIn: 'slideInDown',		
		autoplay:false,
		smartSpeed:3000,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:1
			},
			1000:{
				items:1
			}
		}
	}); 
/*--------------------------------
    Blog Carousel
--------------------------------- */
	$('.blog-carousel').owlCarousel({
		loop:true,
		margin:0,
        items:3,
        dots: false,
		nav:false,	
		autoplay:false,
		smartSpeed:3000,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:2
			},
			900:{
				items:2
			},
			1000:{
				items:3
			},
			1200:{
				items:3
			}
		}
	}); 
	
	$('.blog-file-slide').owlCarousel({
		loop:true,
		margin:0,
        items:4,
        dots: false,
		nav:false,	
		autoplay:false,
		smartSpeed:3000,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:2
			},
			900:{
				items:2
			},
			1000:{
				items:4
			},
			1200:{
				items:4
			}
		}
	}); 
    
/*-------------------------------------------
    03. jQuery MeanMenu
--------------------------------------------- */
	$('.main-menu nav').meanmenu({
		meanScreenWidth: "991",
		meanMenuContainer: '.mobile-menu'
	});     
    
/*------------------------------------
	04. Slick Carousel 
--------------------------------------*/
/*------------------------------------
	Feature Slick Carousel 
--------------------------------------*/
    $('.feature-slick-carousel').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: false,
        arrows: true,
        centerMode: true,
        focusOnSelect: true,
        centerPadding: '10px',
		responsive: [
			{
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    infinite: true,
                }
			},
			{
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    arrows: false,
                }
			},
			{
                breakpoint: 500,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
			}
		]
    });
/*------------------------------------
	Feature Slick Carousel Two
--------------------------------------*/
    $('.feature-slick-carousel-two').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        dots: true,
        arrows: false,
        centerMode: true,
        focusOnSelect: true,
        centerPadding: '10px',
		responsive: [
			{
                breakpoint: 1500,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: true
                }
			},
			{
			  breakpoint: 992,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: true
                }
			},
			{
                breakpoint: 770,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
			},
			{
                breakpoint: 500,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
			}
		]
    });
    
/*-----------------------------------------
    05. Image Load & Isoptope
------------------------------------------ 
    $('.grid').imagesLoaded( function() {
		// filter items on button click
		$('.event-menu').on( 'click', 'button', function() {
		  var filterValue = $(this).attr('data-filter');
		  $grid.isotope({ filter: filterValue });
		});	
        
		// init Isotope
		var $grid = $('.grid').isotope({
		  itemSelector: '.grid-item',
            layoutMode: 'fitRows',
            percentPosition: true,
		});
	});*/	
    
/*-----------------------------------------
    06. Counter Up
------------------------------------------ 
    $('.counter').counterUp({
        delay: 70,
        time: 5000
    });*/	
	
/*------------------------------------------
    07. ScrollUp
------------------------------------------- */	
	$.scrollUp({
        scrollText: '<i class="icon-arrow-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade'
    });  
    
/*------------------------------------------
    08. Wow js
-------------------------------------------- */    
    new WOW().init();
    
/*------------------------------------------
    09. Price Slider Activate
--------------------------------------------
    $( "#slider-range" ).slider({
        range: true,
        min: 110,
        max: 300,
        values: [ 120, 240 ],
        slide: function( event, ui ) {
		$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
        }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
	   " - $" + $( "#slider-range" ).slider( "values", 1 ) );   */  
    
	 

})(jQuery);  