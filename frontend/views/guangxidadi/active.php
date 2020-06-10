<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>大地保险尊享代驾</title>
  <link rel="stylesheet" type="text/css" href="/frontend/web/cloudcarv2/css/ydui.css">
  <link rel="stylesheet" type="text/css" href="/frontend/web/guangxidadi/css/common.css">
  <link rel="stylesheet" type="text/css" href="/frontend/web/guangxidadi/css/style.css">
  <script src="/frontend/web/cloudcarv2/js/ydui.flexible.js"></script>
</head>
<body>
<section class="contentContain" style="height: 100%">
    <div class="imgContain" style="height: 100%">
        <img src="/frontend/web/guangxidadi/images/3.jpg" style="height: 100%; display: block;">
        <div class="formContain">
            <div class="title">
                <img src="/frontend/web/guangxidadi/images/biaoti.png">
            </div>
            <ul class="formpadding">
                <li>
                    <input type="text" name="" class="username" placeholder="请输入您的姓名">
                    <div class="sex">
                        <label>性别</label>
                        <input type="radio" name="sex" checked="">
                        <span>男</span>
                        <input type="radio" name="sex">
                        <span>女</span>
                    </div>
                </li>
                <li>
                    <input type="tel" name="mobile" class="tel" placeholder="请输入您的手机号">
                </li>
                <li>
                    <div class="yanzhengma">
                        <input type="text" name="code" class="codeinput" placeholder="请输入验证码">
                        <button type="button" class="btn btn-warning" id="J_GetCode">免费获取验证码</button>
                    </div>
                </li>
                <div class="btnContain">
                    <button type="submit" class="btn btn-primary send-comfirm">提交</button>
                </div>
        </div>
    </div>

</section>
</body>
<script src="/frontend/web/cloudcarv2/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>
<script>
    var $getCode = $('#J_GetCode');

    /* 定义参数 */
    $getCode.sendCode({
        disClass: 'btn-disabled',
        secs: 15,
        run: false,
        runStr: '{%s}秒后重新获取',
        resetStr: '重新获取验证码'
    });
    var testMobile = function (m) {
        var reg = /^1[0-9]{10}$/;
        return reg.test(m);
    };

    $getCode.on('touchstart', function () {
        var mobile = $("input[name=mobile]").val();
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
        /* ajax 成功发送验证码后调用【start】 */
        YDUI.dialog.loading.open('发送中');
        $.post("<?php echo Url::to(['caruser/sendsms']);?>",{mobile:mobile},function(json){
            YDUI.dialog.loading.close();
            if(json.status == 1){
                $getCode.sendCode('start');
                YDUI.dialog.toast('验证码已发送', 'none', 1500);
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });
    //提交确认
    var isSubmit = false;
    $('.send-comfirm').on('touchstart',function(){
        console.log(1);
        if(isSubmit) return false;
        var mobile = $("input[name=mobile]").val();
        if(!testMobile(mobile)){
            YDUI.dialog.toast('请输入正确的手机号码','none',1500);
            return false;
        }
        var code = $("input[name=code]").val();
        if(!code.length){
            YDUI.dialog.toast('请输入验证码','none',1500);
            return false;
        }
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['caruser/bindmobile'])?>",{mobile:mobile,code:code},function(json){
            isSubmit = false;
            YDUI.dialog.loading.close();
            if(json.status == 1){
                YDUI.dialog.toast('绑定成功', 'none', 1500,function(){
                    window.location.href = json.url;
                });
            }else{
                YDUI.dialog.alert(json.msg);
            }
        },'json');
    });

</script>
</html>