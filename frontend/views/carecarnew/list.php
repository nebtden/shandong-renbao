<?php

use yii\helpers\Url;
use common\models\Car_paternalor;
?>
<div class="m-tab" data-ydui-tab>
    <div class="tab-panel">
        <div class="tab-panel-item tab-active">
            <?php if (!empty($list)) { ?>
                <ul class="order-list-ul">
                    <!--                    1道路救援，2代驾，3在线洗车券购买,5油卡订单 6年检  -->
                    <?php foreach ($list as $val): ?>
                        <li onclick="location.href='<?= Url::to(["caruorder/ecar","id"=>$val["m_id"]]); ?>'" >
                            <div class="left commom-img">
                                <img src="/frontend/web/cloudcarv2/images/super-driving.png" >
                            </div>
                            <div class="middle">
                                <i>代驾服务</i>
                                <span><?php echo date("Y.m.d H:i:s", $val['c_time']); ?></span>
                                <span>使用：<?php  echo  $val['coupon_name']; ?></span>

                                <span>起止：<?= $val['departure'].'-'.$val['destination'] ?></span>


                            </div>
                            <div class="right"><?php echo $val['status_text']; ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php } else { ?>
                <span style="display:block;color: #7c7c7c;text-align: center;margin-top:.4rem; ">亲暂无订单信息！</span>
            <?php } ?>

        </div>

    </div>
</div>
<div class="commom-tabar-height"></div>
