<?php

use yii\helpers\Url;

?>
<div class="commom-order-header finish">
    <div class="left commom-img order-details-img">
        <img src="/frontend/web/cloudcarv2/images/oil-card-recharge.png" >
    </div>
    <div class="right">
        <?php if ($info['status'] != 2 && $info['status'] != 3): ?>
            提交成功
            <span>（预计48小时到账）</span>
        <?php else: ?>
            <?= $info['status_text'] ?>
        <?php endif; ?>
    </div>
</div>


<div class="order-details-wrapper">
    <ul class="order-details-ul">
        <li>
            <i>加油卡号: </i>
            <span><?= $info['card_no'] ?></span>
        </li>
        <li>
            <i>使用券：</i>
            <span>油卡充值服务 <i class="price">抵扣￥<?= floatval($info['amount']) ?></i></span>
        </li>
    </ul>
</div>

<div class="order-details-wrapper">
    <ul class="order-details-ul">
        <li>
            <i>订单类型：</i>
            <span>油卡充值服务</span>
        </li>
        <li>
            <i>订单编号：</i>
            <span><?= $info['orderid'] ?></span>
        </li>
        <li>
            <i>创建时间：</i>
            <span><?php echo date("Y-m-d H:i:s", $info['c_time']); ?></span>
        </li>
        <?php if ($info['s_time']): ?>
        <li>
            <i>服务完成时间：</i>
            <span><?php
                    echo date("Y-m-d H:i:s", $info['s_time']);
                ?></span>
        </li>
        <?php endif; ?>
    </ul>
</div>
<?php if ($info['s_time']): ?>
<!--<div class="commom-submit evaluate-submit">
        <a href="javascript:;" class="btn-block">评价</a>
    </div>-->
<?php endif; ?>