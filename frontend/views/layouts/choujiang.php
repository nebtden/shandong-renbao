<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <link rel="stylesheet" href="/static/mobile/css/shopcommon1.css" type="text/css">
    <link rel="stylesheet" href="/static/mobile/css/css1.css" type="text/css"> 
    <script src="/static/mobile/js/jquery-1.10.1.js"></script>
</head>

<body style="background-color: #222;">
    <?php $this->beginBody() ?>
    
        <?= $content ?>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
