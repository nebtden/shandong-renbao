<?php
use yii\helpers\Url;
use frontend\controllers\NmearthinsController;

?>
<body class="index-bg">
<div class="itemWrap">
    <a href="<?php echo Url::to(['nmwash']) ?>"><img src="<?php echo NmearthinsController::STATIC_PATH; ?>images/icon-wash.png">清洗检测</a>
    <a href="<?php echo Url::to(['nmins']) ?>"><img src="<?php echo NmearthinsController::STATIC_PATH; ?>images/icon-check.png">代办年检</a>
    <a href="http://zh.schengle.com/ShengDaSZYCJC/szyc/index.jhtm"><img src="<?php echo NmearthinsController::STATIC_PATH; ?>images/icon-safe.png">安全检测</a>
</div>
</body>