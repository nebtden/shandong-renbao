<?php

use frontend\controllers\SegwayController;
use yii\helpers\Url;

?>
    <style>
        .commom-submit > .btn-disabled {
            height: .9rem;
            line-height: .9rem;
            background-color: #ccc;
            border-radius: 30px;
            font-size: .36rem;
            color: #fff;
            position: relative;
            pointer-events: auto;
            text-align: center;
            border: none;
            width: 100%;
            display: block;
            margin-top: .5rem;
        }
    </style>
    <div class="commom-img">
        <img src="<?= SegwayController::STATIC_PATH ?>/img/daibu-banner.jpg">
        <p class="service-rule"><i class="icon-warn-outline"></i>险后代步车服务说明</p>
    </div>
    <div class="operation-order">
        <p>险后代步车预约流程</p>
        <div class="progress-wrap"><img src="<?= SegwayController::STATIC_PATH ?>/img/progress.png"></div>
        <ul class="progress-ul">
            <li><span>填写出险信息<br/>预约借车</span></li>
            <li><span>客服回电<br/>核对真实性</span></li>
            <li><span>约定时间<br/>提供借车</span></li>
            <li><span>按照约定<br/>进行还车</span></li>
        </ul>
    </div>
    <div class="yuyue-info">
        <ul class="year-testing-ul yuyue-info-ul">
            <li>
                <i>车辆选择</i>
                <div class="right">
                    <select name="carId" id="carId">
                        <option value="0">请选择年检车辆</option>
                        <?php foreach ($carinfo as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= ($v['id'] == $seginfo['carid']) ? 'selected' : '' ?>><?= $v['card_province'] . $v['card_char'] . $v['card_no'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
                </div>
                <a href="<?= Url::to(['caruser/bindcar']) ?>" class="add-car-btn">添加车辆</a>
            </li>
            <li>
                <i>保单号</i>
                <div class="right">
                    <input type="text" name="policyNum" placeholder="请输入本车保单号" id="policyNum"
                           value="<?= $seginfo['policyNum'] ?>">
                </div>
            </li>
            <li>
                <i>保单号照</i>
                <div class="right">
                    <div class="file-box">
                        <?php if (!$seginfo): ?>
                            <div class="file-wrap">
                                <i class="camera-bg"></i>
                                <p>拍照需含<br/>保险公司与保单号</p>
                            </div>
                        <?php endif; ?>
                        <img src="<?= json_decode($seginfo['img_url_json'])->policyPic1 ?>"
                             class="<?= $seginfo ? 'img-show' : '' ?>"/>
                        <input type="hidden" name="policyPic1"
                               value="<?= json_decode($seginfo['img_url_json'])->policyPic1 ?>">
                        <input class="file" type="file" name="policyPic1" onchange="handleFiles(this)"/>
                    </div>
                </div>
                <div class="file-eg" data-name="baodan">
                    <img src="<?= SegwayController::STATIC_PATH ?>/img/file-01.png">
                    <p>点击图片查看示例</p>
                </div>
            </li>
            <li>
                <i>4S店维修工单</i>
                <div class="right">
                    <div class="file-box">
                        <?php if (!$seginfo): ?>
                            <div class="file-wrap">
                                <i class="camera-bg"></i>
                                <p>拍照需含4S店名称<br/>与维修工单号</p>
                            </div>
                        <?php endif; ?>
                        <img src="<?= json_decode($seginfo['img_url_json'])->policyPic2 ?>"
                             class="<?= $seginfo ? 'img-show' : '' ?>"/>
                        <input type="hidden" name="policyPic2"
                               value="<?= json_decode($seginfo['img_url_json'])->policyPic2 ?>">
                        <input class="file" type="file" name="policyPic2" onchange="handleFiles(this)"/>
                    </div>
                </div>
                <div class="file-eg" data-name="gongdan">
                    <img src="<?= SegwayController::STATIC_PATH ?>/img/file-02.png">
                    <p>点击图片查看示例</p>
                </div>

            </li>
        </ul>
        <div class="voucher-wrap <?= $couponinfo ? 'to_select' : 'to_exchange' ?>">
            <i>优惠券</i>
            <div class="right">

                <input type="hidden" name="couponid" value="<?= $couponinfo ? $couponinfo['id'] : 0 ?>">
                <?php if ($couponinfo): ?>
                    <i><?= $couponinfo['name'] ?></i>
                <?php else: ?>
                    <i>无可用劵</i>
                <?php endif; ?>
            </div>
            <em class="icon-cloudCar2-jiantou"></em>
        </div>
        <p class="voucher-tips">客服会对您提交的预约信息进行回电并核对真实性，若您的车辆并未出险，可能无法提供代步车服务。</p>
    </div>
    <div class="commom-submit">
        <button type="button" class="<?= $couponinfo ? 'btn-block' : 'btn-disabled' ?>">下一步</button>
    </div>
    <!-- 示例图弹框 -->
    <div class="commom-popup-outside examples-popup-outside " style="display:none;">
        <div class="commom-popup">
            <div class="title"><span></span><i class="icon-error"></i></div>
            <div class="content">
                <div class="same-examples commom-img examples-baodan"><img
                            src="<?= SegwayController::STATIC_PATH ?>/img/baodan.jpg"></div>
                <div class="same-examples commom-img examples-gongdan"><img
                            src="<?= SegwayController::STATIC_PATH ?>/img/gongdan.jpg"></div>
            </div>
        </div>
    </div>
    <!--    服务规则弹框-->
    <div class="commom-popup-outside service-rule-popup" style="display:none;">
        <div class="commom-popup">
            <div class="close-btn"><i class="icon-error-outline"></i></div>
            <div class="content">
                <div class="common-des rule-des">
                    <p><em class="icon-cloudCar2-qiaquanshuoming"></em>服务规则</p>
                    <ol>
                        <li>车型选择：经济型轿车（别克凯越或类似车型）、舒适型轿车（大众朗逸或类似车型）。</li>
                        <li>服务范围：覆盖全国31个省，245个城市，具体见范围列表；</li>
                        <li>服务对象：权益用户户；</li>
                        <li>服务响应时间：全天24小时可预约服务，取车还车（上门取送车）时间按各门店营业时间执行；</li>
                        <li>预约规则：提前1天来电预约服务，最迟提供提前三小时预约服务；</li>
                        <li>取消规则：需至少在取车时间提前3小时来电取消，否则扣除一天权益；</li>
                        <li>续租规则：需至少于还车时间提前24小时来电确认该车辆是否能续租；</li>
                    </ol>
                </div>
                <div class="common-des focus-des">
                    <p><i class="icon-warn-outline"></i>注意事项</p>
                    <ol>
                        <li>服务以自然天（一天24小时）计算，超时还车1小时或以上均另算一天权益；超过免费权益的天数需按门店标价现场付费；</li>
                        <li>车辆保险包含交强险（免赔额1500元）、车损险、第三者责任险（20万）；</li>
                        <li>如权益客户需要可购买附加险；</li>
                        <li>用户无需现场支付押金，由盛大做担保，如发生违章罚款、维修、赔偿等费用，盛大会向用户追偿，如用户不配合，需要机构协助追偿；</li>
                        <li>
                            租赁期间如发生事故，需第一时间拨打110进行报案，并拨打4008801768告知情况，根据客服的处理指引配合操作，提交相应材料；如因客户未及时报备导致无法报保险，客户需自行承担所有维修及赔偿费用；
                        </li>
                        <li>租赁期间如发生事故，用户需根据门店规则，承担车辆经营损失费，车辆贬值损失；基本保险中免赔额内的费用，以及基本保险理赔范围外的损失；</li>
                        <li>
                            用户自理燃油费，交接车辆时客户与门店服务人员共同确认当前油量，还车时应保持与取车时同等油量，如油量不足，客户须按市场当期油价现场支付差额油量的燃油费，并额外向门店支付燃油费20%-50%的加油服务费；
                        </li>
                        <li>用户可以选择购买最低免赔险每天65元，产生的事故及保险费用无最低赔付额，如不购买则出现事故及出险后最低1500元以下不赔付；</li>
                        <li>门店价格会常有浮动，可以参考门店价，但门店浮动价格会经常变化。</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- 选择优惠券 -->
    <div class="commom-input-place select-daibu-popup">
        <ul class="card-list-ul">
            <?php foreach ($couponres as $k => $v): ?>
                <li class="service-daibu-car">
                    <div class="title ">
                        <i data-id="<?= $v['id'] ?>"><?= $v['name'] ?></i>
                    </div>
                    <div class="content">
                        <div class="up">
                            <div class="left">
                                <span>服务码: <?= $v['coupon_sn'] ?></span>
                                <span>有效期至：<?= date("Y-m-d", $v['use_limit_time']) ?></span>
                                <?php if ($v['status'] == 2): ?>
                                    <span>使用日期：<?= $v['use_time'] ? date("Y-m-d H:i:s", $v['use_time']) : '' ?></span>
                                <?php endif; ?>
                                <span>仅用于抵扣使用代步车服务时所产生的费用</span>
                            </div>
                            <div class="right">
                                <a class="btn <?= $v['status'] < 2 ? 'to_use' : '' ?>" href="javascript:;">
                                    <?php if ($v['status'] == 2): ?>
                                        已使用
                                    <?php elseif ($v['status'] == 3): ?>
                                        已失效
                                    <?php else: ?>
                                        使用
                                    <?php endif; ?>
                                </a>
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
                                    <?php foreach ($use_text[$v['coupon_type']] as $txt): ?>
                                        <li><?= $txt ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php $this->beginBlock('script'); ?>
    <script>
        //下一步
        var isSubmit = false;
        $('.commom-submit>.btn-block').on('click', function () {
            if (isSubmit) {
                return false;
            }
            //验证...
            var carId = $('#carId').val();
            var policyNum = $('#policyNum').val();
            var policyPic1 = $('input[name="policyPic1"]').val();
            var policyPic2 = $('input[name="policyPic2"]').val();
            var couponid = $('input[name="couponid"]').val();
            if (carId == '0') {
                YDUI.dialog.toast('请选择车辆', 'none', 1500);
                return false;
            }
            if (policyNum == '') {
                YDUI.dialog.toast('请输入您的保单号', 'none', 1500);
                return false;
            }
            if (!policyPic1 || !policyPic2) {
                YDUI.dialog.toast('请上传全部图片', 'none', 1500);
                return false;
            }
            YDUI.dialog.loading.open('跳转中...');
            isSubmit = true;
            $.post("<?php echo Url::to(['saveone'])?>", {
                carId: carId,
                policyNum: policyNum,
                policyPic1: policyPic1,
                policyPic2: policyPic2,
                couponid: couponid,
            }, function (res) {
                YDUI.dialog.loading.close();
                isSubmit = false;
                if (res.status == 1) {
                    location.href = '<?= Url::to(["segway/appointment"])?>';
                } else {
                    YDUI.dialog.toast(json.msg, 'none', 1500);
                }
            }, 'json');
        });

        //示例弹框显示
        $('.file-eg').on('click', function () {
            var str1 = '保单号照',
                str2 = '4S店维修工单',
                $popup = $('.examples-popup-outside'),
                $examples = $('.same-examples'),
                name = $(this).attr('data-name');
            $examples.hide();
            switch (name) {
                case 'baodan':
                    $popup.find('.title>span').text(str1);
                    $('.examples-baodan').show();
                    break;
                case 'gongdan':
                    $popup.find('.title>span').text(str2);
                    $('.examples-gongdan').show();
                    break;
            }
            $popup.show();
        });

        //服务规则弹框显示
        $('.service-rule').on('click', function () {
            $('.service-rule-popup').show()
        });

        //示例弹框关闭
        $('.commom-popup>.title>i,.commom-popup>.close-btn>i').on('click', function () {
            $('.commom-popup-outside').hide();
        });
        //去兑换
        $('.to_exchange').on('click', function () {
            location.href = '<?= Url::to(["caruser/accoupon"])?>';
        });

        //点击代步券
        $('.to_select').on('click', function () {
            $('.commom-input-place').hide();
            $('.select-daibu-popup').show();
        });
        //选择代步券
        $('.card-list-ul>li>.content .to_use').on('click', function (e) {
            var _str = $(this).parents('.service-daibu-car').find('.title>i').text();
            var _id = $(this).parents('.service-daibu-car').find('.title>i').data('id');
            e.stopPropagation();
            $('.commom-input-place').hide();
            $('.to_select').children('.right').find('i').text(_str);
            $('.to_select').children('.right').find('input').val(_id);
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

        //上传图片
        function handleFiles(obj) {
            var f = obj.files;
            for (var i = 0; i < f.length; i++) {
                var reader = new FileReader();
                reader.readAsDataURL(f[i]);
                reader.onload = function (e) {
                    if (isSubmit) {
                        return false;
                    }
                    YDUI.dialog.loading.open('上传中...');
                    isSubmit = true;
                    var attrName = $(obj).attr('name');
                    var picValue = e.target.result;
                    $.post("<?php echo Url::to(['uploadimg'])?>", {
                        attrName: attrName,
                        picValue: picValue
                    }, function (json) {
                        YDUI.dialog.loading.close();
                        isSubmit = false;
                        if (json.status == 1) {
                            $(obj).siblings('img').attr('src', json.msg);
                            $(obj).prev().val(json.msg);
                            $(obj).siblings('.file-wrap').remove();
                            $(obj).siblings('img').addClass('img-show');
                        } else {
                            YDUI.dialog.toast(json.msg, 'none', 1500);
                        }
                    }, 'json');
                }
            }
        }
    </script>
<?php $this->endBlock('script'); ?>