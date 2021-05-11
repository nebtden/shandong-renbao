<?php

use yii\helpers\Url;

?>
<div class="bg-index">
     
    
	id,车牌,奖励id,推广父类ID
    <br>
    <br>
    <?php foreach ($cars as $car){
        echo $car->id.','.$car->mobile.','.$car->license_plate.','.$car->rewards.','.$car->parent_id;
        echo '<br>';

    } ?>
</div>

