<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/5
 * Time: 13:35
 */

use yii\helpers\Url;
?>
    <div class="commom-order-header finish">
        <div class="left commom-img order-details-img">
            <img src="/frontend/web/cloudcarv2/images/disinfect.png" >
        </div>
        <div class="right">订单<?= $info['status_text'] ?></div>
    </div>
    <div class="order-details-wrapper">
        <ul class="order-details-ul">
            <li>
                <i>使用门店: </i>
                <span><?= $info['shopName'] ?>(<?= $info['shopAddress'] ?>)</span>
            </li>
            <li>
                <i>使用券：</i>
                <span><?= $info['serviceName'] ?> <i class="price">抵扣￥<?= $info['amount'] ?></i></span>
            </li>
            <li>
                <i>服务码：</i>
                    <span id="qr-code" style="width:100px; height:100px; margin-top:10px;"></span>
            </li>
        </ul>
    </div>
    <div class="order-details-wrapper">
        <ul class="order-details-ul">
            <li>
                <i>订单类型：</i>
                <span>臭氧杀菌服务</span>
            </li>
            <li>
                <i>订单编号：</i>
                <span><?= $info['main_ordersn'] ?></span>
            </li>
            <li>
                <i>创建时间：</i>
                <span><?php echo date("Y-m-d H:i:s", $info['c_time']); ?></span>
            </li>
            <?php if($info['s_time']): ?>
                <li>
                    <i>服务完成时间：</i>
                    <span><?php echo date('Y-m-d H:i:s', $info['s_time']) ?></span>
                </li>
            <?php endif ?>
        </ul>
    </div>
<?php if($info['status'] == ORDER_HANDLING):?>
    <div class="order-details-wrapper">
        <div class="commom-submit need-submit">
            <a class="btn-block btn-primary small-popup-btn" id='cancel' href="#" >取消订单</a>
        </div>
    </div>
<?php endif;?>
<?php $this->beginBlock('script');?>
<script src="/frontend/web/cloudcarv2/js/qrcode.min.js"></script>
<script>
        //展示二维码
        var qrcode = new QRCode(document.getElementById('qr-code'), {
            width : 100,
            height : 100
        });
        function makeCode (text) {
            qrcode.makeCode(text);
        }
        makeCode('<?= $info['consumer_code'] ?>');

        //取消订单
        $('#cancel').on('click',function(){
            var id = <?= $info['id'] ?>;
            YDUI.dialog.loading.open('订单取消中');
            $.ajax({
                url: '<?php echo Url::to(['car-disinfect/cancelorder']) ?>',
                data: {id:id},
                type: 'POST',
                dataType: 'json',
                timeout:6000,
                success: function(json){
                    console.log(json);
                    if(json.status == 1){
                        YDUI.dialog.loading.close();
                        YDUI.dialog.toast('订单取消成功', 'success',1000, function(){
                            $('#cancel').parent().remove();
                            window.location.href = '<?php echo Url::to(['caruser/coupon']) ?>'
                        });

                    } else {
                        YDUI.dialog.loading.close();
                        YDUI.dialog.toast(json.msg, 'error',1000, function(){

                        });
                    }
                },
                complete: function(XMLHttpRequest,status){
                    if(status == 'timeout'){
                        YDUI.dialog.loading.close();
                        YDUI.dialog.toast('请求超时', 'error',1000, function(){
                        });
                    }
                },
                error: function(XMLHttpRequest) {
                    YDUI.dialog.loading.close();
                    YDUI.dialog.toast('订单取消失败', 'error',1000, function(){
                    });
                }
            })
        })

    </script>
<?php $this->endBlock('script');?>