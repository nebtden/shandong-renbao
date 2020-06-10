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
        <li class="noactive-li"><a href="<?php echo Url::to(['carwashshop/incomedetail'])?>" class="shouyi">收益明细</a></li>
        <li class="active-li"><a href="<?php echo Url::to(['carwashshop/withdrawals'])?>" class="tixian">提现明细</a></li>
    </ul>
    <div class="detail-panel">
        <ul>
            <?php foreach($withdrawals as $val):?>
            <li>
                <div>
                    <p>提现：<i class="blue">¥
                            <?php echo number_format($val['amount'],2,".",",")?> </i>
                        （单号：<?= $val['withdrawals_no']?>）</p>
                    <p><?= date('Y-m-d H:i:s',$val['apply_time'])?></p>
                </div>
                <?php if($val['status'] == 1):?>
                <span>处理中</span>
                <?php elseif ($val['status'] == 2):?>
                <span class="tx-success">处理成功</span>
                <?php elseif ($val['status'] == 3):?>
                <span>已拒绝</span>
                <?php endif?>
            </li>
           <?php endforeach;?>
        </ul>
    </div>
</div>
<div class="commom-tabar-height"></div>


