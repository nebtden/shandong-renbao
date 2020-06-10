/**
 * Created by Administrator on 2017/1/22.
 */
(function (doc, win) {
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function () {
            var clientWidth = docEl.clientWidth;
            if (!clientWidth) return;
            if(clientWidth>=750)
            {
                docEl.style.fontSize = '100px';
            }
            else
            {
                docEl.style.fontSize = 100 * (clientWidth /750) + 'px';
                //console.log(docEl.style.fontSize);
            }
        };

    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);

$(function() {
    //获取短信验证码
    var t;
    var validCode = true;
    $(".yzm").click(function() {
        var time = 60;
        var code = $(this);
        if (validCode) {
            validCode = false;
            code.html(60 + "秒");
            t = setInterval(function() {
                time--;
                code.html(time + "秒");
                if (time == 0) {
                    clearInterval(t);
                    code.html("获取验证码");
                    validCode = true;
                }
            }, 1000)
        }
    })
})


$('.check-btn').on('click',function () {
    $(this).addClass('active').siblings().removeClass('active')
})