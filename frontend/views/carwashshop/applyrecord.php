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
    <dd>
        <ul>
            <li>
                <i>门店名称</i>
                <span><?php echo $washShop['shop_name']?></span>
            </li>
            <li>
                <i>门店地址</i>
                <span><?php echo $washShop['shop_address']?></span>
            </li>
            <li>
                <i>统一社会信用代码</i>
                <span><?php echo $washShop['shop_credit_code']?></span>
            </li>
            <li>
                <i>注册时间</i>
                <span><?php echo date('Y年m月d日',$washShop['shop_register_time'])?></span>
            </li>
            <li>
                <i>联系电话</i>
                <span><?php echo $washShop['shop_tel']?></span>
            </li>
            <li>
                <i>营业时间</i>
                <span><?php echo $washShop['start_time']?>-<?php echo $washShop['end_time']?></span>
            </li>
            <li>
                <i>提现手机号</i>
                <span><?php echo $washShop['mobile']?></span>
            </li>
        </ul>
    </dd>
</dl>
<?php foreach ($account as $val):?>
<dl class="common-dl">
    <dt>
        <span>收款账户信息</span>
    </dt>
    <dd>
        <ul>
            <?php if($val['type'] == 2):?>
            <li>
                <i>账户名称</i>
                <span><?php echo $val['payee_name']?></span>
            </li>
            <li>
                <i>账号</i>
                <span><?php echo $val['payee_account']?></span>
            </li>
            <li>
                <i>开户行</i>
                <span><?php echo $val['payee_bank']?></span>
            </li>
            <?php elseif ($val['type'] ==1):?>
            <li>
                <i>支付宝账号名</i>
                <span><?php echo $val['payee_account']?></span>
            </li>
            <li>
                <i>真实姓名</i>
                <span><?php echo $val['payee_name']?></span>
            </li>
            <?php endif?>
        </ul>
    </dd>
</dl>
<?php endforeach;?>
<div class="commom-tabar-height"></div>
