
<?php
use yii\helpers\Url;
?>
<style>
    .hidden{
        display: none;
    }
</style>
    <div class="commom-order-header <?php if(in_array($info['status'],[7,8,9])): ?>cancel <?php  else:?>finish<?php endif; ?> ">
        <div class="left commom-img order-details-img">
            <img src="/frontend/web/cloudcarv2/images/load-rescue.png" >
        </div>
        <div class="right">
            <?= $info['status_text']; ?>
        </div>
    </div>
    <div class="order-details-wrapper">
        <ul class="order-details-ul">
            <li>
                <i>救援位置: </i>
                <span><?= $info['faultaddress']  ?></span>
            </li>
            <li>
                <i>救援项目: </i>
                <span><?= $info['type_text']  ?></span>
            </li>
            <li>
                <i>故障车辆：</i>
                <span><?= $info['carno'] ?></span>
            </li>
            <li>
                <i>使用券：</i>
                <span> <?=  $info['coupon_name']?> <i class="price">抵扣￥<?= $info['coupon_amount'] ?></i></span>
            </li>
            <li>
                <i>联系电话：</i>
                <span><?= $info['phone'] ?></span>
            </li>
        </ul>
        <div class="remarks-info">
            <span class="title">备注信息</span>
            <span class="content"><?= $info['remark'] ?></span>
        </div>
    </div>
    <div class="order-details-wrapper">
        <ul class="order-details-ul">
            <li>
                <i>订单类型：</i>
                <span>救援服务</span>
            </li>
            <li>
                <i>订单编号：</i>
                <span><?= $info['orderid'] ?></span>
            </li>
            <li>
                <i>创建时间：</i>
                <span><?= date('Y-m-d H:i:s',$info['c_time'] ) ?></span>
            </li>
            <?php  if($info['acceptance_time']): ?>
            <li>
                <i>接单时间：</i>
                <span><?= $info['acceptance_time']?date('Y-m-d H:i:s',$info['acceptance_time'] ):'' ?></span>
                <?php endif; ?>
            </li>
            <?php  if($info['complete_time']): ?>
            <li>
                <i>服务完成时间：</i>
                <span><?= $info['complete_time']?date('Y-m-d H:i:s',$info['complete_time'] ):'' ?></span>
            </li>
            <?php endif; ?>
        </ul>
    </div>

<!--  <div class="commom-submit evaluate-submit">
      <a href="javascript:;" class="btn-block">评价订单</a>
  </div>-->
<?php if(in_array($info['status'],[0,1])): ?>
  <div class="commom-submit cancel-order-submit">
      <a href="javascript:;" class="btn-block">取消订单</a>
  </div>
<?php  endif; ?>

    <div class="recharge-tip <?php if(in_array($info['status'],[0,1])): ?>hidden<?php endif; ?>" style="text-align: center;" >
        如有疑问，请拔打： <i class="price"><a href="tel:4001084001" data-role="button" data-theme="a">4001084001</a></i>
    </div>



    <div class="commom-tabar-height"></div>
<script src="/frontend/web/hbtp/js/jquery-2.1.4.js"></script>
<script>
    //同步订单状态及位置
    var order_no =  '<?= $info['orderid'] ?>';
    var sync_order = function () {
        sync_order_timer = setInterval(function () {
            $.post('<?php echo Url::to(["carrescue/syncorder"])?>', {order_no: order_no}, function (json) {
                console.log(json.data.status);
                sync_back(json.data.status, json.data.lbs);
            }, 'json');
        }, 5000);
    };

    //根据订单状态做出相应的反应
    var sync_back = function (sta, points) {

        //根据状态处理
        sta = parseInt(sta);
        switch (sta) {
            case 0:
                break;
            case 1:
                //待受理
                if (order_sta !== 1) {
                    order_sta = 1;
                }
                break;
            case 2:
                //已受理
                if(order_sta !== 2){
                    $(".commom-order-header .right").text('救援方已经受理');
                    order_sta = 2;
                }
                $('.cancel-order-submit').hide();
                $('.recharge-tip').show();

                break;
            case 3:
                //已调派
                if(order_sta !== 3){
                    $(".commom-order-header .right").text('救援方已调派，正在火速赶来');
                    order_sta = 3;
                }
                $('.cancel-order-submit').hide();
                $('.recharge-tip').show();
                break;
            case 4:
                //已回拨
                if(order_sta !== 4){
                    $(".commom-order-header .right").text('救援方已回拨');
                    order_sta = 4;
                }
                $('.cancel-order-submit').hide();
                $('.recharge-tip').show();
                break;
            case 5:
                //已到达
                if(order_sta !== 5){
                    $("commom-order-header .right").text('救援方已到达救援地点，开始救援');
                    order_sta = 5;
                }
                $('.cancel-order-submit').hide();
                $('.recharge-tip').show();
                break;
            case 6:
                //已完成
                if(order_sta !== 6){
                    $(".commom-order-header .right").text('救援已完成');
                    window.clearInterval(sync_order_timer);
                    order_sta = 6;
                }
                $('.cancel-order-submit').hide();
                $('.recharge-tip').show();
                break;
            default:
                window.clearInterval(sync_order_timer);
                // $('.commom-order-header').addClass('cancel');
                // $('.commom-order-header').removeClass('finish');
                // $(".commom-order-header .right").text('系统已取消');
                // $('.cancel-order-submit').hide();
                // location.reload();
                //状态7，8，9，订单取消
                order_sta = sta;
                break;
        }
    };


    //取消订单
    var isCancelOrder = false;
    $('.cancel-order-submit').on('touchstart', function () {
        if (isCancelOrder) return false;
        isCancelOrder = true;
        $.post("<?php echo Url::to(['carrescue/cancel'])?>", {order_no: order_no}, function (json) {
            isCancelOrder = false;
            if (json.status === 1) {
                $('.commom-order-header').addClass('cancel');
                $('.commom-order-header').removeClass('finish');
                $(".commom-order-header .right").text('您已经取消此次救援');
                $('.cancel-order-submit').hide();
                $('.recharge-tip').show();
                //取消定时任务
                window.clearInterval(sync_order_timer);

            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });
    $(function(){
        var status = <?= $info['status'] ?>;
        if(status!=7 || status!=8 || status!=9){
            sync_order();
        }
    });

</script> 
