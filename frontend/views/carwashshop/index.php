<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">

<?php $this->endBlock('hStyle')?>

<?php if(empty($washShop) || $washShop['shop_status'] == 3): ?>
    <div class="commom-img">
        <img src="/frontend/web/cloudcarv2/images/apply-no.jpg" >
    </div>
    <div class="hot-line">
        <a href="tel:400-617-1981">
            <i class="icon-cloudCar2-kefurexian"></i>
            <span>客服热线</span>
            <time><?php echo \Yii::$app->params['yunche_hotline'] ?></time>
        </a>
    </div>
    <div class="commom-submit">
        <a href="<?php echo Url::to(['carwashshop/apply'])?>" class="btn-block">申请商户认证</a>
    </div>

<?php elseif($washShop['shop_status'] == 1): ?>
    <div class="commom-img">
        <img src="/frontend/web/cloudcarv2/images/apply-ing.jpg" >
    </div>
    <div class="hot-line">
        <a href="tel:400-617-1981">
            <i class="icon-cloudCar2-kefurexian"></i>
            <span>客服热线</span>
            <time><?php echo \Yii::$app->params['yunche_hotline'] ?></time>
        </a>
    </div>
    <div class="commom-submit">
        <a href="<?php echo Url::to(['carwashshop/applyrecord']);?>" class="btn-block">查看送审记录</a>
    </div>
<?php else:?>

<div class="store-header">
    <div class="store-logo">
        <img src="<?php echo $washShop['shop_pic']?>">
    </div>
    <div class="store-info">
        <p class="store-name"><?php echo $washShop['shop_name']?><img src="/frontend/web/cloudcarv2/images/v.png"></p>
        <p class="store-site"><img src="/frontend/web/cloudcarv2/images/site.png"><?php echo $washShop['shop_address']?></p>
        <p class="store-line"><img src="/frontend/web/cloudcarv2/images/phone.png"><?php echo $washShop['shop_tel']?></p>
    </div>
    <a href="<?php echo Url::to(['carwashshop/shopdetail']);?>"><i class="icon-cloudCar2-jiantou"></i></a>
</div>
<ul class="data-wrap">
    <li>
        <p>已服务数量</p>
        <span><?php echo $washShop['service_num']?></span>
    </li>
    <li>
        <p>收入总额（元）</p>
        <span><?php echo number_format($washShop['gross_income'],2,".",",")?></span>
    </li>
    <li>
        <p>可提现额（元）</p>
        <span><?php echo number_format($washShop['amount'],2,",",".")?></span>
    </li>
</ul>
<ul class="common-group">
    <li>
        <a href="<?php echo Url::to(['carwashshop/withdraw'])?>">
            <i class="icon-common icon-tixian"></i>
            <span>申请提现</span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </li>
    <li>
        <a href="<?php echo Url::to(['carwashshop/withdrawals'])?>">
            <i class="icon-common icon-txjl"></i>
            <span>提现记录</span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </li>
    <li>
        <a href="<?php echo Url::to(['carwashshop/incomedetail'])?>">
            <i class="icon-common icon-shouru"></i>
            <span>收入明细</span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </li>
</ul>
<ul class="common-group">
    <li>
        <a href="<?php echo Url::to(['carwashshop/childaccount'])?>">
            <i class="icon-common icon-zzh"></i>
            <span>子账号管理</span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </li>
    <li>
        <a href="tel:400-617-1981">
            <i class="icon-cloudCar2-kefurexian"></i>
            <span>客服热线</span>
            <time>400-617-1981</time>
        </a>
    </li>
</ul>
<div class="commom-submit">
    <a href="<?php echo Url::to(['carwashshop/verification'])?>" class="btn-block">核销</a>
</div>
<div class="commom-tabar-height"></div>
<?php endif;?>