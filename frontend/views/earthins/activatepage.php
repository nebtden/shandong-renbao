<?php

use yii\helpers\Url;
use frontend\controllers\EarthinsController;

?>
<?php $this->beginBlock('hStyle'); ?>
<style>
    html, body {
        min-height: 100%;
    }
</style>
<?php $this->endBlock('hStyle'); ?>
<body class="h3-bg">
<div class="top-tit">
    <img src="<?php echo EarthinsController::STATIC_PATH; ?>images/jh_txt.png" alt="">
</div>
<div class="from-box">
    <div class="iptBox">
        <span class="iptBox-left">姓名</span>
        <input class="iptBox-right" type="text" name="realname">
    </div>
    <div class="iptBox">
        <span class="iptBox-left">性别</span>
        <div class="iptBox-right text-c">
            <label class="check-btn sle-man active" data-name="1"><i></i><span>男</span></label>
            <label class="check-btn sle-woman" data-name="2"><i></i>女</label>
        </div>
    </div>
    <div class="iptBox">
        <input class="iptBox-right" name="mobile" placeholder="请输入您的手机号" maxlength="11" type="text">
    </div>
    <div class="iptBox ">
        <input class="iptBox-right" name="code" placeholder="验证码" maxlength="6" type="text">
        <a class="yzm " id="J_GetCode">获取验证码</a>
    </div>
    <div class="iptBox">
        <span class="iptBox-left">券码</span>
        <input class="iptBox-right" type="text" name="packagepwd">
    </div>
    <div class="btn-box send-comfirm ">
        <a href="javascript:void (0)">立即激活</a>
    </div>
    <div class="logo">
        <img src="<?php echo EarthinsController::STATIC_PATH; ?>images/logo_icon.png" alt="">
    </div>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    var testMobile = function (m) {
        var reg = /^1[0-9]{10}$/;
        return reg.test(m);
    };
    var $getCode = $('#J_GetCode');
    /* 定义参数 */
    $getCode.sendCode({
        disClass: 'btn-disabled ',
        secs: 59,
        run: false,
        runStr: '{%s}秒',
        resetStr: '重新获取'
    });
    $getCode.on('touchstart', function () {
        var mobile = $("input[name=mobile]").val();
        if (!testMobile(mobile)) {
            YDUI.dialog.toast('请输入正确的手机号码', 'none', 1500);
            return false;
        }
        /* ajax 成功发送验证码后调用【start】 */
        YDUI.dialog.loading.open('发送中');
        $.post("<?php echo Url::to(['sendsms']);?>", {mobile: mobile}, function (json) {
            YDUI.dialog.loading.close();
            if (json.status == <?php echo SUCCESS_STATUS;?>) {
                $getCode.sendCode('start');
                YDUI.dialog.toast('验证码已发送', 'none', 1500);
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });
    //提交确认
    var isSubmit = false;
    $('.send-comfirm').on('touchstart', function () {
        if (isSubmit) return false;
        var mobile = $("input[name=mobile]").val();
        if (!testMobile(mobile)) {
            YDUI.dialog.toast('请输入正确的手机号码', 'none', 1500);
            return false;
        }
        var code = $("input[name=code]").val();
        if (!code.length) {
            YDUI.dialog.toast('请输入验证码', 'none', 1500);
            return false;
        }
        var realname = $("input[name=realname]").val();
        var packagepwd = $("input[name=packagepwd]").val();
        var sex = $('.active').data('name');
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['activate-page'])?>", {
            mobile: mobile,
            code: code,
            realname: realname,
            packagepwd: packagepwd,
            sex: sex
        }, function (json) {
            isSubmit = false;
            YDUI.dialog.loading.close();
            if (json.status == <?php echo SUCCESS_STATUS;?>) {
                YDUI.dialog.toast('绑定成功', 'none', 1500, function () {
                    window.location.href = json.url;
                });
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>
</body>
