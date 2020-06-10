<?php

use yii\helpers\Url;

?>
<body class="h2-bg">
<div class="btn-box">
    <?php $this->beginContent('@frontend/views/nmearthins/activeservice.php'); ?>
    <?php $this->endContent() ?>
    <a href="#">查看网点</a>
    <?php $this->beginContent('@frontend/views/nmearthins/mine.php'); ?>
    <?php $this->endContent() ?>
</div>
<?php $this->beginContent('@frontend/views/nmearthins/service.php'); ?>

<?php $this->endContent() ?>
</body>