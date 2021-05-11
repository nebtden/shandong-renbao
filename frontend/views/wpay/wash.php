<?php

use yii\helpers\Url;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <title>洗车服务</title>
    <link rel="stylesheet" href="/frontend/web/hbtp/css/ydui.css"/>
    <link rel="stylesheet" href="/frontend/web/hbtp/css/common.css">
    <link rel="stylesheet" href="/frontend/web/hbtp/css/all.css">
    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="/frontend/web/hbtp/js/ydui.flexible.js"></script>
</head>
<body>
<div class="coupon-wrapper">
    <div class="coupon">
        <div class="left">
            <div class="up">
                <i>¥</i><span>15</span><em>¥30</em>
            </div>
            <div class="down">价值30元/次洗车服务</div>
        </div>
        <div class="right">洗<br>车<br>优<br>惠<br>券</div>
    </div>
    <div class="num-opreate-wrapper">
            <span class="m-spinner" id="J_Quantity">
                <a href="javascript:;" class="J_Del"></a>
                <input type="text" class="J_Input" value="1" placeholder=""/>
                <a href="javascript:;" class="J_Add"></a>
            </span>
        <input type="hidden" name="buy_num" value="1">
    </div>
    <div class="notice">
        <span class="title">使用须知:</span>
        <ul class="notice-ul">
            <li>购买后可在订单中心查看订单并激活，本服务不可退换。</li>
            <li>请在<i class="same-color">60天内激活</i>，否则会自动失效并不予退款。</li>
            <li>激活完成后请在<i class="same-color">当月30日前</i>使用完毕，否则自动失效并不予退款。</li>
            <li>任何问题请拨打客服热线<a class="same-color" href="tel:400-617-1981">400-617-1981</a>。</li>
        </ul>
    </div>
</div>
<div class="user-input-wrapper">
    <div class="user-input-info">
        <ul class="input-info-ul">
            <li>
                <input type="tel" id="mobile" placeholder="输入手机号">
            </li>
            <li>
                <input type="text" id="code" placeholder="输入验证码">
                <button class="send-code" type="button" id="J_GetCode">发送验证码</button>
            </li>
            <li>
                <input type="text" id="promotioncode" placeholder="请输入平安好车主优惠码">
            </li>
        </ul>
        <div class="commom-btn commom-buy">
            <button type="button" class="btn-block btn-primary" id="tobuy">提交并购买</button>
        </div>
    </div>
</div>

<script src="/frontend/web/hbtp/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/hbtp/js/ydui.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo $alxg_sign['appId']; ?>', // 必填，公众号的唯一标识
        timestamp: <?php echo $alxg_sign['timestamp'] ?> , // 必填，生成签名的时间戳
        nonceStr: '<?php echo $alxg_sign['noncestr'] ?>', // 必填，生成签名的随机串
        signature: '<?php echo $alxg_sign['signature'] ?>',// 必填，签名，见附录1
        jsApiList: [
            'checkJsApi',
            'chooseWXPay',
        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    // js防止安卓手机软键盘弹出挤压页面导致变形
    $('.input-info-ul>li>input').on('focus', function (e) {
        $('body').height($('body')[0].clientHeight);
    });
    /* 定义参数发送验证码 */
    var $getCode = $('#J_GetCode');
    $getCode.sendCode({
        disClass: 'btn-disabled',
        secs: 59,
        run: false,
        runStr: '{%s}秒后重新获取',
        resetStr: '重新获取验证码'
    });
    var testMobile = function (m) {
        var reg = /^1[0-9]{10}$/;
        return reg.test(m);
    };
    var getMobileCode = false;
    $getCode.on('click', function () {
        if (getMobileCode) return false;
        var mobile = $("#mobile").val();
        if (!testMobile(mobile)) {
            YDUI.dialog.toast('请输入正确的手机号码', 'none', 1000);
            return false;
        }
        /* ajax 成功发送验证码后调用【start】 */
        getMobileCode = true;
        YDUI.dialog.loading.open('发送中');
        $.post("<?php echo Url::to(['sendcode'])?>", {mobile: mobile}, function (json) {
            YDUI.dialog.loading.close();
            getMobileCode = false;
            if (json.status === 1) {
                $getCode.sendCode('start');
                YDUI.dialog.toast('验证码已发送', 'none', 1500);
            } else {
                YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
        setTimeout(function () {


        }, 1500);
    });
    //数字加减
    $('#J_Quantity').spinner({
        input: '.J_Input',
        add: '.J_Add',
        minus: '.J_Del',
        unit: function () {
            return 1;
        },
        max: function () {
            // return (1 + 2 + 3 + 4 + 5) * 5;
        },
        callback: function (value, $ele) {
            // $ele 当前文本框[jQuery对象]
            // $ele.css('background', '#FF5E53');
            // console.log('值：' + value);
            $("input[name=buy_num]").val(value);
        }
    });

    function wxpay(param,url){
        wx.chooseWXPay({
            timestamp: param['timeStamp'],
            nonceStr: param['nonceStr'],
            package: param['package'],
            signType: param['signType'],
            paySign: param['paySign'],
            success: function (res) {
                // 支付成功后的回调函数
                window.location.href = url;
            },
            fail: function (res) {
                YDUI.dialog.alert('支付失败');
            }
        });
    }

    //提交订单
    var isSubmit = false;
    $("#tobuy").click(function () {
        if (isSubmit) return false;
        var mobile = $("#mobile").val();
        if (!testMobile(mobile)) {
            YDUI.dialog.toast('请输入正确的手机号码', 'none', 1000);
            return false;
        }
        var code = $("#code").val();
        if (!code.length) {
            YDUI.dialog.toast('请输入验证码', 'none', 1000);
            return false;
        }
        var promotion = $("#promotioncode").val();
        if (!promotion.length) {
            YDUI.dialog.toast('请输入优惠码', 'none', 1000);
            return false;
        }
        var num = $("input[name=buy_num]").val();
        if (isNaN(num) || !num) {
            YDUI.dialog.toast('请输入正确的购买数量', 'none', 1000);
            return false;
        }
        var data = {
            mobile: mobile,
            code: code,
            promotion: promotion,
            num: num
        };
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['washpay']);?>",data,function(json){
             isSubmit = false;
            YDUI.dialog.loading.close();
            //console.log(json.data);
            if(json.status === 1){
                wxpay($.parseJSON(json.data),json.url);
            }else{
                YDUI.dialog.toast(json.msg,'none',1000);
            }
        },'json');
    });
</script>
</body>
</html>