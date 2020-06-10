<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php $this->beginPage() ?>
<html lang="en">
<head >
    <meta charset="UTF-8">
    <meta name="wap-font-scale" content="no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telphone=no, email=no"/>
    <title><?php echo $this->context->title ?></title>
    <?php if (isset($this->blocks['headStyle'])): ?>
        <?= $this->blocks['headStyle'] ?>
    <?php else:?>
        <link rel="stylesheet" href="/frontend/web/cloudcarv2/css/ydui.css" />
        <link rel="stylesheet" href="/frontend/web/goldenegg/css/public.css">
        <link rel="stylesheet" href="/frontend/web/goldenegg/css/base.css">
    <?php endif; ?>
    <?php if(isset($this->blocks['hStyle'])):?>
        <?= $this->blocks['hStyle'] ?>
    <?php endif;?>

    <?php if(isset($this->blocks['hScript'])):?>
        <?= $this->blocks['hScript'] ?>
    <?php endif;?>
</head>
<?php $this->beginBody() ?>
    <body class="<?php echo $this->context->background ?>">
        <?= $content ?>
<?php $this->endBody() ?>
<script src="/frontend/web/goldenegg/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/cloudcarv2/js/ydui.js"></script>
<script src="/frontend/web/goldenegg/js/public.js"></script>
<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
