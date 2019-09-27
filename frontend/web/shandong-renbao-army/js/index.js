  //自适应
  (function(win, doc) {
    if (!win.addEventListener) return;
    var html = document.documentElement;

    function setFont() {
        var html = document.documentElement;
        var k = 750;
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
// 隐藏弹窗
$('#pop1').on('click', function(){
    pop1.style="display:none"
});
// 弹窗
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
        $('body').append('<div name="overlay" for=' + id + ' style="width:100%;height:100%;top:0;left:0;z-index:997;background:rgba(0,0,0,0.8);"></div>');
    }
}
function popHide() {
    $('[for="pop"]').hide().attr('style', '');
}    
// 判断安卓或是ios
$(function () {
    var u = navigator.userAgent,
        app = navigator.appVersion;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //g
    var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    if (isAndroid) {
        //这个是安卓操作系统
        console.log(1);
        $('.pop').addClass('pop-anzhuo');
        console.log('index-anzhuo');
        
    }
    if (isIOS) {
        //这个是ios操作系统
        console.log(2);
        $('.pop').addClass('pop-ios');
    }
});

// 名单滚动  
var area =document.getElementById('scrollBox');
var con1 = document.getElementById('con1');
var con2 = document.getElementById('con2');
con2.innerHTML=con1.innerHTML;
function scrollUp(){
if(area.scrollTop>=con1.offsetHeight){
    area.scrollTop=0;
}else{
    area.scrollTop++
}
}                
var time = 50;
var mytimer=setInterval(scrollUp,time);
area.οnmοuseοver=function(){
    clearInterval(mytimer);
}
area.οnmοuseοut=function(){
    mytimer=setInterval(scrollUp,time);
}
