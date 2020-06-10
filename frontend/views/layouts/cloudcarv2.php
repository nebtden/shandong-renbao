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
    <?php if (isset($this->blocks['headStyle'])): ?>
        <?= $this->blocks['headStyle'] ?>
    <?php else:?>
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/ydui.css" />
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/common.css">
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/icons/iconfont.css">
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/swiper-3.0.4.min.css">
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/all.css">
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/ciao.css">
    <?php endif; ?>
    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="/frontend/web/cloudcarv2/js/ydui.flexible.js"></script>

    <?php if(isset($this->blocks['hStyle'])):?>
        <?= $this->blocks['hStyle'] ?>
    <?php endif;?>

    <?php if(isset($this->blocks['hScript'])):?>
        <?= $this->blocks['hScript'] ?>
    <?php endif;?>

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
        //如果是web端用户，底部菜单栏变更为web端链接
        $webUrl = (isset($this->context->webUrl))?$this->context->webUrl:null;
        if(!$hfive){
            ?>
        <a href="<?php echo Url::to([''.$webUrl.'carhome/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'carhome') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-shouye"></i>

            </span>
            <span class="tabbar-txt">首页</span>
        </a>
        <?php }?>
        <a href="<?php echo Url::to([''.$webUrl.'caruser/accoupon','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'accoupon') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-duihuan"></i>
            </span>
            <span class="tabbar-txt">兑换</span>
        </a>
        <a href="<?php echo Url::to([''.$webUrl.'caruorder/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'caruorder') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-dingdan"></i>
            </span>
            <span class="tabbar-txt">订单</span>
        </a>
        <a href="<?php echo Url::to([''.$webUrl.'caruser/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'caruser') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="icon-cloudCar2-wode"></i>
            </span>
            <span class="tabbar-txt">我的</span>
        </a>
    </div>
<?php endif; ?>
<script src="/frontend/web/cloudcarv2/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>

<script src="/frontend/web/cloudcarv2/js/commom.js?ver=1102"></script>

<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
