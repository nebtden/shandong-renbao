<?php

use yii\helpers\Url;

?>
<div class="bg-index">
    奖励id代表：1电商优惠券   2单次免费洗车   3单次浪漫鲜花
    <br>
    <br>
    id--手机&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;   --车牌--奖励id--推广父类ID
    <br>
    <?php foreach ($cars as $car){
        echo $car->id.'-'.$car->mobile.'-'.$car->license_plate.'--'.$car->rewards.'--'.$car->parent_id;
        echo '<br>';

    } ?>
</div>

