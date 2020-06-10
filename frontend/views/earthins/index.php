<?php

use yii\helpers\Url;
use frontend\controllers\EarthinsController;

?>
<?php $this->beginBlock('hStyle'); ?>
<style>
    html, body {
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        overflow: hidden
    }
</style>
<?php $this->endBlock('hStyle'); ?>
<body class="h1-bg">
<div class="btn-box">
    <a href="<?php echo Url::to(['activate-page']) ?>">
        <img src="<?php echo EarthinsController::STATIC_PATH;?>images/btnl_icon.png" alt="">
    </a>
    <a href="<?php echo Url::to(['user-flow-page']) ?>">
        <img src="<?php echo EarthinsController::STATIC_PATH;?>images/btnc_icon.png" alt="">
    </a>
    <a href="<?php echo Url::to(['caruser/coupon']) ?>">
        <img src="<?php echo EarthinsController::STATIC_PATH;?>images/btnr_icon.png" alt="">
    </a>
</div>
</body>
