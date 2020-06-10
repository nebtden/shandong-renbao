<?php

use yii\helpers\Url;

?>
<body class="h3-bg" >
<div class="btn-box">
    <?php $this->beginContent('@frontend/views/nmearthins/activeservice.php'); ?>
    <?php $this->endContent() ?>
    <a href="<?php echo Url::to(['inspection/index'])?>">马上办理</a>
    <a href="javascript:;">六年以上</a>
</div>
<?php $this->beginContent('@frontend/views/nmearthins/service.php'); ?>

<?php $this->endContent() ?>
</body>