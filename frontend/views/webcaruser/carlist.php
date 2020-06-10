<?php

use yii\helpers\Url;

?>
    <div class="my-garage-list">
        <ul class="my-garage-ul">
            <?php foreach ($list as $car): ?>
                <li>
                    <div class="left ">
                    <span class="commom-img">
                        <img src="/frontend/web/cloudcarv2/images/car.png" >
                        <!--                        <img src="--><?//= $car['car_logo'] ?><!--" >-->
                    </span>
                    </div>
                    <div class="middle">
                    <span>
                        <i><?= $car['card_brand'] ?><?= $car['car_series_name'] ?></i>
                        <a href="javascript:;" class="btn">
                            <?php echo $car['card_province'] . $car['card_char'] ?>·<?= $car['card_no'] ?>
                        </a>
                    </span>
                        <span>绑定时间：<?php echo date("Y.m.d H:i:s", $car['c_time']); ?></span>
                    </div>
                    <div class="right">
                        <a href="<?php echo Url::to(['bindcar', 'id' => $car['id']]); ?>" class="btn">修改</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="add-address-wrapper">
            <a href="<?php echo Url::to(['bindcar']) ?>" >
                <img src="/frontend/web/cloudcarv2/images/add.png" >
                <span>添加车辆信息</span>
            </a>
        </div>
    </div>

<?php $this->beginBlock('script'); ?>

<?php $this->endBlock('script'); ?>