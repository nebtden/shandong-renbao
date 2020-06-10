<?php
use yii\helpers\Url;
?>
<div class="detail-num">
    <p>
        <span>已服务数量</span>
        <i><?= $washShop['service_num']?></i>
    </p>
    <p>
        <span>收入总额（元）</span>
        <i><?php echo number_format($washShop['gross_income'],2,".",",")?></i>
    </p>
    <p>
        <span>可提现额（元）</span>
        <i class="blue"><?php echo number_format($washShop['amount'],2,".",",")?></i>
        <?php if($washShop['amount']<50):?>
            <a class="cash-btn cancel-btn" href="#">申请提现</a>
        <?php else:?>
            <a class="cash-btn" href="<?php echo Url::to(['carwashshop/withdraw'])?>" disabled>申请提现</a>
        <?php endif;?>

    </p>
</div>

