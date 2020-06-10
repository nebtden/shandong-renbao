<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="author" content="Tencent-CP"/>
    <meta name="description"content=""/>
    <meta name="keywords"content=""/>
    <meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover"/>
    <meta name="format-detection" content="telephone=no">
    <meta content="yes" name="mobile-web-app-capable">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <title>中秋送福 感谢有礼</title>
    <link rel="stylesheet" href="/frontend/web/shandong-renbao-wish/css/rest.css">
    <link rel="stylesheet" href="/frontend/web/shandong-renbao-wish/css/swiper.min.css">
    <link rel="stylesheet" href="/frontend/web/shandong-renbao-wish/css/content.css">
    <script src="/frontend/web/shandong-renbao-wish/js/rest.js"></script>
    <script src="/frontend/web/shandong-renbao-wish/js/jquery-2.2.0.min.js"></script>
    <script src="/frontend/web/shandong-renbao-wish/js/common.js"></script>
    <script src="/frontend/web/shandong-renbao-wish/js/animate.js"></script>
    <script src="/frontend/web/shandong-renbao-wish/js/index.js"></script>
    <!-- Swiper JS -->
    <script src="/frontend/web/shandong-renbao-wish/js/swiper.min.js"></script>

</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>

<script src="/frontend/web/shandong-renbao-wish/js/show.js"></script>
<script src="/frontend/web/shandong-renbao-wish/js/tc.js"></script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?5f06545547077824d8b7eb47e0ac761f";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>

</body>
</html>
<?php $this->endPage() ?>
