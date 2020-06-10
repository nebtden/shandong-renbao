<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head >
    <meta charset="UTF-8">
    <meta name="wap-font-scale" content="no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telphone=no, email=no"/>
    <title>领取卡券</title>
    <link rel="stylesheet" href="/frontend/web/drivingH5/css/ydui.css" />
    <link rel="stylesheet" href="/frontend/web/drivingH5/css/public.css">

    <link rel="stylesheet" href="/frontend/web/drivingH5/css/ciao.css">

    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="/frontend/web/cloudcarv2/js/ydui.flexible.js"></script>
</head>
<style>
    .commom-submit>.btn-block {
        background-color: #e8350d;
        height: .8rem;
        line-height: .8rem;
        font-size: .3rem;
        margin-top: .6rem;
        border-radius:0;
    }
    .commom-submit {
        width: 100%;
        padding: 0;
        margin-left: auto;
        margin-right: auto;
    }
    .tips-wrap {
        margin-top: .55rem;
        text-align: left;
        font-size: .25rem;
        line-height: .4rem;
        color: #666;
    }

    .red {
        color: #e8350d;
        font-size:.3rem
    }
</style>

<body>
<div class="coupon-page">
    <div class="input-list">
        <i>兑换码</i>
        <input type="text" name="code" placeholder="张三2019123456（商业险保单号后六位）">
    </div>
    <div class="commom-submit">
        <a class="btn-block btn-primary" href="javascript:;">领取卡券</a>
    </div>
    <div class="tips-wrap">
        <p class="red">兑换码说明:</p>
        <p>您的专属增值服务兑换码由以下元素组成
        <span class="bold">(被保险人姓名+投保年份+保单号后六位）</span>
        ，请您确保兑换码中间没有空格和任意符号。</p>
    </div>
    <div class="logo-wrap">
        <img src="/frontend/web/cloudcarv2/img/car-logo.png">
        <p>本卡使用服务解释权归提供服务的云车驾到平台所有</p>
        <p>24小时客服热线：400-617-1981</p>
    </div>
</div>
<script src="/frontend/web/cloudcarv2/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>
<script>
    var isSubmit = false;
    //验证兑换码格式，姓名+2019+保单号后六位
    var testCode = function(m){
        var reg = /^[\u4e00-\u9fa5]{2,5}2019\d{6}$/;
        return reg.test(m);
    };
    $(document).on('click','.commom-submit>a',function(){
        //防止重复提交
        if(isSubmit){
            return false;
        }
        var code = $('input[name=code]').val();
        if(!testCode(code)){
            YDUI.dialog.toast('兑换码格式不正确','none',1500);
            return false;
        }
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post(
            '<?php echo Url::to(['drivingh5/accoupon'])?>',
            {code:code},
            function(json){
                isSubmit = false;
                YDUI.dialog.loading.close();
                if(json.status == 1){
                    YDUI.dialog.toast('兑换成功','none',1500,function(){
                        window.location.href = '<?php echo Url::to(['drivingh5/rbdriving'])?>';
                    });
                }else {
                    YDUI.dialog.alert(json.msg,'none',1500);
                }
            }
        ,'json')
    });

</script>
</body>
</html>
