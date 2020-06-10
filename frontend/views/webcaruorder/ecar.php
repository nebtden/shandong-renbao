


<div class="commom-order-header <?php  if(in_array($info['status'],[401,402,403,404])): ?>cancel<?php else: ?>finish <?php  endif;?> ">
    <div class="left commom-img order-details-img">
        <img src="/frontend/web/cloudcarv2/images/super-driving.png" >
    </div>
    <div class="right"><?= $info['status_text'] ?></div>
</div>

<div class="driving-info-wrapper">
    <i class="icon-cloudCar2-siji"></i>
    <span>司机：<?= $info['drivername'] ?></span>
    <i class="icon-cloudCar2-jialing"></i>
    <span>驾龄：<?= $info['driveryear'] ?></span>
</div>
<div class="order-details-wrapper">
    <ul class="order-details-ul">
        <li>
            <i>出发地：</i>
            <span><?= $info['departure']  ?></span>
        </li>
        <li>
            <i>目的地：</i>
            <span><?= $info['destination']  ?></span>
        </li>
        <?php if($info['coupon_name']): ?>
            <li>
                <i>使用券：</i>
                <span><?= $info['coupon_name'] ?> <i class="price">抵扣公里<?= $info['coupon_amount'] ?></i></span>
            </li>
        <?php endif; ?>
        <li>
            <i>支付金额：</i>
            <span class="price">￥<?= $info['cast'] ?></span>
        </li>
    </ul>
</div>
<div class="order-details-wrapper">
    <ul class="order-details-ul">
        <li>
            <i>订单类型：</i>
            <span>代驾服务</span>
        </li>
        <li>
            <i>订单编号：</i>
            <span><?= $info['orderid'] ?></span>
        </li>
        <li>
            <i>创建时间：</i>
            <span><?= date('Y-m-d H:i:s',$info['start_time'] ) ?></span>
        </li>
        <?php  if($info['receive_time']): ?>
        <li>
            <i>接单时间：</i>
            <span><?= $info['receive_time']?date('Y-m-d H:i:s',$info['receive_time'] ):'' ?></span>
        </li>
        <?php  endif;?>
        <?php  if(!in_array($info['status'],[401,402,403,404])): ?>
        <li>
            <i>服务完成时间：</i>
            <span><?= $info['end_time']?date('Y-m-d H:i:s',$info['end_time'] ):'' ?></span>
        </li>
        <?php  endif;?>
    </ul>
</div>
<!--<div class="commom-submit evaluate-submit">
    <a href="javascript:;" class="btn-block">评价订单</a>
</div>-->
<div class="commom-tabar-height"></div>

