<?php
use yii\helpers\Url;
?>
<div class="leftCont boxSizing">
    <div class="containY">
        <ul class="lfcTop boxSizing webkitbox clearfix">
            <li><a href="#"><img src="../images/jieti.png"></a></li>
            <li><a href="#"><img src="../images/bi.png"></a></li>
            <li><a href="#"><img src="../images/guanliyuan.png"></a></li>
            <li><a href="#"><img src="../images/sz.png"></a></li>
        </ul>
        <ul class="lfcMid">
            <?php foreach($this->context->menu as $k=> $m){?>
            <li>
                <div class="liTitle boxSizing"><img src="../images/<?php echo $m['icon'];?>"><span><?php echo $k; ?></span></div>
                <div class="liList" <?php if($this->context->id==$m['cur']) echo 'style="display:block;"'; ?>>
                    <?php if($m['subs']){ ?>
                    <?php foreach($m['subs'] as $i=> $v) {
                            $ex=explode('/',$v['url']);
                            ?>
                    <p  <?php if($this->context->action->id==$ex[1]) echo 'class="cur"'; ?>><a href="<?php  echo Url::to([$v['url']]); ?>"><?php echo $i; ?></a></p>
                    <?php } ?>
                    <?php } ?>
                </div>
                <div class="rightPos">
                    <div class="PosThree"></div>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>
<!--    <div class="containX">-->
<!--        <div class="lfcTop1">-->
<!--            <div class="fourDiv boxSizing clearfix">-->
<!--                <p></p><p></p><p></p><p></p>-->
<!--            </div>-->
<!--            <ul class="forDivHeng">-->
<!--                <li><a href="#"><img src="../images/jieti.png"></a></li>-->
<!--                <li><a href="#"><img src="../images/bi.png"></a></li>-->
<!--                <li><a href="#"><img src="../images/guanliyuan.png"></a></li>-->
<!--                <li><a href="#"><img src="../images/sz.png"></a></li>-->
<!--            </ul>-->
<!--        </div>-->
<!--        <ul class="lfcMid1">-->
<!--            <li>-->
<!--                <div class="liTitle1"><img src="../images/kzt.png"></div>-->
<!--                <div class="liList1">-->
<!--                    <p class="cur"><a href="#">用户信息</a></p>-->
<!--                    <p><a href="#">收件箱</a></p>-->
<!--                    <p><a href="#">售价单</a></p>-->
<!--                    <p><a href="#">购物车</a></p>-->
<!--                    <p><a href="#">时间轴</a></p>-->
<!--                    <p><a href="#">卖家等级信息</a></p>-->
<!--                </div>-->
<!--                <div class="rightPos1">-->
<!--                    <div class="PosThree1"></div>-->
<!--                </div>-->
<!--            </li>-->
<!--            <li>-->
<!--                <div class="liTitle1"><img src="../images/kzt.png"></div>-->
<!--                <div class="liList1">-->
<!--                    <p class="cur"><a href="#">用户信息</a></p>-->
<!--                    <p><a href="#">收件箱</a></p>-->
<!--                    <p><a href="#">售价单</a></p>-->
<!--                    <p><a href="#">购物车</a></p>-->
<!--                    <p><a href="#">时间轴</a></p>-->
<!--                    <p><a href="#">卖家等级信息</a></p>-->
<!--                </div>-->
<!--                <div class="rightPos1">-->
<!--                    <div class="PosThree1"></div>-->
<!--                </div>-->
<!--            </li>-->
<!--            <li>-->
<!--                <div class="liTitle1"><img src="../images/kzt.png"></div>-->
<!--                <div class="liList1">-->
<!--                    <p class="cur"><a href="#">用户信息</a></p>-->
<!--                    <p><a href="#">收件箱</a></p>-->
<!--                    <p><a href="#">售价单</a></p>-->
<!--                    <p><a href="#">购物车</a></p>-->
<!--                    <p><a href="#">时间轴</a></p>-->
<!--                    <p><a href="#">卖家等级信息</a></p>-->
<!--                </div>-->
<!--                <div class="rightPos1">-->
<!--                    <div class="PosThree1"></div>-->
<!--                </div>-->
<!--            </li>-->
<!--            <li>-->
<!--                <div class="liTitle1"><img src="../images/kzt.png"></div>-->
<!--                <div class="liList1">-->
<!--                    <p class="cur"><a href="#">用户信息</a></p>-->
<!--                    <p><a href="#">收件箱</a></p>-->
<!--                    <p><a href="#">售价单</a></p>-->
<!--                    <p><a href="#">购物车</a></p>-->
<!--                    <p><a href="#">时间轴</a></p>-->
<!--                    <p><a href="#">卖家等级信息</a></p>-->
<!--                </div>-->
<!--                <div class="rightPos1">-->
<!--                    <div class="PosThree1"></div>-->
<!--                </div>-->
<!--            </li>-->
<!--        </ul>-->
<!--    </div>-->
    <div class="zdQiehuan">
        <div class="posZ"></div>
    </div>
</div>
