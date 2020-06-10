<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/11 0011
 * Time: 上午 9:15
 */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->context->site_title; ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimum-scale=1,maximum-scale=1">
    <meta name="renderer" content="webkit">
    <meta name="x5-orientation" content="portrait">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta http-equiv="expires" content="10">
    <meta name="mobile-web-app-capable" content="yes">
    <?php if (isset($this->blocks['headStyle'])): ?>
        <?= $this->blocks['headStyle'] ?>
    <?php else:?>
        <link rel="stylesheet" href="/frontend/web/travel/css/chengtou-90f4f10114.css">
        <link rel="stylesheet" href="/frontend/web/travel/css/index.css">


    <?php endif; ?>

    <?php if(isset($this->blocks['hStyle'])):?>
        <?= $this->blocks['hStyle'] ?>
    <?php else:?>

    <?php endif; ?>
    <?php if(isset($this->blocks['hScript'])):?>
        <?= $this->blocks['hScript'] ?>
    <?php else:?>
        <script src="/frontend/web/travel/lib/js/jquery-2.4.1.js"></script>
        <script src="/frontend/web/travel/js/index.js"></script>
    <?php endif; ?>

</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
<?php if (isset($this->blocks['footer'])):; ?>
    <?= $this->blocks['footer'] ?>
<?php endif; ?>

<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
