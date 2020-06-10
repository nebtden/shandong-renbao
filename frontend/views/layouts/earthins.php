<?php

use frontend\controllers\EarthinsController;

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <title><?php echo $this->context->site_title; ?></title>
    <?php if (isset($this->blocks['headStyle'])): ?>
        <?= $this->blocks['headStyle'] ?>
    <?php else: ?>
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/ydui.css" />
        <link rel="stylesheet" href="<?php echo EarthinsController::STATIC_PATH; ?>css/public.css"/>
        <link rel="stylesheet" href="<?php echo EarthinsController::STATIC_PATH; ?>css/base.css">
    <?php endif; ?>
    <script src="/frontend/web/cloudcarv2/js/ydui.flexible.js"></script>

    <?php if (isset($this->blocks['hStyle'])): ?>
        <?= $this->blocks['hStyle'] ?>
    <?php endif; ?>

    <?php if (isset($this->blocks['hScript'])): ?>
        <?= $this->blocks['hScript'] ?>
    <?php endif; ?>

</head>

<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
<?php if (isset($this->blocks['footer'])):; ?>
    <?= $this->blocks['footer'] ?>
<?php endif; ?>

<script src="<?php echo EarthinsController::STATIC_PATH; ?>js/jquery-2.1.4.js"></script>
<script src="<?php echo EarthinsController::STATIC_PATH; ?>js/public.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>
<script src="/frontend/web/cloudcarv2/js/commom.js?ver=1102"></script>

<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>

</html>
<?php $this->endPage() ?>
