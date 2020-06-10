  //自适应
  (function(win, doc) {
    if (!win.addEventListener) return;
    var html = document.documentElement;

    function setFont() {
        var html = document.documentElement;
        var k = 640;
        html.style.fontSize = html.clientWidth / k * 100 + "px";
    }
    setFont();
    setTimeout(function() {
        setFont();
    }, 300);
    doc.addEventListener('DOMContentLoaded', setFont, false);
    win.addEventListener('resize', setFont, false);
    win.addEventListener('load', setFont, false);
})(window, document);

/// 弹窗
var popIsShow = false;
var popDom = null;
function popShow(id) {
    popHide();
    var p = $('#'+id);
    popDom = p;
    if (p) {
        p.show().css({
            position: 'fixed',
            top: '50%',
            left: '50%',
            marginTop: -popDom.height() / 2 + 'px',
            marginLeft: -popDom.width() / 2 + 'px',
            zIndex: 998
        });
        p.attr('for', 'pop');
        popIsShow = true;
        if ($('[for="' + id + '"]').length >= 1) return;
        $('body').append('<div name="overlay" for=' + id + ' style="width:100%;height:100%;top:0;left:0;z-index:997;background:#000; opacity:0.5"></div>');
    }
}
function popHide() {
    $('[for="pop"]').hide().attr('style', '');
}      