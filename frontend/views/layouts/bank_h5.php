<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <title><?php echo $this->context->title;?></title>
    <?php if (isset($this->blocks['headStyle'])): ?>
        <?= $this->blocks['headStyle'] ?>
    <?php else:?>
        <link rel="stylesheet" href="/frontend/web/h5/css/ydui.css">
        <link rel="stylesheet" href="/frontend/web/h5/css/public.css">
        <link rel="stylesheet" href="/frontend/web/h5/css/ciao.css">
    <?php endif;?>
    <?php if(isset($this->blocks['hStyle'])):?>
        <?= $this->blocks['hStyle'] ?>
    <?php endif;?>

    <?php if(isset($this->blocks['hScript'])):?>
        <?= $this->blocks['hScript'] ?>
    <?php endif;?>
</head>

<body>
<?php $this->beginBody() ?>

<?= $content ?>
<script src="/frontend/web/h5/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/h5/js/ydui.flexible.js"></script>
<script src="/frontend/web/h5/js/ydui.js"></script>
<?php $this->endBody() ?>
<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
