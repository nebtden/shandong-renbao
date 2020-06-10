<?php

use yii\helpers\Url;

?>
<div class="yc-shuo-wrapper">
    <ul class="yc-shuo-ul">
        <?php foreach ($list as $val):?>
            <li>
                <a href="<?php echo Url::to(['car-news/detail','id'=>$val['id']])?>">
                    <span><?=$val['title'] ?></span>
                    <i class="icon-cloudCar2-jiantou"></i>
                </a>
            </li>
        <?php endforeach;?>
    </ul>
</div>

