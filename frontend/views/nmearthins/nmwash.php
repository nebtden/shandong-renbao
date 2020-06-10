<?php

use yii\helpers\Url;

?>
<body class="h1-bg">
<div class="btn-box">
    <?php $this->beginContent('@frontend/views/nmearthins/activeservice.php'); ?>
    <?php $this->endContent() ?>
    <?php if ($washCoupon): ?>
        <a href="<?php echo Url::to(['carwash/shoplist', 'company' => COMPANY_SHENGDA_WASH, 'couponId' => $washCoupon[0]['id']]) ?>">查看网点</a>
    <?php else: ?>
        <a href="<?php echo Url::to(['caruser/accoupon', 'curtimestamp' => time()]) ?>">查看网点</a>
    <?php endif; ?>
    <?php $this->beginContent('@frontend/views/nmearthins/mine.php'); ?>
    <?php $this->endContent() ?>
</div>
<?php $this->beginContent('@frontend/views/nmearthins/service.php'); ?>
<?php $this->endContent() ?>

</body>