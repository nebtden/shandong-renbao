<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php echo $this->context->title;?></title>
    <link rel="stylesheet" href="/static/mobile/h5/css/swiper.min.css">
    <link rel="stylesheet" href="/static/mobile/h5/css/animate.css">
    <link rel="stylesheet" href="/static/mobile/h5/css/shopcommon.css" type="text/css">
    <link rel="stylesheet" href="/static/mobile/h5/css/activeH5.css" type="text/css">
    <script src="/static/mobile/js/jquery-1.10.1.js"></script>
</head>

<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
