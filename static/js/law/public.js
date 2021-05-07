/**
 * Created by Administrator on 2017/1/22.
 */
(function (doc, win) {
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function () {
            var clientWidth = docEl.clientWidth;
            if (!clientWidth) return;
            if(clientWidth>=640)
            {
                docEl.style.fontSize = '100px';
            }
            else
            {
                docEl.style.fontSize = 100 * (clientWidth /640) + 'px';
                //console.log(docEl.style.fontSize);
            }
        };

    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);



$('.questions-option ').on('click','p',function () {
    if($('.next-question').hasClass('disabled')){
        $('.next-question').removeClass('disabled')
    }
    $(this).find('em').addClass('active').parent().siblings().find('em').removeClass('active')
})

//$('.next-question').on('click',function () {
//    var _num =  $('.questions-num').find('span').text();
//    _num++;
//    $('.questions-num').find('span').text(_num)
//    $(this).hide();
//    $('.submit').show();
//})

$('.errorResult-show').on('click',function () {
    $('.errorResultMask').show();
})

$('.errorResult-close').on('click',function () {
    $('.errorResultMask').hide();
})


