<?php

use yii\helpers\Url;

?>
<div class="score-bg">
    <h1 class="hide">猜英雄 赢大奖</h1>
    <div class="scroe-kk"style="display: none" >
        <p class="scr-cs">0</p>
        <span >
               <h2>谢谢参与</h2>
                感谢您对临沂人保财险的关注<br/>
                送您一次抽奖的机会
          </span>
    </div>
    <div class="scroe-kk">
        <p class="scr-cs"><?=$scores?></p>
        <span>
                     <h2>恭喜您</h2>
                     获得1次抽奖机会
                </span>
    </div>
    <a href="javascript:;" class="scroe-btn">
        去抽奖
    </a>
    <!-- 底部申明 -->
    <p class="idx-sm score-sm">已有<?=$total?>人参与<br />
        最终解释权归临沂人保财险所有</p>

</div>

<script>


</script>

