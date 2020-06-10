<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">
<style>
    .cancel-btn {
        background: #cbcbcb;
    }

</style>
<?php $this->endBlock('hStyle')?>
<?=$this->render('_detail-num', ['washShop' => $washShop])?>
<div class="detail-wrap">
    <ul class="detail-nav">
        <li class="active-li"><a href="<?php echo Url::to(['carwashshop/incomedetail'])?>" class="shouyi">收益明细</a></li>
        <li class="noactive-li"><a href="<?php echo Url::to(['carwashshop/withdrawals'])?>" class="tixian">提现明细</a></li>
    </ul>
<div class="detail-panel">
    <ul>
        <?php foreach($incomedetail as $val):?>
        <li>
            <div>
                <p>订单:<?= $val['mainOrderSn']?>（服务码：<?= $val['consumerCode']?>）</p>
                <p><?php echo date('Y-m-d H:i:s', $val['s_time'])?></p>
            </div>
            <span>+<?= $val['promotion_price']?></span>
        </li>
        <?php endforeach;?>
    </ul>
</div>
</div>
<div class="commom-tabar-height"></div>

