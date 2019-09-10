<?php

use yii\helpers\Url;

?>
<div class="index-bg">
    <div class="idx-szf">
        <a href="javascript:popShow('pop_per');"  class="idx-yk">领取祝福礼包</a>
        <a href="blessing.html" class="idx-yk">我要给TA送祝福</a>
        <p>送TA祝福，TA也有领取礼包的机会哦</p>
    </div>
    <!-- 中奖名单 -->
    <div class="pct-list" id="scrollBox">

        <ul id="con1">
            <li>
                <b>131****3658</b>
                <span>单次免费洗车</span>
            </li>
            <li>
                <b>137****6106</b>
                <span>单次浪漫鲜花</span>
            </li>
            <li>
                <b>135****5930</b>
                <span>电商优惠券</span>
            </li>
            <li>
                <b>138****1817</b>
                <span>单次免费洗车</span>
            </li>
            <li>
                <b>135****5506</b>
                <span>单次浪漫鲜花</span>
            </li>
            <li>
                <b>130****4785</b>
                <span>电商优惠券</span>
            </li>
            <li>
                <b>177****4871</b>
                <span>电商优惠券</span>
            </li>
            <li>
                <b>189****5506</b>
                <span>单次免费洗车</span>
            </li>
            <li>
                <b>130****7575</b>
                <span>九阳免安装洗碗机</span>
            </li>
            <li>
                <b>152****6421</b>
                <span>九阳破壁免滤豆浆机</span>
            </li>

        </ul>
        <ul id="con2"></ul>

    </div>
    <p class="idx-sm">已有<?= $total ?>人参与<br/>
        最终解释权归临沂人保财险所有</p>


</div>
<div  class="pop" id="pop_per">
    <div class="pop_cont3">
        <input type="text" class="numb" placeholder="请输入手机号码">
        <input type="text" class="numb1" placeholder="请输入验证码">
        <a href="javascript:;" class="hqyzm"></a>
    </div>
    <a href="javascript:;" class="idx-yk tohy">领取礼包</a>
    <a href="javascript:popHide();" title="点击关闭" class="btn close"></a>
</div>
<script>
    $('.hqyzm').click(function () {
        var mobile = $('.numb').val();
        //检测手机号码
        if(!(/^1[3456789]\d{9}$/.test(mobile))){
            alert("手机号码有误，请重填");
            return false;
        }

        $.post('code.html',{mobile:mobile},function (data) {
            // console.log();
            if(data.status==0){
                alert(data.msg);

            }else if(data.status==-1){
                alert(data.msg);
                window.location.href = 'result.html?id='+data.data.id;
            }else{
                alert('短信发送成功！');
            }
        },'json');
    });

    $(".tohy").click(function () {
        var mobile = $('.numb').val();
        var code = $('.numb1').val();

        if(!(/^1[3456789]\d{9}$/.test(mobile))){
            alert("手机号码有误，请重填1");
            return false;
        }

        if(code.length!=6){
            alert("验证码错误,请检查！");
            return false;
        }

        $.post('submit.html',{mobile:mobile,code:code},function (data) {
            if(data.status==1){
                window.location.href = 'result.html?id='+data.data.id;
            }else if(data.status==-1){
                alert(data.message);
                window.location.href = 'result.html?id='+data.data.id;
            }else{
                alert(data.message);
            }
        },"json");
    });

</script>


<audio id="Jaudio" class="media-audio" src="/frontend/web/shandong-renbao-wish/music.mp3" preload loop="loop"></audio >
<script type="text/javascript">
    function audioAutoPlay(id){
        var audio = document.getElementById(id);
        audio.play();
        document.addEventListener("WeixinJSBridgeReady", function () {
            audio.play();
        }, false);
    }
    audioAutoPlay('Jaudio');
</script>


