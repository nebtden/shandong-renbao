<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 上午 9:53
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd">
    <header class="CenterHeader">
        <img src="/frontend/web/images/shanghuzhongxinbg.jpg">
        <dl class="CenterTopDL boxSizing webkitbox">
            <dt>
                <?php if(!empty($shopinfo['shop_pic'])){?>
                    <img src="<?php echo $shopinfo['shop_pic'];?>">
                <?php }else if(!empty($user['headimgurl'])){?>
                    <img src="<?php echo $user['headimgurl'];?>">
                <?php }else {?>
                    <img src="/frontend/web/images/qiche.jpg">
                <?php }?>
            </dt>
            <dd>

                <p class="p1 webkitbox">
                    <span><?php echo $shopinfo['shop_name'];?></span>
                    <?php if($shopinfo['shop_status']==2){?>
                        <em>已认证</em>
                    <?php }?>
                </p>
                <p class="p2">地址：<?php echo $shopinfo['shop_address'];?></p>
                <p class="p3">电话：<?php echo $shopinfo['mobile'];?></p>
            </dd>
            <div class="edit-wrapper" onclick="window.location.href='<?php echo Url::to(['car/authentication']);?>';"><span><img src="/frontend/web/images/edit.png" >编辑</span></div>
        </dl>
    </header>
    <ul class="aboutMoney webkitbox boxSizing">
        <li class="boxSizing">
            总收入：<img src="/frontend/web/images/income.png" ><i><?php echo $shopinfo['gross_income'];?></i>
        </li>
        <li class="boxSizing">
            已提现：<img src="/frontend/web/images/income.png" ><i><?php echo $shopinfo['already_amount'];?></i>
        </li>
    </ul>
    <div class="shopPageCont NoPaddLR NoPaddBot NoMarginTop">
        <ul class="memFuncList BotUlImg">
            <li>
                <a href="<?php echo Url::to(['car/income_details']);?>">
                    <div class="webkitbox boxSizing">
                        <img src="/frontend/web/images/shouzhimingxi.png">
                        <span>收支明细</span>
                    </div>
                </a>
            </li>

            <li>
                <a href="<?php echo Url::to(['car/apply_withdrawal']);?>">
                    <div class="webkitbox boxSizing">
                        <img src="/frontend/web/images/shenqingtixian.png">
                        <span>申请提现</span>
                    </div>
                </a>
            </li>

        </ul>
    </div>
    <div class="shopPageCont NoPaddLR NoPaddBot">
        <ul class="memFuncList BotUlImg">
            <li>
                <a href="<?php echo Url::to(['car/details']);?>">
                    <div class="webkitbox boxSizing">
                        <img src="/frontend/web/images/shanghurenzheng.png">
                        <span>商户认证</span>
                    </div>
                </a>
            </li>
            <?php if (empty($_SESSION['wx_user_auth']['pid'])){?>
            <li>
                <a href="<?php echo Url::to(['car/admin_account']);?>">
                    <div class="webkitbox boxSizing">
                        <img src="/frontend/web/images/zhanghuguanli.png">
                        <span>账户管理</span>
                    </div>
                </a>
            </li>
            <?php }?>
            <li>
                <a href="tel:400-176-0899">
                    <div class="webkitbox boxSizing noyjt">
                        <img src="/frontend/web/images/kefuguanli.png">
                        <span>客服电话：<em>400-176-0899</em></span>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</section>
