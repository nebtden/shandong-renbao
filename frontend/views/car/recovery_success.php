<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 上午 9:34
 */
use yii\helpers\Url;

?>
<section class="contentFull overFlow">
    <dl class="huigouSuccess">
        <dt>
            <img src="/frontend/web/images/dahonggou.png">
        </dt>
        <dd>
            恭喜您回购成功！
        </dd>
    </dl>
    <div class="duihuanBot boxSizing" style="margin-top: 10%">
        <a href="<?php echo Url::to(['car/shop_core']);?>">确定</a>
    </div>
    <div class="YZMtellYou boxSizing">
        您可以点击<a href="<?php echo Url::to(['car/shop_core']);?>">“商户中心——金库”</a>查看资金信息或提现如果在使用过程中有疑难问题请拨打客服热线：<i>400-000-000</i>
    </div>
</section>
