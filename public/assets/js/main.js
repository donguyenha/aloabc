var $ = jQuery.noConflict();
jQuery(document).ready(function($) {

  var tooltip_menu = {
    init: function() {
      $width = $(window).width();
      if ($width < 768) {
        $("#link_search_mobi").click(function() {
          $("#search_mobi .search-mobi").stop().fadeToggle("fast");
        });
        $(".bar-mobi").click(function(event) {
          $(".menu-mobi").stop().fadeToggle("fast");
        });
      } else {
        $('.jt').cluetip({
          cluetipClass: 'jtip',
          attribute: 'data-jtip',
          local: true,
          arrows: true,
          dropShadow: true,
          hoverIntent: true,
          sticky: true,
          topOffset: 10,
          mouseOutClose: 'both',
          delayedClose: 100,
          cluezIndex: 499,
          width: 350,
          arrowPixelAdded: 185,
          closePosition: 'title'
        });
      }
    }
  }

  tooltip_menu.init();
  $(window).resize(function() {
    tooltip_menu.init();
    console.log("dff")
  });

  $('#video_popup').modal();

  setTimeout(function() {
    $('.readmore').readmore({
      speed: 500,
      collapsedHeight: 140,
      lessLink: '<a href="#">Đóng <i class="fa fa-angle-double-up"></i></a>',
      moreLink: '<a href="#">Xem thêm <i class="fa fa-angle-double-down"></i></a>',
    });
  }, 2000);

  $(".mCustomScrollbar").mCustomScrollbar({
    autoHideScrollbar: true
  });

  $('.single-item').slick();

  $('.responsive').slick({
    dots: false,
    infinite: false,
    speed: 300,
    slidesToShow: 7,
    slidesToScroll: 7,
    responsive: [{
        breakpoint: 1200,
        settings: {
          slidesToShow: 6,
          slidesToScroll: 6,
          infinite: true,
        }
      }, {
        breakpoint: 1024,
        settings: {
          slidesToShow: 5,
          slidesToScroll: 5,
          infinite: true,
        }
      }, {
        breakpoint: 768,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 4
        }
      }, {
        breakpoint: 600,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3
        }
      }, {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      }
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ]
  });

  var scrollToTop = {
    /**
     * When the user has scrolled more than 100 pixels then we display the scroll to top button using the fadeIn function
     * If the scroll position is less than 100 then hide the scroll up button
     *
     * On the click event of the scroll to top button scroll the window to the top
     */
    init: function() {

      //Check to see if the window is top if not then display button
      $(window).scroll(function() {
        if ($(this).scrollTop() > 450) {
          $('.video-anchor, .scroll-to-top').fadeIn();
        } else {
          $('.video-anchor, .scroll-to-top').fadeOut();
        }
      });

      // Click event to scroll to top
      $('.scroll-to-top').click(function() {
        $('html, body').animate({ scrollTop: 0 }, 800);
        return false;
      });
    }
  };
  scrollToTop.init();

});
