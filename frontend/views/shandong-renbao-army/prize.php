<div class="prize-bg">
    <h1 class="hide">胜利大阅兵</h1>
    <div class="prize-con">
            <span>
                恭喜您获得<br/>
                <?= $result['rewards'] ?>
            </span>
        <p>分享3位朋友参加抽奖，<br/>
            您将获得电影票1张或洗车1次。</p>
    </div>
    <a href="prize-step.html?id=<?= $id ?>" class="prize-btn">去使用</a>
    <a href="javascript:popShow('pop1');" class="prize-btn">分享给好友</a>

    <!-- 弹窗 -->
    <div  class="pop pop1" id="pop1">
        <div class="pop1_cont3">
            <div class="pop1-con1">
                <img src="/frontend/web/shandong-renbao-army/images/jt.png" alt="箭头">
                <p>邀请好友一起来抽奖</p>
            </div>
        </div>
    </div>
</div>
<script src="/frontend/web/shandong-renbao-army/js/show.js"></script>
<script src="/frontend/web/shandong-renbao-army/js/index.js"></script>