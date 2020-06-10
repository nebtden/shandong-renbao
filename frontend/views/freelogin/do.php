<?php

use yii\helpers\Url;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <title>安全出行 太保相随</title>
    <!-- 引入YDUI自适应解决方案类库 -->
    <style>
        html, body, * {
            padding: 0;
            margin: 0;
        }

        img {
            max-width: 100%;
        }
    </style>
</head>
<body>
<div>
    <img src="/frontend/web/cloudcar/images/tp1.jpg" alt="">
</div>
<div onclick="location='<?php echo Url::to(['caruser/accoupon']); ?>'">
    <img src="/frontend/web/cloudcar/images/tp2.jpg" alt="">
</div>
</body>
</html>