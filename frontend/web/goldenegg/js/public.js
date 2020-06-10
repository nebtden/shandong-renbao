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


function sharedirect(){
    var clientWidth = $(document).width();
    var clientHight = $(document).height();
    var direct = $('<div class="direct-mask"><div id="directbox"></div></div>');
    direct.appendTo('body');
    $('body').css("overflow-y","hidden");
    direct.click(function(e){
        $(this).remove();
        $('body').css("overflow-y","auto");
    });
};
function sharePage() {
    sharedirect();
}

