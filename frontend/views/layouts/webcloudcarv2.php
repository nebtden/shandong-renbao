<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <title><?php echo $this->context->site_title; ?></title>
    <link rel="stylesheet" href="/frontend/web/webcloudcarv2/css/ydui.css?v2" />
    <link rel="stylesheet" href="/frontend/web/webcloudcarv2/css/common.css?v2">
    <link rel="stylesheet" href="/frontend/web/webcloudcarv2/icons/iconfont.css?v2">
    <link rel="stylesheet" href="/frontend/web/webcloudcarv2/css/swiper-3.0.4.min.css?v2">
    <link rel="stylesheet" href="/frontend/web/webcloudcarv2/css/all.css?v2">
    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="/frontend/web/webcloudcarv2/js/ydui.flexible.js"></script>
</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
<?php if (isset($this->blocks['footer'])):; ?>
    <?= $this->blocks['footer'] ?>
<?php else: ?>
<div class="commom-tabar-height"></div>
<div class="m-tabbar tabbar-fixed">
    <?php
    $hfive = Yii::$app->session['car_h5'];
    if(!$hfive){
    ?>
    <a href="<?php echo Url::to(['webcarhome/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'carhome') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-shouye"></i>

            </span>
        <span class="tabbar-txt">首页</span>
    </a>
    <?php }?>
    <a href="<?php echo Url::to(['webcaruser/accoupon','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'accoupon') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-duihuan"></i>
            </span>
        <span class="tabbar-txt">兑换</span>
    </a>
    <a href="<?php echo Url::to(['webcaruorder/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'caruorder') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-dingdan"></i>
            </span>
        <span class="tabbar-txt">订单</span>
    </a>
    <a href="<?php echo Url::to(['webcaruser/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'caruser') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-wode"></i>
            </span>
        <span class="tabbar-txt">我的</span>
    </a>
</div>
<?php endif; ?>
<script src="/frontend/web/webcloudcarv2/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/webcloudcar/js/ydui.js"></script>
<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
