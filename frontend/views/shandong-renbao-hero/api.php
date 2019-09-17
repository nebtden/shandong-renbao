<?php

use yii\helpers\Url;

?>
<div class="bg-index">
    奖励id代表：
    1	华帝多功能消毒刀架
    <br>
    2	多功能家车两用工具箱户外灯
    <br>
    3	铝合金香氛双号停车牌
    <br>
    4	洗车单次
    <br>
    5	鲜花（乐享）
    <br>
    6	E惠通（乐享）
    <br>
    <br>
    id--手机&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;   --奖励id--群id--创建时间
    <br>
    <?php foreach ($cars as $car){
        echo $car->id.'-'.$car->mobile.'-'.'--'.$car->rewards_id.'--'.$car->group_id.$car->created_at;
        echo '<br>';

    } ?>
</div>

