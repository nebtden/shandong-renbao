<?php

use yii\helpers\Url;

?>
<div class="youka-recharge-list">
    <ul class="youka-recharge-ul">
        <?php if ($info['oil_card_type'] == 1): ?>
            <li>
                <div class="left">
                    <span><img src="/frontend/web/cloudcar/images/zhongshiyou-color.png"></span>
                </div>
                <div class="right">
                    <div class="up">
                        <div class="shiyou-company">
                            <span>中国石油</span>
                            <i>卡号</i>
                        </div>
                    </div>
                    <div class="down"><?= $info['oil_card_no'] ?></div>
                </div>
            </li>
        <?php else: ?>
            <li>
                <div class="left">
                    <span><img src="/frontend/web/cloudcar/images/zhongshihua-color.png"></span>
                </div>
                <div class="right">
                    <div class="up">
                        <div class="shiyou-company">
                            <span>中国石化</span>
                            <i>卡号</i>
                        </div>
                        <a class="a-change" href="<?php echo Url::to(['bind', 'id' => $info['id']]) ?>"
                           data-name="zhongshihua">更&nbsp;改</a>
                    </div>
                    <div class="down"><?= $info['oil_card_no'] ?></div>
                </div>
            </li>
        <?php endif; ?>
    </ul>
</div>
<div class="youka-voucher-wrapper">
    <div class="up">
        <i class="icon-car-gou3"></i>
        <span>油卡充值券</span>
        <a class="a-youka-service" href="javascript:;">油卡充值服务 <i class="icon-car-jiantou"></i></a>
    </div>
    <span class="down">抵扣 ¥<?= floatval($coupon['amount']) ?></span>
</div>
<div class="memo-wrapper">
    <span>尊敬的用户，油卡充值服务每天最多使用5张充值券，最高充值金额以及加油卡最高备用金额，以加油卡发行方公示为准。  </span>
</div>
<div class="commom-bind-card recharge-wrapper">
    <button type="button" class="btn-block btn-primary">确认充值</button>
</div>

<?php $this->beginBlock('script'); ?>
<script>
    var isSubmit = false;
    $('.recharge-wrapper').on('click', function (e) {
        if (isSubmit) return false;
        var coupon_id = <?=$coupon['id']?>;
        var url = '<?php echo Url::to(["playorder"])?>';
        YDUI.dialog.loading.open('正在提交');
        $.post(url, {cid: coupon_id}, function (json) {
            isSubmit = false;
            YDUI.dialog.loading.close();
            if (json.status == 1) {
                //跳到订单页
                window.location.href = '<?php echo Url::to(["caruorder/oilinfo"]);?>'+'?id='+json.data.id;
            } else {
                YDUI.dialog.toast(json.msg, 'none', 1500);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>
