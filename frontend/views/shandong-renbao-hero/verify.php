
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
    <title>猜英雄 赢大奖</title>
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
        html {-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;font-size: 100px;background: #840f1c;}
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
        #afooter {color: #d1cfcf;font-size: 0.18rem;background: #000;line-height: 0.25rem;text-align: center;padding:
                0.12rem 0;width: 100%;}
        /* 内容 */
        .index-bg{background: url('/frontend/web/shandong-renbao-hero/images/index-bg.jpg')no-repeat;background-size: contain;height:19rem;}
        .index-conlist{background: #fff; color: #000; margin-top: 5.5rem; text-align: center;}
        .index-conlist h2{line-height: 0.7rem;}
        .yxgz{font-size: 0.3rem; display: block;margin: 0.3rem auto 0.3rem; text-align: left;  font-weight: bolder; width: 7.1rem;line-height: 0.4rem;}
        .yxgz h3{text-align: center; margin-bottom: 0.1rem;}
        .index-conlist h4{line-height: 0.4rem;}
        .index-sm{margin-top: 8rem; color: #ccc;}
    </style>

</head>

<body>
<div class="index-bg">
    <h1 class="hide">猜英雄 赢大奖</h1>
    <div class="index-conlist">
        <h2>活动时间：2019年9月18日~2019年9月23日</h2>
        <span class="yxgz">
               <h3>游戏规则：</h3>
               &nbsp &nbsp只要在“PICC临沂客户俱乐部”和“山东人保财险”微信公众号参与活动“猜英雄，赢大奖！”，
            即可获得超值大礼，相关奖券将于五个工作日内，通过注册完成“山东人保财险”微信公众号后，在“享服务”中“我的礼包”领取和使用。
           </span>
        <h4>注意：奖品数量有限，先到先得，<br>
            注册时一定要填“临沂地区”否则无法领取礼包。</h4>
    </div>


    <p class="index-sm">已有3201人参与<br />
        临沂人保财险保留在法律范围内对本活动的解释权</p>
</div>

<script >
    //自适应
    (function(win, doc) {
        if (!win.addEventListener) return;
        var html = document.documentElement;

        function setFont() {
            var html = document.documentElement;
            var k = 750;
            html.style.fontSize = html.clientWidth / k * 100 + "px";
        }
        setFont();
        setTimeout(function() {
            setFont();
        }, 300);
        doc.addEventListener('DOMContentLoaded', setFont, false);
        win.addEventListener('resize', setFont, false);
        win.addEventListener('load', setFont, false);
    })(window, document);
</script>
</body>

</html>