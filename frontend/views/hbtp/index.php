<?php

use yii\helpers\Url;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <title>首页</title>
    <link rel="stylesheet" href="/frontend/web/hbtp/css/ydui.css" />
    <link rel="stylesheet" href="/frontend/web/hbtp/css/common.css">
    <link rel="stylesheet" href="/frontend/web/hbtp/css/all.css">
    <!-- 引入YDUI自适应解决方案类库 -->
    <script src="/frontend/web/hbtp/js/ydui.flexible.js"></script>
</head>
<body class="body-bg-index">
<div class="pull-page-wrapper">
    <img  data-url="/frontend/web/hbtp/images/pull-page1.jpg">
    <img  data-url="/frontend/web/hbtp/images/pull-page2.jpg">
    <img  data-url="/frontend/web/hbtp/images/pull-page3.jpg">
    <img  data-url="/frontend/web/hbtp/images/pull-page4.jpg">
    <img  data-url="/frontend/web/hbtp/images/pull-page5.jpg">
    <img  data-url="/frontend/web/hbtp/images/pull-page6.jpg">
    <div class="common-btn"><a class="btn-block btn-primary" href="<?php echo Url::to(['wpay/wash'])?>">立即使用洗车服务</a></div>
</div>
<script src="/frontend/web/hbtp/js/jquery-2.1.4.js"></script>
<script src="/frontend/web/hbtp/js/ydui.js"></script>
<script>
    //图片懒加载
    $('.pull-page-wrapper>img').lazyLoad();
</script>
</body>
</html>