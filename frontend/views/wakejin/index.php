<?php

use yii\helpers\Url;

?>


<div class="input-oilcard-info" style="display: block">
    <ul class="oilcard-info-ul">
        <li class="shiyou zhongshiyou">
            <i class="icon-cloudCar2-querenqiahao"></i>
            <em>爱奇艺账号</em>
            <input type="text" name="account" placeholder="请输入爱奇艺账号" value="">
        </li>

        <li class="shiyou zhongshiyou">
            <i class="icon-cloudCar2-querenqiahao"></i>
            <em>请确认账号</em>
            <input type="text" name="account_repeat" placeholder="再次输入爱奇艺账号!" value="">
        </li>

    </ul>
    <div class="recharge-tip zhongshiyou">
        注：提交成功后，一般半分钟内刷新页面即可充值成功，如果仍没成功，请联系客服
    </div>

    <input type="hidden" name="coupon_id" id="coupon_id" value="<?= $id   ?>">
    <div class="commom-submit comfirm-recharge-submit" style="bottom: 1.7rem">
        <a href="javascript:;" class="btn-block">确定</a>
    </div>
</div>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script'); ?>

<script>

    //确认充值
    var isSubmit = false;
    $('.comfirm-recharge-submit').on('click', function () {
        if (isSubmit) return false;
        var   account = $("input[name=account]").val();
        var   account_repeat = $("input[name=account_repeat]").val();
        console.log(account);
        console.log(account_repeat);
        console.log(2222222);
        if (account != account_repeat) {
            YDUI.dialog.toast('两次账号不一致', 1000);
            return false;
        }

        isSubmit = true;
        var coupon_id = $("#coupon_id").val();
        var data = {
            account: account,
            coupon_id: coupon_id,
        };

        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['submit'])?>", data, function (json) {
            isSubmit = false;
            YDUI.dialog.loading.close();
            if (json.status === 1) {
                YDUI.dialog.toast('提交成功，请过段时间刷新', 'success', 1000, function () {
                    window.location.href = json.url;
                });
            } else {
                YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
    });

</script>
<?php $this->endBlock('script'); ?>
