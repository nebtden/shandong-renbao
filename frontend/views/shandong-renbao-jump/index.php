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
    <title>车主“跳一跳”</title>
    <link rel="stylesheet" href="/frontend/web/shandong-renbao-jump/css/rest.css">
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
    <style>
        /*reset*/
        ::-webkit-input-placeholder {color: #999; }
        input[type="text"],input[type="password"],select {-webkit-appearance: none;appearance: none;outline: none;-webkit-tap-highlight-color: rgba(0, 0, 0, 0);border-radius: 0;font-family:
                '\5FAE\8F6F\96C5\9ED1';box-sizing: border-box;}
        html,body,div,p,ul,li,dl,dt,dd,em,i,span,a,img,input,h1,h2,h3,h4,h5 {margin: 0;padding: 0; line-height: 1;}
        img {border: 0;display: block; }
        a,button,input {-webkit-tap-highlight-color: rgba(255, 0, 0, 0);}
        ol,ul {list-style: none;}
        table { border-collapse: collapse;border-spacing: 0;}
        html {-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;font-size: 100px;background: #f8d4d3;}
        body,html {-webkit-tap-highlight-color: transparent;-webkit-user-select: none;width: 100%;font-size:0.18rem;color: #fff;text-align: center;}
        .c:after { content: '\20'; display: block; height: 0; line-height: 0; visibility: hidden;clear: both;}
        .hide {height:0.1rem;font-size: 0;line-height: 0;visibility: hidden;overflow: hidden;}
        a{text-decoration: none;display: block;}
        a:focus { outline: none}
        .fl {float: left;}
        .fr {float: right;}
        .pr { position: relative;}
        .pa {position: absolute;}
        .t { text-indent: -9999rem; display: block;}
        #afooter {color: #d1cfcf;font-size: 0.18rem;background: #000;line-height: 0.25rem;text-align: center;padding:0.12rem 0;width: 100%;}

        /* 内容部分 */
        .bg-award {
            width: 7.5rem;
            height: 11.92rem;
            background: url('/frontend/web/shandong-renbao-jump/images/index-bg.jpg')no-repeat;
            background-size: 100%;
        }

        .bg-award a {
            /*  background: url('/frontend/web/shandong-renbao-jump/images/btn-end.png')no-repeat; */
            /* ------活动开始按钮------ */
          background: url('/frontend/web/shandong-renbao-jump/images/btn-start.png')no-repeat; 
            background-size: 100%;
            width: 3.5rem;
            height: 0.85rem;
            border-radius: 0.5rem;
            position: relative;
            top: 5rem;
            left: 0.4rem;
        }
    </style>
</head>

<body>
<div class="bg-award">
    <h1 class="hide">有奖测试,看看你的性格归属</h1>
    <a href="http://507535.m.chaoapp.cn/mobile/newgame/index.jsp?aid=57e43ac2f2af4e3f8d68dd40e1363a51&activityid=144446&wuid=507535&keyversion=0&isFromApiFilter=1">

    </a>
</div>

</body>

</html>