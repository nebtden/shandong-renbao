<?php

use yii\helpers\Url;

?>
    <div class="bind-plateNumber">
        <ul class="bind-plateNumber-ul">
            <?php foreach ($list as $car): ?>
                <li>
                    <div class="li-left">
                        <img src="/frontend/web/cloudcar/images/car.png">
                    </div>
                    <div class="li-right">
<!--                        <div class="carBrand"><span>--><?//= $car['card_brand'] ?><!-----><?//=$car['car_model_small_name']?><!--</span></div>-->
                        <div class="carBrand"><span><?= $car['card_brand'] ?></span></div>
                        <div class="plateNumber"><?php echo $car['card_province'] . $car['card_char'] ?>
                            ·<?= $car['card_no'] ?></div>
                        <time>绑定时间：<?php echo date("Y.m.d H:i:s", $car['c_time']); ?></time>
                        <div class="plateNumber-modify"
                             onclick="location='<?php echo Url::to(['bindcar', 'id' => $car['id']]); ?>'">修改<i
                                    class="iconfont icon-car-jiantou"></i></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="add-car-info">
        <a href="<?php echo Url::to(['bindcar']) ?>">
            <img src="/frontend/web/cloudcar/images/add.png">
            <span>添加车辆信息</span>
        </a>
    </div>

<?php $this->beginBlock('script'); ?>

<?php $this->endBlock('script'); ?>