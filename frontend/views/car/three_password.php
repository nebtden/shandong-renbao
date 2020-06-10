<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7 0007
 * Time: 下午 4:31
 */
use yii\helpers\Url;
?>
<section class="contentFull bgColor overFlow secPadd">
    <div class="modifyTelTitle boxSizing">
        修改提现密码
    </div>
    <div class="shopPageCont NoMarginTop modifyPassImg webkitbox">
        <span class="afterFour cur"></span>
        <div class="liuchengImg">
            <em class="img">
                <img src="/frontend/web/images/shenfenhong.png">
            </em>
            <em class="txt cur">
                验证身份
            </em>
        </div>
        <span class="afterFour cur"></span>
        <div class="liuchengImg">
            <em class="img">
                <img src="/frontend/web/images/mimahong.png">
            </em>
            <em class="txt cur">
                修改登录密码
            </em>
        </div>
        <span class="afterFour cur"></span>
        <div class="liuchengImg">
            <em class="img">
                <img src="/frontend/web/images/wanchenghong.png">
            </em>
            <em class="txt cur">
                完成
            </em>
        </div>
        <span class="afterFour cur"></span>
    </div>
    <div class="YZMtellYou boxSizing" style="text-align: center">
        您的新密码修改成功，请妥善保管您的新密码！
    </div>
    <div class="duihuanBot boxSizing">
        <a href="<?php echo Url::to(["car/shop_core"])?>">返回商户中心</a>
    </div>
</section>
