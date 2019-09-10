
<body>
<div class="result-bg">
    <p class="piz-txt rsul-txt">
        <?php if($result['rewards_id']){
            echo '恭喜您获得<br/>'.$result['rewards'];
        }else{
            echo '下次活动18号<br/>不见不散';
        } ?>

    </p>
    <div class="piz-szf rsul-szf">
        <?php if($result['rewards_id']){ ?>
        <a href="way.html" class="idx-yk">立即领取</a>
        <?php } ?>
        <a  href="blessing.html" class="idx-yk">我要给TA送祝福</a>
        <p>送TA祝福，TA也有领取礼包的机会哦</p>
    </div>
    <!-- 中奖名单 -->
    <div class="pct-list rsul-list" id="scrollBox">
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
    <p class="idx-sm rsul-js">已有<?= $total ?>人参与<br/>
        最终解释权归临沂人保财险所有</p>

</div>


