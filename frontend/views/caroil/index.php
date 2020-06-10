<?php

use yii\helpers\Url;

?>
    <div class="oil-card-header">
        <div class="up">
            <button type="button" class="btn"
                    onclick="location.href='<?php echo Url::to(['bind', 'id' => $info['id']]) ?>'">更改
            </button>
        </div>
        <div class="down">
            <div class="left commom-img">
                <img src="/frontend/web/cloudcarv2/images/<?= $info['imgName'] ?>.png">
            </div>
            <div class="right">
                <i><?= $info['type_txt'] ?>卡号</i>
                <span><?= $info['oil_card_no'] ?></span>
            </div>
        </div>
    </div>
    <div class="youka-link-wrapper">
        <a href="javascript:;">
            <i>油卡充值券</i>
            <div class="right">
                <i>油卡充值服务</i>
                <span class="price"></span>
                <em class="icon-cloudCar2-jiantou"></em>
            </div>
        </a>
    </div>
    <div class="recharge-tip">
        注：<br>
        1、在有效期内，您可以使用服务券进行储值IC卡充值。<br>
        2、本服务券支持对应的中石油或中石化储值IC卡充值。<br>
        3、中石油储值IC卡支持充值个人记名卡、企业非增票主卡。<br>
        4、中石化储值IC卡支持充值个人记名主卡，个人不记名卡和企业非增票主卡。<br>
        5、<i class="price">每天充值不超过5次/张卡；每月充值不超过10次/张卡；每月充值不超过5000元/张卡；10分钟内只能提交一次</i>。<br>
        6、如有疑问，请致电客服：<i class="price"><a href="tel:400-617-1981" data-role="button" data-theme="a">400-617-1981</a></i>
    </div>
    <div class="commom-submit comfirm-recharge-submit" style="bottom: 1.7rem">
        <a href="javascript:;" class="btn-block">确认充值</a>
        <input type="hidden" id="cid" value="0">
    </div>

    <!-- 选择油卡充值券 -->
    <div class="commom-input-place select-driving-popup">
        <ul class="card-list-ul">
            <?php if (empty($list)): ?>
                <div class="uncoupons-tip-wrapper">
                    <div class="uncoupons-tip-text">您暂时还没有此类型的优惠券哦</div>
                    <div class="send-comfirm uncoupons-back">
                        <a href="<?php echo Url::to(['caruser/accoupon'])?>" class="btn-block btn-primary">去激活</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($list as $val): ?>
                    <li class="service-jiayou" data-content="<?= $val['name'] ?>" data-id="<?= $val['id'] ?>">
                        <div class="title ">
                            <i>油卡充值服务</i>
                            <span>&yen;<?= floatval($val['amount']) ?></span>
                        </div>
                        <div class="content">
                            <div class="up">
                                <div class="left">
                                    <span>服务码: <?= $val['coupon_sn'] ?></span>
                                    <span>有效期至：<?= $val['show_coupon_endtime'] ?></span>
                                </div>
                                <div class="right">
                                    <a class="btn" data-id="<?= $val['id'] ?>" data-price="<?= $val['amount'] ?>"
                                       href="javascript:;">选择</a>
                                </div>
                            </div>
                            <div class="down">
                                <div class="card-explain">
                                    <em class="icon-cloudCar2-qiaquanshuoming"></em>
                                    <span>卡券说明</span>
                                    <i class="icon-cloudCar2-jiantou_down"></i>
                                </div>
                                <div class="explain-content">
                                    <ol class="use-instructions-ol">
                                        <?php foreach ($use_text[$val['coupon_type']] as $txt): ?>
                                            <li><?= $txt ?></li>
                                        <?php endforeach; ?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

<?php $this->beginBlock('script'); ?>
    <script>
        //点击油卡充值券
        $('.youka-link-wrapper>a').on('click', function () {
            $('.commom-input-place').hide();
            $('.select-driving-popup').show();
        });
        //选择点击油卡充值券
        $('.card-list-ul>li>.content .btn').on('click', function (e) {
            e.stopPropagation();
            $('#cid').val($(this).data('id'));
            $('.youka-link-wrapper .price').html('&yen;' + $(this).data('price'));
            $('.commom-input-place').hide();
        });
        //展开收起说明
        $('.card-explain').on('click', function (e) {
            e.stopPropagation();
            var isShow = $(this).attr('data-show');
            if (isShow == 'true') {
                $(this).attr('data-show', 'fasle')
                $(this).next('.explain-content').hide(10);
                $(this).find('i').removeClass('icon-zjlt-jiantou_up').addClass('icon-zjlt-jiantou_down');
            } else {
                $(this).attr('data-show', 'true')
                $(this).next('.explain-content').show(10);
                $(this).find('i').removeClass('icon-zjlt-jiantou_down').addClass('icon-zjlt-jiantou_up');
            }
        });

        $(".btn-block").click(function () {
            var id = $('#cid').val();
            var isSubmit = false;
            if (id>0) {
                if (isSubmit){
                    return false;
                }
                isSubmit = true;
                YDUI.dialog.confirm('提示', '确定使用该加油券吗？', function () {
                    var url = '<?php echo Url::to(["playorder"])?>';
                    YDUI.dialog.loading.open('正在提交');
                    $.post(url, {cid: id}, function (json) {
                        isSubmit = false;
                        YDUI.dialog.loading.close();
                        if (json.status == 1) {
                            //跳到订单页
                            window.location.href = '<?php echo Url::to(["caruorder/oilinfo"]);?>'+'?id='+json.data.id;
                        } else {
                            YDUI.dialog.toast(json.msg, 'none', 1500);
                        }
                    }, 'json');
                });
            } else {
                YDUI.dialog.alert('没有选择加油券');
            }
        });
    </script>
<?php $this->endBlock('script'); ?>