<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5 0005
 * Time: 下午 5:12
 */
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->context->site_title; ?></title>
    <?php if (isset($this->blocks['headStyle'])): ?>
        <?= $this->blocks['headStyle'] ?>
    <?php else:?>
        <link rel="stylesheet" href="/frontend/web/calendar/css/reset.css" />
        <link rel="stylesheet" href="/frontend/web/calendar/css/font-set.css">
        <link rel="stylesheet" href="/frontend/web/calendar/css/index.css">
    <?php endif; ?>
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

<?php $this->endBody() ?>
<?php if (isset($this->blocks['footer'])):; ?>
    <?= $this->blocks['footer'] ?>
<?php endif; ?>

<script src="/frontend/web/calendar/js/jquery.min.js"></script>
<script src="/frontend/web/calendar/js/fixScreen.js"></script>

<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
