<?php

use yii\helpers\Url;

?>
<style>
    .commom-address-ul>li>span {
        font-size: .28rem;
    }
</style>
<?php if($type):?>
    <?php if ($pecResult):?>
    <div class="regulate-title">
        <div class="regulate-symbol">违</div>
        <span><i>2</i>未处理违章</span>
    </div>
    <div class="regulate-des">
        <ul class="commom-address-ul">
            <li>
                <p>驾驶机动车违反道路交通信号灯通行的</p>
                <p>湖南省长沙市雀园路</p>
                <p class="regulate-time"><span>2018-09-25</span><span>罚款150元</span><span>记6分</span></p>
            </li>
            <li>
                <p>驾驶机动车违反道路交通信号灯通行的</p>
                <p>湖南省长沙市雀园路</p>
                <p class="regulate-time"><span>2018-09-25</span><span>罚款150元</span><span>记6分</span></p>
            </li>
        </ul>
    </div>
    <p class="regulate-tips">请先处理违章后再预约办理代办年检</p>
    <!--无违章，显示此段-->
    <?php else:?>
    <div class="no-regulate">
        <img src="/frontend/web/cloudcarv2/img/no-regulate.png">
        <p>真棒！您的爱车没有违章！</p>
    </div>
    <?php endif;?>
    <div class="commom-submit ">
        <a href="javascript:location.replace('<?php echo Url::to(["preorder",'carId'=>$carinfo['id']]);?>');" class="btn-block">返&nbsp;&nbsp;回</a>
    </div>
<?php else:?>
    <div class="commom-regulate">
        <ul class="commom-address-ul">
            <li>
                <i>车牌号码：</i>
                <span><?=$carinfo['card_province'] . $carinfo['card_char'] . $carinfo['card_no']?></span>
            </li>
            <li>
                <i>注册日期：</i>
                <span><?= date('Y-m-d',$carinfo['rg_time'])?></span>
            </li>
            <li>
                <i>发动机号：</i>
                <input type="text" name="motor" placeholder="请填写发动机号后6位">
            </li>
            <li>
                <i>车架号码：</i>
                <input type="text" name="carframe"  placeholder="请填写车架号后6位">
            </li>
        </ul>
    </div>
    <p class="regulate-tips">请补充填写发动机号及车架号码以便进行违章查询</p>
    <div class="commom-submit ">
        <a href="javascript:;" class="btn-block">查&nbsp;&nbsp;询</a>
    </div>
<?php endif;?>
<div class="commom-tabar-height"></div>
<?php $this->beginBlock('script'); ?>
<script>
    //提交
    var isSubmit = false;
    $('.commom-submit').click(function () {
        if(isSubmit){
            return false;
        }else{
            var motor = $('input[name="motor"]').val();
            var carframe = $('input[name="carframe"]').val();
            if (motor == '' || carframe=='') {
                YDUI.dialog.toast('请填写完整信息', 1000);
                return false;
            }
            YDUI.dialog.loading.open('查询中...');
            isSubmit = true;
            window.location.replace('<?php echo Url::to(["peccancy",'carId'=>$carinfo['id'],'type'=>1]);?>'+'&motor='+motor+'&carframe='+carframe);
        }
    });
</script>
<?php $this->endBlock('script'); ?>
