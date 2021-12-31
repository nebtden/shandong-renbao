<?php

use yii\helpers\Url;

?>
    <div class="commom-order-header finish">
        <div class="left commom-img order-details-img">
            <img src="/frontend/web/cloudcarv2/images/oil-card-recharge.png" >
        </div>
        <div class="right">
            <?php if ($info['status'] ==2): ?>
                提交成功
                <span></span>
            <?php else: ?>
            <p>等待系统确认!</p>
            <br>
            <span style="font-size: 0.25rem">注：提交成功后，一般半分钟内刷新此页面即可充值成功，如果仍没成功，请联系客服</span>
            <?php endif; ?>
        </div>
    </div>


    <div class="order-details-wrapper">
        <ul class="order-details-ul">
            <li>
                <i>充值账号: </i>
                <span><?= $info['account'] ?></span>
            </li>

        </ul>
    </div>

    <div class="order-details-wrapper">
        <ul class="order-details-ul">

            <li>
                <i>订单编号：</i>
                <span><?= $info['order_no'] ?></span>
            </li>
            <li>
                <i>创建时间：</i>
                <span><?php echo date("Y-m-d H:i:s", $info['c_time']); ?></span>
            </li>
            <?php if ($info['s_time']): ?>
                <li>
                    <i>充值完成时间：</i>
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