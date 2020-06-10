<?php

use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
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
        <link rel="stylesheet" href="/frontend/web/cloudcar/css/ydui.css?v1" />
        <link rel="stylesheet" href="/frontend/web/cloudcar/css/common.css?v1">
        <link rel="stylesheet" href="/frontend/web/cloudcar/icons/iconfont.css?v1">
        <link rel="stylesheet" href="/frontend/web/cloudcar/css/my.css?v2">
        <link rel="stylesheet" href="/frontend/web/cloudcar/css/index.css?v2">
        <!-- 引入YDUI自适应解决方案类库 -->
        <script src="/frontend/web/cloudcar/js/ydui.flexible.js"></script>
    <?php endif; ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
<?php if (isset($this->blocks['footer'])):; ?>
    <?= $this->blocks['footer'] ?>
<?php else: ?>
    <div class="m-tabbar tabbar-fixed">
        <?php
            $hfive = Yii::$app->session['car_h5'];
            if(!$hfive){
        ?>
        <a href="<?php echo Url::to(['carhome/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'carhome') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="iconfont icon-car-shouye"></i>
            </span>
            <span class="tabbar-txt">首页</span>
        </a>
        <?php }?>
        <a href="<?php echo Url::to(['caruorder/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'caruorder') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class=" iconfont icon-car-dingdan"></i>
            </span>
            <span class="tabbar-txt">订单</span>
        </a>
        <a href="<?php echo Url::to(['caruser/index','curtimestamp'=>time()])?>" class="tabbar-item <?php if($this->context->menuActive == 'caruser') echo 'tabbar-active';?>">
            <span class="tabbar-icon">
                <i class="iconfont icon-car-mine"></i>
            </span>
            <span class="tabbar-txt">我的</span>
        </a>
    </div>
<?php endif; ?>

<script src="/frontend/web/cloudcar/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcar/js/ydui.js"></script>

<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
