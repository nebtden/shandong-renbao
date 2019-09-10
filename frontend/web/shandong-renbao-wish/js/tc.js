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