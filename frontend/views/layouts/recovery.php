<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 上午 9:09
 */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php echo $this->context->sitetitle;?></title>
    <link rel="stylesheet" href="/frontend/web/css/ydui.css" />
    <link rel="stylesheet" href="/frontend/web/css/common.css">
    <link rel="stylesheet" href="/frontend/web/icons/iconfont.css">
    <link rel="stylesheet" href="/frontend/web/css/addCss.css">
    <script src="/frontend/web/js/ydui.flexible.js"></script>
</head>
<body class="body">
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
<?php if(isset($this->blocks['footer'])):;?>
    <?= $this->blocks['footer'] ?>
<?php endif;?>
<script src="/frontend/web/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/js/ydui.js"></script>
<?php if(isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>
<?php endif;?>
</body>
</html>
<?php $this->endPage() ?>

