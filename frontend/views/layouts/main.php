<?php
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
    <link rel="stylesheet" href="/frontend/web/css/shopcommon.css" type="text/css">
    <link rel="stylesheet" href="/frontend/web/css/css.css" type="text/css">
    <link rel="stylesheet" href="/frontend/web/css/lCalendar.css" type="text/css">
</head>
<body class="body">
    <?php $this->beginBody() ?>
    
        <?= $content ?>

    <?php $this->endBody() ?>
    <?php if(isset($this->blocks['footer'])):;?>
        <?= $this->blocks['footer'] ?>
    <?php else:?>
        <footer class="footMenu webkitbox boxSizing">
            <ul class="right webkitbox">
                <li>
                    <a href="<?php echo Url::to(['car/shop_list']);?>">服务网点</a>
                </li>
                <li>
                    <a href="<?php echo Url::to(['car/recovery']);?>">兑换</a>
                </li>
                <li>
                    <a href="<?php echo Url::to(['car/shop_core']);?>">商户中心</a>
                </li>
            </ul>
        </footer>
    <?php endif;?>
    <script src="/frontend/web/js/jquery-1.10.1.js"></script>
    <script src="/frontend/web/js/MeTool.js"></script>
    <script src="/frontend/web/js/alert.js"></script>
    <?php if(isset($this->blocks['script'])): ?>
        <?= $this->blocks['script'] ?>
    <?php endif;?>
</body>
</html>
<?php $this->endPage() ?>
