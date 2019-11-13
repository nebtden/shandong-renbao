
$(window).on("load", function () {
  init();
  resize();
  slideChange(0);
});

var swiper;

function init() {
  swiper = new Swiper('#swiper_index', {
    direction: 'vertical',
    speed: 500,
    on: {
      slideChange: function () {
        slideChange(swiper.activeIndex);
      }
    }
  });
}

function slideChange(n) {
  $("#swiper_index .swiper-slide:eq(" + (n - 1) + ") .ele").show();
  $("#swiper_index .swiper-slide:eq(" + (n - 2) + ") .ele").hide();
  $("#swiper_index .swiper-slide:eq(" + (n + 1) + ") .ele").show();
  $("#swiper_index .swiper-slide:eq(" + (n + 2) + ") .ele").hide();
  $("#swiper_index .swiper-slide:eq(" + n + ") .ele").show();
  if ($.inArray(n, [$("#swiper_index .swiper-slide").length - 1]) > -1) {
    $(".up").hide();
  } else {
    $(".up").show();
  }
}

function resize() {
  var per_w = $("body").width() / $(".container").width();
  var per_h = $("body").height() / $(".container").height();
  var per = per_w < per_h ? per_w : per_h;
  $(".container").css('transform', 'scale(' + per + ',' + per + ')');
}
// -选中
