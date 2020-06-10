<?php

use frontend\controllers\SegwayController;
use common\models\CarSegorder;
use common\models\Car_paternalor;
use yii\helpers\Url;

?>
    <div class="commom-order-top <?= CarSegorder::$order_doc[$info['status']]['class'] ?>">
        <div class="left">
            <?= CarSegorder::$order_doc[$info['status']]['html'] ?>
        </div>
        <div class="right">
            <img src="<?= SegwayController::STATIC_PATH ?>/img/<?= CarSegorder::$order_doc[$info['status']]['img'] ?>">
        </div>
    </div>
    <div class="order-details-wrapper">
        <ul class="order-details-ul">
            <li>
                <i>预约城市：</i>
                <span><?= $info['prepro'] . ' ' . $info['precity'] . ' ' . $info['precounty'] ?></span>
            </li>
            <li>
                <i>预约网点：</i>
                <span><?= $info['prestore'] ?></span>
            </li>
            <li>
                <i>预约用车时间：</i>
                <span><?= date('Y-m-d H:i:s', $info['pre_u_time']) ?></span>
            </li>
            <li>
                <i>预计还车时间：</i>
                <span><?= date('Y-m-d H:i:s', $info['pre_r_time']) ?></span>
            </li>
            <li>
                <i>联系人：</i>
                <span><?= $info['liaison'] ?></span>
            </li>
            <li>
                <i>联系电话：</i>
                <span><?= $info['telphone'] ?></span>
            </li>
            <li>
                <i>使用：</i>
                <span><?= $info['couponname'] ?></span>
            </li>
            <!--            已到达/服务中/已完成-->
            <?php if ($info['statua'] == ORDER_WAITING || $info['status'] == ORDER_SERVEING || $info['status'] == ORDER_SUCCESS): ?>
                <li>
                    <i>实际用车时间：</i>
                    <span><?= date('Y-m-d H:i:s', $info['rel_u_time']) ?></span>
                </li>
            <?php endif; ?>
            <!--            已完成-->
            <?php if ($info['status'] == ORDER_SUCCESS): ?>
                <li>
                    <i>实际还车时间：</i>
                    <span><?= date('Y-m-d H:i:s', $info['rel_r_time']) ?></span>
                </li>
            <?php endif; ?>
        </ul>
        <!--        已取消不显示-->
        <?php if ($info['status'] != ORDER_CANCEL): ?>
            <div class="remark-info">
                <span class="service-line">如有疑问请拨打 <a
                            href="tel:400-8801-768"><?= SegwayController::TELPHONE ?></a></span>
                <!--            已到达/服务中/已完成-->
                <?php if ($info['status'] == ORDER_WAITING || $info['status'] == ORDER_SERVEING || $info['status'] == ORDER_SUCCESS): ?>
                    <p class="arrive-tips">权益卡券范围外的费用，请线下和门店结算</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="order-details-wrapper">
        <ul class="order-details-ul">
            <li>
                <i>订单类型：</i>
                <span><?= Car_paternalor::$type[SEGWAY] ?></span>
            </li>
            <li>
                <i>订单编号：</i>
                <span><?= $info['orderid'] ?></span>
            </li>
            <li>
                <i>创建时间：</i>
                <span><?= date('Y-m-d H:i:s', $info['c_time']) ?></span>
            </li>
            <!--            已接单/已到达/服务中/已完成   有接单时间就显示-->
            <?php if ($info['r_time']): ?>
                <li>
                    <i>接单时间：</i>
                    <span><?= date('Y-m-d H:i:s', $info['r_time']) ?></span>
                </li>
            <?php endif; ?>
            <!--            已完成-->
            <?php if ($info['statua'] == ORDER_SUCCESS): ?>
                <li>
                    <i>服务完成时间：</i>
                    <span><?= date('Y-m-d H:i:s', $info['s_time']) ?></span>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <!--    已提交-->
<?php if ($info['status'] == ORDER_UNSURE): ?>
    <div class="commom-submit cancel-order-submit">
        <a href="javascript:;" class="btn-block cancelorder">取消订单</a>
    </div>
<?php endif; ?>

<?php $this->beginBlock('script'); ?>
    <script>
        window.addEventListener('pageshow', function (e) {
            // 通过persisted属性判断是否存在 BF Cache
            if (e.persisted) {
                location.reload();
            }
        });
        //取消订单
        var isSubmit = false;
        var id = "<?php echo $info['id'];?>";
        let pre_u_time = "<?php echo $info['pre_u_time'];?>";
        let c_time = "<?php echo time();?>";
        $('.cancelorder').click(function () {
            if (pre_u_time - c_time <= 60 * 30) {
                YDUI.dialog.toast('离预约时间小于30分钟，不能取消', 'none', 1500);
                return false;
            }
            if (isSubmit) {
                return false;
            }
            YDUI.dialog.confirm('提示', '确定要取消订单吗?', function () {
                isSubmit = true;
                YDUI.dialog.loading.open('提交中...');
                $.post("<?php echo Url::to(['cancelorder'])?>", {
                    id: id,
                }, function (json) {
                    YDUI.dialog.loading.close();
                    isSubmit = false;
                    YDUI.dialog.toast(json.msg, 'none', 1500);
                    if (json.status == <?php echo SUCCESS_STATUS;?>) {
                        window.location.reload();
                    }
                }, 'json');
            });

        });
    </script>
<?php $this->endBlock('script'); ?>