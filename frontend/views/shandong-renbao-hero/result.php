<?php

use yii\helpers\Url;

?>
<div class="bg-packet">
    <!-- 评价结果 -->
    <div class="pckt-pj">
        <h1>评估结果</h1>
        <p><?= $result['tag']; ?>
        </p>
        <h2>车牌估值：<?= $result['value']; ?>RMB</h2>
    </div>
    <!-- 刮奖 -->
    <div id="scratch">
        <div id="card">
                <span>
                    <?php if($car['rewards']){
                        echo '恭喜您获得';
                    }else{
                        echo '非常遗憾';
                    } ?>

                    <p><?= $result['reward']; ?></p>
                </span>
        </div>
    </div>
    <a href="rewards.html?id=<?= $id ?>" >
        <img src="/frontend/web/shandong-renbao/images/go-lj.png" alt="去领奖" class="go-lj">
    </a>
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

</div>
<script src="/frontend/web/shandong-renbao/js/lucky-card.min.js"></script>

<script>
    function sendSms(){

        var id = <?= $id ?>;
        $.post('send.html',{id:id},function (data) {
            // alert(data);
        },'json');
    }



    LuckyCard.case({
        ratio:.3,callback:function(){
            this.clearCover();
            sendSms();
        }
    });



    // 名单滚动
    var area =document.getElementById('scrollBox');
    var con1 = document.getElementById('con1');
    var con2 = document.getElementById('con2');
    con2.innerHTML=con1.innerHTML;
    function scrollUp(){
        if(area.scrollTop>=con1.offsetHeight){
            area.scrollTop=0;
        }else{
            area.scrollTop++
        }
    }
    var time = 50;
    var mytimer=setInterval(scrollUp,time);
    area.οnmοuseοver=function(){
        clearInterval(mytimer);
    }
    area.οnmοuseοut=function(){
        mytimer=setInterval(scrollUp,time);
    }
</script>