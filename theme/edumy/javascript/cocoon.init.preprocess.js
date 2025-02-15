/* Slider Style 5 */
$(window).on('load', function() {
  if ($(".block_cocoon_slider_5").length) {
    var bsCarouselItems = 1;
    if ($(".bs_carousel .carousel-item").length) {
      $(".bs_carousel .carousel-item").each(function(index, element) {
        if (index == 0) {
          $(".bs_carousel_prices").addClass("pprty-price-active pprty-first-time");
        }
        $(".bs_carousel_prices .property-carousel-ticker-counter").append("<span>0" + bsCarouselItems + "</span>");
        bsCarouselItems += 1;
      });
    }
    $(".bs_carousel_prices .property-carousel-ticker-total").append("<span>0" + $(".bs_carousel .carousel-item").length + "</span>");
    if ($(".bs_carousel").length) {
      $(".bs_carousel").on("slide.bs.carousel", function(carousel) {
        $(".bs_carousel_prices").removeClass("pprty-first-time");
        $(".bs_carousel_prices").carousel(carousel.to);
      });
    }
    if ($(".bs_carousel").length) {
      $(".bs_carousel").on("slid.bs.carousel", function(carousel) {
        var tickerPos = (carousel.to) * 25;
        $(".bs_carousel_prices .property-carousel-ticker-counter > span").css("transform", "translateY(-" + tickerPos + "px)");
      });
    }
    if ($(".bs_carousel .property-carousel-control-next").length) {
      $(".bs_carousel .property-carousel-control-next").on("click", function(e) {
        $(".bs_carousel").carousel("next");
      });
    }
    if ($(".bs_carousel .property-carousel-control-prev").length) {
      $(".bs_carousel .property-carousel-control-prev").on("click", function(e) {
        $(".bs_carousel").carousel("prev");
      });
    }
  }
  $(".moremenu .more-nav .dropdownmoremenu .dropdown-menu").each(function() {
    $(this).find("a").each(function() {
      if($(this).attr('href') === "#") $(this).removeAttr("href");
    })
  })
});
if ($(".bs_carousel").length) {
  $(".bs_carousel").carousel({
    interval: 6000,
    pause: "true"
  });
}
// #Fixing by MJ 02-2025 -- header nav issue in my courses page

let gp_counter = 0,
gp_nav = $('#page-content > header > div > nav'),
gp_intervalLoading = setInterval(function(){
// until the interval finds the menu or a maximum of 6 times if it already exists in the header
if($('nav.navbar').length > 0 || gp_counter > 6 ){
clearInterval(gp_intervalLoading);

if($('nav.ccn_nav_group').length == 0){
gp_nav.addClass('ccn_nav_group');
$('#respMenu').removeClass('ace-responsive-menu');
}
let xHtml = $('div.primary-navigation').html();
if(xHtml != ''){
$('div.primary-navigation').remove();
gp_nav.find('a.navbar_brand').after(
'<nav class="navbar navbar-expand" aria-label="Site Navigation" style="display:none;">'+
' <div class="primary-navigation">' + xHtml + '</div></nav>');
gp_nav.find('ul.nav').addClass('ace-responsive-menu');
$('#page-content > header > div > nav > nav.navbar').fadeIn(400);
}
}
gp_counter++;
}, 200);