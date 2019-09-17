<?php

use yii\helpers\Url;

?>
<div class="index-bg" >
    <h1 class="hide">猜英雄赢大奖</h1>
    <div class="idx-szf">

        <a href="question.html?id=1">
            <img src="/frontend/web/shandong-renbao-hero/images/cs-btn.png" alt="" class="cs-btn">
        </a>
        <div class="index-con1">
            <img src="/frontend/web/shandong-renbao-hero/images/index.jpsz.jpg" alt="">
        </div>
    </div>
    <!-- 中奖名单 -->
     <div class="pct-list" id="scrollBox">
        <ul id="con1">
            <li>
                <b>131****3658</b>
                <span>铝合金香氛双号停车牌</span>
            </li>
            <li>
                <b>137****6106</b>
                <span>单次浪漫鲜花</span>
            </li>
            <li>
                <b>135****8830</b>
                <span>华帝多功能消毒刀架</span>
            </li>
            <li>
                <b>138****1733</b>
                <span>单次免费洗车</span>
            </li>
            <li>
                <b>135****5506</b>
                <span>铝合金香氛双号停车牌</span>
            </li>
            <li>
                <b>130****4785</b>
                <span>电商优惠券</span>
            </li>
            <li>
                <b>177****5272</b>
                <span>铝合金香氛双号停车牌</span>
            </li>
            <li>
                <b>189****4356</b>
                <span>单次免费洗车</span>
            </li>
            <li>
                <b>130****7575</b>
                <span>多功能家车两用工具箱户外灯</span>
            </li>
            <li>
                <b>152****6421</b>
                <span>单次免费洗车</span>
            </li>
        </ul>
        <ul id="con2"></ul>

    </div>
    <p class="idx-sm">已有<?= $total ?>人参与<br/>
        临沂人保财险保留在法律范围内对本活动的解释权</p>

</div>
<audio id="Jaudio" class="media-audio" src="/frontend/web/shandong-renbao-hero/music.mp3" preload loop="loop"></audio >
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

