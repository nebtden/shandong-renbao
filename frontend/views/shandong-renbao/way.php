<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="author" content="Tencent-CP" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="format-detection" content="telephone=no">
    <meta content="yes" name="mobile-web-app-capable">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <title>抓住您的梦想之车</title>

    <script>
        //自适应
        (function (win, doc) {
            if (!win.addEventListener) return;
            var html = document.documentElement;

            function setFont() {
                var html = document.documentElement;
                var k = 750;
                html.style.fontSize = html.clientWidth / k * 100 + "px";
            }
            setFont();
            setTimeout(function () {
                setFont();
            }, 300);
            doc.addEventListener('DOMContentLoaded', setFont, false);
            win.addEventListener('resize', setFont, false);
            win.addEventListener('load', setFont, false);
        })(window, document);
    </script>
    <link rel="stylesheet" href="/frontend/web/shandong-renbao/css/index.css">
    <style>
    </style>
</head>

<body>
<header>
    <img src="/frontend/web/shandong-renbao/img/title.png" alt="抓住您的梦想之车">
</header>
<div class="main">
    <span class="txt1"> <b>临沂人保财险线上趣味游戏</b> 参与即有惊喜</span>
    <p class="p1">活动时间：2019年11月18日~11月28日</p>
    <span class="p2">(只限临沂本地私家车主参与，否则中奖视为无效)</span>
    <img src="/frontend/web/shandong-renbao/img/xian.jpg" alt="分割线" class="main-pic">
    <img src="/frontend/web/shandong-renbao/img/car.png" alt="输入车牌" class="main-pic1">

    <div class="main-input">
        <input type="text" value="鲁" maxlength="1">
        <input type="text" id="ipu1" maxlength="1">
        <i>.</i>
        <input type="text" name="sn1" maxlength="1" id="sn1">
        <input type="text" name="sn2" maxlength="1" id="sn2">
        <input type="text" name="sn3" maxlength="1" id="sn3">
        <input type="text" name="sn4" maxlength="1" id="sn4">
        <input type="text" name="sn5" maxlength="1" id="sn5">
        <div class="tishi" style="display: none;">抱歉，此活动只限临沂私家车主参与</div>
        <div class="tishi1" style="display: none;">请填写完整</div>

        <?php if ($begin){ ?>
            <!-- 提交按钮 -->
            <a href="javascript:;" class="active-btn active-btn1">
                提交
            </a>
        <?php }else{ ?>
            <a href="javascript:;" class=" active-btn ">
                活动即将开始
            </a>
        <?php  } ?>

        <img src="/frontend/web/shandong-renbao/img/pic.png" alt="pic" class="main-tu">
    </div>
</div>
<footer>
    本活动最终解释权归临沂人保财险所有
</footer>

<script src="/frontend/web/shandong-renbao/js/jquery-2.2.0.min.js"></script>
<script>
    $("#ipu1").focus();
    $(function () {
        function device_verify() {
            // console.log($("#sn2").val() + $("#sn3").val() + $("#sn4").val() + $("#sn5").val());
        }

        //自动跳到下一个输入框
        $("input[name^='sn']").each(function () {
            $(this).keyup(function (e) {
                if ($(this).val().length < 1) {
                    $(this).prev().focus();
                } else {
                    if ($(this).val().length >= 1) {
                        $(this).next().focus();
                    }
                }
            });

        });
        $("input[type='text'][id^='sn']").bind('keyup',
            function () {
                var len = $("#sn1").val().length + $("#sn2").val().length + $("#sn3").val().length + $("#sn4").val()
                    .length + $("#sn5").val().length;
                if (len == 4) device_verify();
            });
    });



    $(function () {
        $(".active-btn").click(function () {
            var kong = false;
            $('input').each(function () {
                if (!$(this).val()) {
                    $(".tishi1").css("display", "block")
                    $(".tishi").css("display", "none")
                    kong = true;
                    return false;
                }
            });

            if (kong) {
                return false;
            }

            if ($('#ipu1').val() != "Q" && $('#ipu1').val() != "q") {
                $(".tishi").css("display", "block")
                $(".tishi1").css("display", "none")
                return false;
            }

            window.location.href = 'http://507535.m.chaoapp.cn/mobile/newgame/index.jsp?aid=1f4002bfd7574869b90fa25c9df5dffb&activityid=146004&wuid=507535&keyversion=0&isFromApiFilter=1'

        });
    })
</script>
</body>

</html>