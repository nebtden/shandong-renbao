<?php

use yii\helpers\Url;
?>
<?php $this->beginBlock('hStyle')?>
<link rel="stylesheet" href="/frontend/web/cloudcarv2/css/carwashshop.css">

<?php $this->endBlock('hStyle')?>

<div class="commom-img">
    <img src="<?php echo $washShop['shop_pic']?>" >
</div>
<dl class="common-dl">
    <dt><span>门店基础信息</span></dt>
    <dd class="common-select">
        <i>门店照片</i>
        <span class="file-span"><a href="<?php echo Url::to(['carwashshop/changepic'])?>">修改</a></span>
        <em class="icon-cloudCar2-jiantou"></em>
    </dd>
    <dd>
        <ul>
            <li>
                <i>门店名称</i>
                <span><?php echo $washShop['shop_name']?></span>
            </li>
            <li>
                <i>统一社会信用代码</i>
                <span><?php echo $washShop['shop_credit_code']?></span>
            </li>
            <li>
                <i>注册时间</i>
                <span><?php echo date('Y年m月d日',$washShop['shop_register_time'])?></span>
            </li>
        </ul>
    </dd>
    <dd>
        <a href="<?php echo Url::to(['carwashshop/changeaddr'])?>" class="store-select">
            <i>门店地址</i>
            <span><?php echo $washShop['district']?><?php echo $washShop['shop_address']?></span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </dd>
    <dd>
        <a href="<?php echo Url::to(['carwashshop/changeaddr'])?>" class="store-select">
            <i>导航定位</i>
            <span><?php echo number_format($washShop['lng'],4).','.number_format($washShop['lat'],4)?></span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </dd>
    <dd>
        <a href="<?php echo Url::to(['carwashshop/changetel'])?>" class="store-select">
            <i>联系电话</i>
            <span><?php echo $washShop['shop_tel']?></span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </dd>
    <dd>
        <a href="<?php echo Url::to(['carwashshop/changeservicetime'])?>" class="store-select">
            <i>营业时间</i>
            <span><?php echo $washShop['start_time']?>-<?php echo $washShop['end_time']?></span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </dd>
    <dd>
        <a href="<?php echo Url::to(['carwashshop/changemobile'])?>" class="store-select">
            <i>提现手机号</i>
            <span><?php echo $washShop['mobile']?></span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </dd>
</dl>
<dl class="common-dl">
    <dt>
        <span>银行账户信息</span>
        <a href="<?php echo Url::to(['carwashshop/changeaccount','type'=>2])?>" class="button">修改</a>
    </dt>
    <?php if(isset($bank)):?>
        <dd>
            <ul>
                <li>
                    <i>账户名称</i>
                    <span><?php echo $bank['payee_name']?></span>
                </li>
                <li>
                    <i>账号</i>
                    <span><?php echo $bank['payee_account']?></span>
                </li>
                <li>
                    <i>开户行</i>
                    <span><?php echo $bank['payee_bank']?></span>
                </li>
            </ul>
        </dd>
    <?php endif;?>
</dl>
<dl class="common-dl">
    <dt>
        <span>支付宝账户信息</span>

        <a href="<?php echo Url::to(['carwashshop/changeaccount','type'=>1])?>" class="button">修改</a>
    </dt>
    <?php if(isset($aipay)):?>
    <dd>
        <ul>
            <li>
                <i>支付宝账号名</i>
                <span><?php echo $aipay['payee_account']?></span>
            </li>
            <li>
                <i>真实姓名</i>
                <span><?php echo $aipay['payee_name']?></span>
            </li>
        </ul>
    </dd>
    <?php endif;?>
</dl>
<dl class="common-dl">
    <dt><span>账户安全</span></dt>
    <dd class="common-select">
        <a href="<?php echo Url::to(['carwashshop/changewithdraw'])?>">
            <i>提现密码</i>
            <span>设置/修改提现密码</span>
            <em class="icon-cloudCar2-jiantou"></em>
        </a>
    </dd>
</dl>
<div class="commom-tabar-height"></div>
