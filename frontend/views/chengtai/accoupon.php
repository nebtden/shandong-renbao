<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/16
 * Time: 14:39
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
    <!-- 提示信息 -->
    <link rel="stylesheet" href="/frontend/web/sincerethai/css/sweetalert.min.css">
    <script src="/frontend/web/sincerethai/js/bootstart.js"></script>
    <script src="/frontend/web/sincerethai/js/sweet.js"></script>

    <link rel="stylesheet" href="/frontend/web/sincerethai/css/content.css" />
    <script src="/frontend/web/sincerethai/js/content.js"></script>
</head>

<body>
<div class="hyxc-member">
    <div class="member-box">
        <input type="text" placeholder="请输姓名" class="member-frame" id="nameval" autofocus="autofocus" />
        <input type="text" placeholder="请输入身份证号码" class="member-frame" id="numberval">
        <a href="javascript:;" class="import-submit member-btn" id="name">提交</a>
        <div class="hyxc-hint">
            温馨提示：
            <p>请填写与保单信息一致的身份信息，否则无法获得相关的权益</p>
        </div>
    </div>
</div>

<!-- 错误提示框 -->

<script type="text/javascript">
    $(function () {
        var isSubmit = false;
        $('#name').click(function () {
            style = "border:1px solid #fff";
            var name = $("#nameval").val();
            var code = $("#numberval").val();
            if(isSubmit) return false;
            if(!name.length){
                swal('请输入姓名');
                return false;
            }
            if(!code.length){
                swal('请输入身份证号');
                return false;
            }
            if(isSubmit){
                swal('服务器繁忙，请重新提交！');
                return false;
            }
            isSubmit = true;
            $.post("<?php echo Url::to(['accoupon'])?>",{preson_name:name,code:code},function(json){
                isSubmit = false;
                if(json.status == 1){
                    swal('兑换成功');
                    window.location.href = "<?php echo Url::to(['webcaruser/coupon'])?>";
                }else{
                    swal(json.msg);
                    if(json.url) {
                        window.location.href = json.url;
                    }
                }
            },'json');
        });

        var m = navigator.userAgent;

        var isAndroid = m.indexOf('Android') > -1 || m.indexOf('Adr') > -1; //android终端

        var isIos = !!m.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端       

        if (isIos) {
            //为input、textarea、select添加blur事件
            $('input, textarea, select').on('blur', function () {
                window.scroll(0, 0);
            });
        }
    });
</script>
</body>

</html>
