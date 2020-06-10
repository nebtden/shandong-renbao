//公用弹窗函数调用
runLoginlayer();
//公用弹窗函数 div拼接
function runLoginlayer(){
    var html = '';
    html += '<div class="login-layer-outside">'+
                '<div class="login-layer">'+
                    '<div class="close-wrapper" id="close-wrapper"><i class="iconfont icon-car-guanbi"></i></div>'+
                    '<div class="hang-top"></div>'+
                    '<div class="hang-down"></div>'+
                    '<div class="title">请使用手机登录开启服务</div>'+
                    '<ul class="login-input-ul">'+
                        '<li class="tel">'+
                            '<i class="icon-phone3"></i>'+
                            '<input type="tel" placeholder="请输入手机号" >'+
                        '</li>'+
                        '<li class="code">'+
                            '<i class="iconfont icon-car-yanzhengma"></i>'+
                            '<input type="text" placeholder="请输入验证码" >'+
                            '<button type="button" class="btn send-code " id="J_GetCode">发送验证码</button>'+ 
                        '</li>'+
                    '</ul>'+
                    '<div class="footer">'+
                        '<button class="btn-block btn-primary">登录</button>'+
                    '</div>'+
                '</div>'+
            '</div>';
    $('body').prepend(html);       
}
/* 定义参数 */
$(document).find('#J_GetCode').sendCode({
    disClass: 'btn-disabled ',
    secs: 59,
    run: false,
    runStr: '重新发送{%s}',
    resetStr: '重新获取'
});
$(document).on('touchstart','#J_GetCode' ,function () {
    /* ajax 成功发送验证码后调用【start】 */
    YDUI.dialog.loading.open('发送中');
    setTimeout(function(){
        YDUI.dialog.loading.close();
        $(document).find('#J_GetCode').sendCode('start');
        YDUI.dialog.toast('已发送', 1500);
    }, 1500);
});
//关闭登录弹窗
$(document).on('click','#close-wrapper',function(e){
    $('.login-layer-outside').hide();
});
//登录确认
$('.login-layer>.footer>button ').on('click',function(){
    
});