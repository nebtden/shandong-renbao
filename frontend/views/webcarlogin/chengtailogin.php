<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/16
 * Time: 15:02
 */
use yii\helpers\Url;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="Tencent-CP" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="format-detection" content="telephone=no" />
    <meta content="yes" name="mobile-web-app-capable" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <title><?php echo $this->context->site_title; ?></title>
    <!-- tc -->
    <script src="/frontend/web/sincerethai/js/jquery-2.2.0.min.js"></script>
    <script src="/frontend/web/sincerethai/js/tc-show.js"></script>
    <!-- 提示信息 -->
    <link rel="stylesheet" href="/frontend/web/sincerethai/css/sweetalert.min.css">
    <script src="/frontend/web/sincerethai/js/bootstart.js"></script>
    <script src="/frontend/web/sincerethai/js/sweet.js"></script>
    <link rel="stylesheet" href="/frontend/web/sincerethai/css/content.css" />
    <script src="/frontend/web/sincerethai/js/content.js"></script>

</head>

<body>
<div class="hyxc-index">
    <a href="javascript:popShow('pop_per');" class="xiche">服务说明</a>
    <div class="import-box">
        <input type="tel" name="mobile" placeholder="请输入手机号" class="import-frame" id="numberval" autofocus="autofocus" />
        <span class="import-yzm"><input type="tel" name="code"  placeholder="请输入验证码" id="yanzhen" />
        <a href="javascript:;" class="import-btn feachBtn" id="btn">点击获取验证码</a>
      </span>
        <a href="javascript:;" class="import-submit" id="submit">提交</a>
    </div>
    <span class="chentai-kq">
           <a  href="javascript:;"  >查看我的卡券 ></a>
        </span>
</div>
<!-- 弹窗 -->
<div class="pop " id="pop_per">
    <div class="fuwu-tc">
        <b>服务使用说明</b>
        <div class="fuwu-box">
        <span>
          1.服务标准：限轿车、微型车、SUV等7座（含）以下车型，按照可享受的免费清洗次数，每月免费享受清洗服务一次；
        </span>
            <span>
          2.服务范围：全省辖区内合作网点均可提供服务（详见洗车网点）；
        </span>
            <span>
          3.服务领取、使用流程：扫二维码或点击链接 → 输入手机号及验证 →
          填写与保单一致姓名和身份证号 → 我的卡券 → 点
          击卡券去使用按钮（注意使用时间，每月一次） → 选择洗车网点 →
          获取服务二维码 → 出示给商家扫一扫并享受服务第
          二次使用扫二维码或点击链接 → 输入手机号及验证 → 我的卡券 →
          点击卡券去使用按钮（注意使用时间，每月一次） → 选 择洗车网点 →
          获取服务二维码 → 出示给商家扫一扫并享受 服务;
        </span>
            <span>
          4.注意事项：本券只限车辆普洗服务，不可抵用其他服务，本券为清洗服务包形式，即从激活之日起，开始累计，后续每月
          无论是否实际使用洗车服务，均按每月一次洗车服务使用扣除；
        </span>
            <span>
          5.服务期限；本券仅限本人当次激活使用，每月仅可使用一次，每张券有效期为一个月；
        </span>
            <span>
          6.春节期间，全国合作洗车店将暂停提供洗车服务；
        </span>
            <span>
          7.本服务最终解释权归诚泰财产保险股份有限公司所有，详情请咨询:4006171981。
        </span>
        </div>
        <a href="javascript:popHide();" title="点击关闭" class="btn close"></a>
    </div>

</div>
<!-- 错误提示框 -->

<script type="text/javascript">
    $(function () {
        // 倒计时
        var oBtn = document.getElementById('btn');
        var flag = true;
        var testMobile = function(m){
            var reg = /^1[0-9]{10}$/;
            return reg.test(m);
        };
        oBtn.addEventListener("click", function () {
            var mobile = $("input[name=mobile]").val();
            if(!testMobile(mobile)){
                swal('请输入正确的手机号码');
                return false;
            }
            $.post("<?php echo Url::to(['webcarlogin/smscode']);?>",{mobile:mobile},function(json){

                if(json.status == 1){
                    var time = 60;
                    oBtn.classList.add('disable');
                    oBtn.innerText = '已发送';
                    if (flag) {
                        flag = false;
                        var timer = setInterval(() => {
                                time--;
                        oBtn.innerText = time + '秒后重新发送';

                        if (time === 0) {
                            clearInterval(timer);
                            oBtn.innerText = '重新获取';
                            oBtn.classList.remove('disable');
                            flag = true;
                        }
                    }, 1000)}
                }else{
                    swal(json.msg);
                }
            },'json');

        });
        //
        var m = navigator.userAgent;

        var isAndroid = m.indexOf('Android') > -1 || m.indexOf('Adr') > -1; //android终端

        var isIos = !!m.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端       

        if (isIos) {
            //为input、textarea、select添加blur事件
            $('input, textarea, select').on('blur', function () {
                window.scroll(0, 0);
            });
        }


        //提交确认
        var isSubmit = false;
        $('.import-submit').on('touchstart',function(){
            style = "border:1px solid #fff";
            if(isSubmit) return false;
            var mobile = $("input[name=mobile]").val();
            if(!testMobile(mobile)){
                swal('请输入正确的手机号码');
                return false;
            }
            var code = $("input[name=code]").val();
            if(!code.length){
                swal('请输入验证码');
                return false;
            }
            if(isSubmit){
                swal('服务器繁忙，请重新提交！');
                return false;
            }
            isSubmit = true;
            $.post("<?php echo Url::to(['webcarlogin/chengtailogin'])?>",{mobile:mobile,code:code},function(json){

                isSubmit = false;
                if(json.status == 1){
                    window.location.href = json.url;
                }else{
                    swal(json.msg);
                }
            },'json');
        });


        //提交验证
        var is_Submit = false;
        $('.chentai-kq').on('touchstart',function(){
            style = "border:1px solid #fff";
            if(is_Submit) return false;
            if(is_Submit){
                swal('服务器繁忙，请重新提交！');
                return false;
            }
            is_Submit = true;
            $.post("<?php echo Url::to(['webcarlogin/checkuser'])?>",{},function(json){
                is_Submit = false;
                if(json.status == 1){
                    window.location.href = json.url;
                }else{
                    swal(json.msg);
                }
            },'json');
        });

    });



</script>
</body>

</html>
