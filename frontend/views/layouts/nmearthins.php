<?php
use frontend\controllers\NmearthinsController;

?>

<?php $this->beginPage() ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="wap-font-scale" content="no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="format-detection" content="telphone=yes, email=no"/>
        <title><?php echo $this->context->site_title; ?></title>
        <?php if (isset($this->blocks['headStyle'])): ?>
            <?= $this->blocks['headStyle'] ?>
        <?php else: ?>
            <link rel="stylesheet" href="<?php echo NmearthinsController::STATIC_PATH; ?>css/public.css">
            <link rel="stylesheet" href="<?php echo NmearthinsController::STATIC_PATH; ?>css/base.css">
        <?php endif; ?>
        <style>
            html,body{  width: 100%; height: 100%;background-color: #ffffff  ;overflow:hidden}
        </style>

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


    <script src="<?php echo NmearthinsController::STATIC_PATH; ?>js/jquery-2.1.4.js"></script>
    <script src="<?php echo NmearthinsController::STATIC_PATH; ?>js/public.js"></script>

    <?php if (isset($this->blocks['script'])): ?>
        <?= $this->blocks['script'] ?>
    <?php endif; ?>

    </html>
<?php $this->endPage() ?>