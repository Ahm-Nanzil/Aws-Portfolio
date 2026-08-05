// IIFE with jQuery Wrapper
(function ($) {
  "use strict";

  /*
   *----------------------------------
   * Document Ready
   *----------------------------------
   */
  $(document).ready(function () {
    $(".dropdown-toggle").dropdown();

    $(".nav_trigger").click(function () {
      $("body").toggleClass("show_sidebar");
      $(".nav_trigger .fa").toggleClass("fa-navicon fa-times"); // toggle 2 classes in Jquery: http://goo.gl/3uQAFJ - http://goo.gl/t6BQ9Q
    });

    $(".master-card").on("click", function () {
      $(".master-card").removeClass("card-active");
      $(this).addClass("card-active");
    });

    $(".header-top-lan-btn").on("click", function () {
      $(".country-name").stop().slideToggle(700);
    });

    /* Rate page */
    var letterLi = $(".first-letter > li");
    letterLi.on("click", function () {
      var letter = $(this).attr("data-tabs");
      letterLi.removeClass("active");
      $(this).addClass("active");

      $(".single-country-des").removeClass("active");
      $("." + letter).addClass("active");
    });

    /*****************
          parallax Effect
        ******************/
    if ($(window).width() > 1024) {
      if ($("body").hasClass("home")) {
        var scene = document.getElementById("scene");
        var parallax = new Parallax(scene);

        var get_app = document.getElementById("get_app");
        var parallax = new Parallax(get_app);

        var testimonialparallax = document.getElementById(
          "testimonial-parallax"
        );
        var parallax = new Parallax(testimonialparallax);
        var testimonialparallax2 = document.getElementById(
          "testimonial-parallax2"
        );
        var parallax = new Parallax(testimonialparallax2);
        var testimonialparallax3 = document.getElementById(
          "testimonial-parallax3"
        );
        var parallax = new Parallax(testimonialparallax3);
        var testimonialparallax4 = document.getElementById(
          "testimonial-parallax4"
        );
        var parallax = new Parallax(testimonialparallax4);
      }

      if ($("body").hasClass("home-page-two")) {
        var home_page_two = document.getElementById("scene");
        var parallax = new Parallax(home_page_two);

        var get_app_tow = document.getElementById("get_app_tow");
        var parallax = new Parallax(get_app_tow);

        var testimonialparallaxtow = document.getElementById(
          "testimonial-parallax-tow"
        );
        var parallax = new Parallax(testimonialparallaxtow);
        var testimonialparallax2tow = document.getElementById(
          "testimonial-parallax2-tow"
        );
        var parallax = new Parallax(testimonialparallax2tow);
        var testimonialparallax3tow = document.getElementById(
          "testimonial-parallax3-tow"
        );
        var parallax = new Parallax(testimonialparallax3tow);
        var testimonialparallax4tow = document.getElementById(
          "testimonial-parallax4-tow"
        );
        var parallax = new Parallax(testimonialparallax4tow);
      }

      if ($("body").hasClass("home-page-three")) {
        var featureBg = document.getElementById("feature-bg-img"),
          parallax = new Parallax(featureBg, {
            frictionX: 0,
          });
        var dialerAppBg = document.getElementById("dialer-app-bg"),
          parallax = new Parallax(dialerAppBg, {
            frictionX: 0,
          });
        var bannerBg = document.getElementById("banner-bg"),
          parallax = new Parallax(bannerBg, {
            frictionX: 0,
          });
        var testiBg = document.getElementById("testi-bg"),
          parallax = new Parallax(testiBg, {
            frictionX: 0,
          });
      }
    }

    /*****************
          Animation
        ******************/
    $(".wow").css("visibility", "hidden");
    var wow = new WOW({
      mobile: false,
      offset: 80,
    });
    wow.init();

    /*****************
          Rate-Slider
        ******************/
    $(".rate-active").owlCarousel({
      loop: true,
      nav: false,
      autoplay: true,
      autoplayTimeout: 8000,
      smartSpeed: 1000,
      responsive: {
        0: {
          items: 1,
        },
        992: {
          items: 2,
        },
        1600: {
          items: 3,
        },
      },
    });

    var loaderDots = $(".dot-group div");
    loaderDots.animate({
      bottom: "20px",
    });
  }); // DOM Ready

  /* ==========================================================================
   copyright year change
   ========================================================================== */

  function walkText(node) {
    var date = new Date().getFullYear();

    if (node.nodeType == 3) {
      node.data = node.data.replace(/2018/g, date);
    }
    if (node.nodeType == 1 && node.nodeName != "SCRIPT") {
      for (var i = 0; i < node.childNodes.length; i++) {
        walkText(node.childNodes[i]);
      }
    }
  }
  var copyrightReplace = document.querySelector(".copyright-text");
  walkText(copyrightReplace);
})(jQuery); // IIFE
