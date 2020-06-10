<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/25
 * Time: 13:59  servicelogin
 */

use yii\helpers\Url;
?>
<!DOCTYPE html>
<html class="no-js">

<head>
    <title>云车客服核销后台（山西国寿)</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimum-scale=1,maximum-scale=1">
    <meta name="renderer" content="webkit">
    <meta name="x5-orientation" content="portrait">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta http-equiv="expires" content="10">
    <meta name="mobile-web-app-capable" content="yes">
    <script src="/frontend/web/sincerethai/js/jquery-2.2.0.min.js"></script>
    <script src="/frontend/web/servicelogin/js/index.js"></script>
    <link rel="stylesheet" type="text/css" href="/frontend/web/servicelogin/css/public.css">
</head>

<body>
<div class="bm-number">
    <div class="con6box">
        <h1>云车客服核销后台（山西国寿）</h1>
        <div class=" con5">
            <ul class="con5list com5list">
                <li class="inputName">
                    <input type="tel" name="mobile"  class="con7input1 con7input3">
                    <span class="con5gonghao">手机号:</span>
                </li>
                <li class="inputName">
                    <input type="password" name="code" class="con7input1 con7input3">
                    <span class="con5gonghao">密<b>码</b>:</span>
                </li>
            </ul>
            <a href="javascript:;" class="con5-btn" id="send-comfirm"> 登录 </a>
        </div>
    </div>
</div>
</body>
</html>
<script>
    var testMobile = function(m){
        var reg = /^1[0-9]{10}$/;
        return reg.test(m);
    };
    var isSubmit = false;
    $('#send-comfirm').on('touchstart',function(){
        if(isSubmit) return false;
        var mobile = $("input[name=mobile]").val();
        var code = $("input[name=code]").val();
        if(isSubmit){
            alert('服务器正忙，请重新提交');
            return false;
        }
        if(!testMobile(mobile)){
            alert('请输入正确的手机号码');
            return false;
        }
        if(!code){
            alert('请输入密码');
            return false;
        }
        isSubmit = true;
        $.post("<?php echo Url::to(['customer-service-login'])?>",{mobile:mobile,code:code},function(json){
            isSubmit = false;

            if(json.status == 1){
                 window.location.href = json.url;
            }else{
                alert(json.msg);
            }
        },'json');
    });
</script>
