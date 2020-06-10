<?php

use frontend\controllers\SegwayController;
use common\models\CarSegorder;
use yii\helpers\Url;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <title>预约成功</title>
    <link rel="stylesheet" href="<?= SegwayController::STATIC_PATH ?>/css/ydui.css"/>
    <link rel="stylesheet" href="<?= SegwayController::STATIC_PATH ?>/css/common.css">
    <link rel="stylesheet" href="<?= SegwayController::STATIC_PATH ?>/icons/iconfont.css">
    <link rel="stylesheet" href="<?= SegwayController::STATIC_PATH ?>/css/swiper-3.0.4.min.css">
    <link rel="stylesheet" href="<?= SegwayController::STATIC_PATH ?>/css/all.css">
    <link rel="stylesheet" href="<?= SegwayController::STATIC_PATH ?>/css/ciao.css"/>
    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="<?= SegwayController::STATIC_PATH ?>/js/ydui.flexible.js"></script>
</head>
<body>
<div class="yuyue-container">
    <div class="yuyue-top">
        <div class="yuyue-top-left">
            <p>预约成功</p>
            <p>您的预约已完成<br/>稍后客服会致电您进行电话确认<br/>请注意保持电话畅通</p>
        </div>
        <div class="yuyue-top-right">
            <img src="<?= SegwayController::STATIC_PATH ?>/img/yuyue-banner.png">
        </div>
    </div>
    <div class="yuyue-info-box">
        <div class="order-details-wrapper">
            <ul class="order-details-ul yuyue-details-ul">
                <li>
                    <i>订单编号：</i>
                    <span><?= $info['orderid'] ?></span>
                </li>
                <li>
                    <i>联系人：</i>
                    <span><?= $info['liaison'] ?></span>
                </li>
                <li>
                    <i>联系电话：</i>
                    <span><?= $info['telphone'] ?></span>
                </li>
                <li>
                    <i>预约城市：</i>
                    <span><?= $info['precity'] ?></span>
                </li>
                <li>
                    <i>预约网点：</i>
                    <span><?= $info['prestore'] ?></span>
                </li>
                <li>
                    <i>预约用车时间：</i>
                    <span><?= date('Y-m-d H:i:s', $info['pre_u_time']) ?></span>
                </li>
                <li>
                    <i>预计还车时间：</i>
                    <span><?= date('Y-m-d H:i:s', $info['pre_r_time']) ?></span>
                </li>
            </ul>
        </div>
        <p class="hot-line">如有疑问请拨打 <?= SegwayController::TELPHONE ?></p>
        <div class="commom-submit">
            <button class="btn-block btn-primary">完成</button>
        </div>
    </div>
</div>
<script src="<?= SegwayController::STATIC_PATH ?>/js/jquery-2.1.4.js"></script>
<script src="<?= SegwayController::STATIC_PATH ?>/js/ydui.js"></script>
<script src="<?= SegwayController::STATIC_PATH ?>/js/commom.js"></script>
<script>
    $('.commom-submit').on('click', function () {
        location.href = '<?= Url::to(["segway/orderdetail", "mid" => $info['m_id']])?>';
    });
</script>
</body>
</html>