<?php

use yii\helpers\Url;
use common\models\Car_paternalor;

?>
<div class="m-tab" data-ydui-tab>
    <ul class="tab-nav">
        <li class="tab-nav-item tab-active">
            <a href="javascript:;">全部</a>
        </li>
        <li class="tab-nav-item">
            <a href="javascript:;">待接单</a></li>
        <li class="tab-nav-item">
            <a href="javascript:;">待服务</a></li>
        <li class="tab-nav-item">
            <a href="javascript:;">待评价</a></li>
    </ul>
    <div class="tab-panel">
        <div class="tab-panel-item tab-active">
            <?php if (!empty($list)) { ?>
                <ul class="order-list-ul">
                    <!--                    1道路救援，2代驾，3在线洗车券购买,5油卡订单 6年检  -->
                    <?php foreach ($list as $val): ?>
                        <li>
                            <div class="left commom-img">
                                <img src="<?php  echo  Car_paternalor::$cloudv2img[$val['order_type']]?>" >
                            </div>
                            <?php if ($val['page_url']):?>
                            <div class="middle"  onclick="location.href='<?= $val['page_url'] ?>'">
                                <?php else: ?>
                                <div class="middle">
                                    <?php endif; ?>
                                    <i><?php  echo  Car_paternalor::$type[$val['order_type']]?></i>
                                    <span><?php echo date("Y.m.d H:i:s", $val['c_time']); ?></span>
                                    <span>使用：<?php  echo  $val['coupon_name']; ?></span>

                                    <?php if($val['order_type']==1): ?>
                                        <span>位置：<?= $val['faultaddress'] ?></span>
                                    <?php elseif($val['order_type']==2): ?>
                                        <span>起止：<?= $val['departure'].'-'.$val['destination'] ?></span>
                                    <?php elseif ($val['order_type']==4): ?>
                                        <span>次数：<?= floatval($val['coupon_amount']) ?></span>
                                    <?php else: ?>
                                        <?php if($val['order_type']!=INSPECTION): ?>
                                            <span>金额：<?= floatval($val['coupon_amount']) ?></span>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="right"><?php echo $val['status_text']; ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php } else { ?>
                <span style="display:block;color: #7c7c7c;text-align: center;margin-top:.4rem; ">亲暂无订单信息！</span>
            <?php } ?>

        </div>
        <div class="tab-panel-item">
            <ul class="order-list-ul">
                <?php if(isset($new_list[1])): ?>
                    <?php foreach ($new_list[1] as $val): ?>
                        <li>
                            <div class="left commom-img">
                                <img src="<?php  echo  Car_paternalor::$cloudv2img[$val['order_type']]?>" >
                            </div>
                            <?php if ($val['page_url']):?>
                            <div class="middle"  onclick="location.href='<?= $val['page_url'] ?>'">
                                <?php else: ?>
                                <div class="middle">
                                    <?php endif; ?>
                                    <i><?php  echo  Car_paternalor::$type[$val['order_type']]?></i>
                                    <span><?php echo date("Y.m.d H:i:s", $val['c_time']); ?></span>
                                    <span>使用：<?php  echo  $val['coupon_name']; ?></span>

                                    <?php if($val['order_type']==1): ?>
                                        <span>位置：<?= $val['faultaddress'] ?></span>
                                    <?php elseif($val['order_type']==2): ?>
                                        <span>起止：<?= $val['departure'].'-'.$val['destination'] ?></span>
                                    <?php elseif ($val['order_type']==4): ?>
                                        <span>次数：<?= floatval($val['coupon_amount']) ?></span>
                                    <?php else: ?>
                                        <?php if($val['order_type']!=INSPECTION): ?>
                                            <span>金额：<?= floatval($val['coupon_amount']) ?></span>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="right"><?php echo $val['status_text']; ?></div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-panel-item">
            <ul class="order-list-ul">
                <?php if(isset($new_list[2])): ?>
                    <?php foreach ($new_list[2] as $val): ?>
                        <li>
                            <div class="left commom-img">
                                <img src="<?php  echo  Car_paternalor::$cloudv2img[$val['order_type']]?>" >
                            </div>
                            <div class="middle" onclick="location.href='<?= $val['page_url'] ?>'">
                                <i><?php  echo  Car_paternalor::$type[$val['order_type']]?></i>
                                <span><?php echo date("Y.m.d H:i:s", $val['c_time']); ?></span>
                                <span>使用：<?php  echo  $val['coupon_name']; ?></span>
                                <?php if ($val['order_type']==4): ?>
                                    <span>次数：<?= floatval($val['coupon_amount']) ?></span>
                                <?php elseif($val['order_type']!=INSPECTION): ?>
                                    <span>金额：<?= floatval($val['coupon_amount']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="right"><?php echo $val['status_text']; ?></div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-panel-item">
            <ul class="order-list-ul">
                <?php if(isset($new_list[3])): ?>
                    <?php foreach ($new_list[3] as $val): ?>
                        <li>
                            <div class="left commom-img">
                                <img src="<?php  echo  Car_paternalor::$cloudv2img[$val['order_type']]?>" >
                            </div>
                            <div class="middle" onclick="location.href='<?= $val['page_url'] ?>'">
                                <i><?php  echo  Car_paternalor::$type[$val['order_type']]?></i>
                                <span><?php echo date("Y.m.d H:i:s", $val['c_time']); ?></span>
                                <span>使用：<?php  echo  $val['coupon_name']; ?></span>
                                <?php if($val['order_type']==1): ?>
                                    <span>位置：<?= $val['faultaddress'] ?></span>
                                <?php elseif($val['order_type']==2): ?>
                                    <span>起止：<?= $val['departure'].'-'.$val['destination'] ?></span>
                                <?php elseif ($val['order_type']==4): ?>
                                    <span>次数：<?= floatval($val['coupon_amount']) ?></span>
                                <?php else: ?>
                                    <?php if($val['order_type']!=INSPECTION): ?>
                                        <span>金额：<?= floatval($val['coupon_amount']) ?></span>

                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="right"><?php echo $val['status_text']; ?></div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<div class="commom-tabar-height"></div>
<?php if($footer == 'hidden'){?>
    <?php $this->beginBlock('footer'); ?>
    <?php $this->endBlock('footer'); ?>
<?php }?>