<?php

use yii\helpers\Url;

?>
<div class="add-car-wrapper">
    <input type="hidden" name="id" value="<?php echo $data ? $data['id'] : 0; ?>">
    <input type="hidden" name="carId" value="<?php echo $data ? $data['carId'] : 0; ?>">
    <ul class="car-info-ul">
        <li class="pinpai">
            <i>选择品牌</i>
            <span id="brand"><?php echo $data ? $data['card_brand'] : '请选择'; ?></span>
            <em class="icon-cloudCar2-jiantou"></em>
            <input type="hidden" name="card_brand" value="<?php echo $data ? $data['card_brand'] : ''; ?>">
            <input type="hidden" name="car_brand_id" value="<?php echo $data ? $data['car_brand_id'] : 0; ?>">
            <input type="hidden" name="car_series_name" value="<?php echo $data ? $data['car_series_name'] : ''; ?>">
            <input type="hidden" name="car_series_id" value="<?php echo $data ? $data['car_series_id'] : 0; ?>">
            <input type="hidden" name="car_logo" value="<?php echo $data ? $data['car_logo'] : ''; ?>">
        </li>
        <li class="car-type">
            <i>选择车型</i>
            <span id="bmodel"><?php echo $data ? $data['car_model_small_fullname'] : '请选择'; ?></span>
            <em class="icon-cloudCar2-jiantou"></em>
            <input type="hidden" name="car_model_small_fullname"
                   value="<?php echo $data ? $data['car_model_small_fullname'] : ''; ?>">
            <input type="hidden" name="car_model_name" value="<?php echo $data ? $data['car_model_name'] : ''; ?>">
            <input type="hidden" name="car_model_id" value="<?php echo $data ? $data['car_model_id'] : ''; ?>">
            <input type="hidden" name="car_model_small_id"
                   value="<?php echo $data ? $data['car_model_small_id'] : ''; ?>">
            <input type="hidden" name="car_model_small_name"
                   value="<?php echo $data ? $data['car_model_small_name'] : ''; ?>">
        </li>
    </ul>
    <div class="chepai-type-wrapper">
        <span class="title">车牌类型</span>
        <div class="chepai-radio">
            <?php foreach ($car_types as $k => $ct): ?>
                <span><i class="icon-cloudCar2-radio <?php if ($data && $data['card_type'] == $k) {
                        echo 'icon-cloudCar2-radioactive';
                    } elseif ($k == 1) {
                        echo 'icon-cloudCar2-radioactive';
                    } ?> " data-id="<?= $k ?>"></i><em><?= $ct ?></em></span>
            <?php endforeach; ?>
            <input type="hidden" name="card_type" value="<?php echo $data ? $data['card_type']:1 ?>">
        </div>
        <div class="chepai-number">
            <span>车牌号码</span>
            <span class="select-province"
                  data-ydui-actionsheet="{target:'#actionSheet-province',closeElement:'#cancel'}">
                    <em><?php echo $data ? $data['card_province'] : '湘'; ?></em><i
                        class="icon-cloudCar2-sanjiaoxing2"></i>
                </span>
            <input type="hidden" name="card_province" value="<?php echo $data ? $data['card_province'] : '湘'; ?>">
            <span class="select-Letter" data-ydui-actionsheet="{target:'#actionSheet-letter',closeElement:'#cancel'}">
                    <em><?php echo $data ? $data['card_char'] : 'A'; ?></em><i class="icon-cloudCar2-sanjiaoxing2"></i>
                </span>
            <input type="hidden" name="card_char" value="<?php echo $data ? $data['card_char'] : 'A'; ?>">
            <input type="text" maxlength="5" onKeyUp="value=value.replace(/[\W]/g,'')" name="card_no" placeholder="请输入车牌后5位"
                   value="<?php echo $data ? $data['card_no'] : ''; ?>">
        </div>
    </div>
</div>
<div class="year-testing-wrapper">
    <ul class="year-testing-ul add-car-ul">
        <li>
            <i>车辆类型</i>
            <div class="right">
                <select name="inspectionVehicleType">
                    <option value="0" >请选择车辆类型</option>
                    <option value="1" <?php echo $data['inspectionVehicleType']==1 ? 'selected': ''; ?>>6座级以下车辆</option>
                    <option value="10" <?php echo $data['inspectionVehicleType']==10 ? 'selected': ''; ?> >7座及以上车辆、面包车</option>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
        <li>
            <i>车辆注册日期</i>
            <div class="right">
                <input type="date" id="date" name="rg_time" value="<?php echo $data ? $data['rg_time'] : ''; ?>" placeholder="">
<!--                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>-->
            </div>
        </li>
<!--        <li>-->
<!--            <i>座位数</i>-->
<!--            <div class="right">-->
<!--                <input type="number" name="carSeats" value="--><?php //echo $data ? $data['carSeats'] : 0; ?><!--" placeholder="座位数">-->
<!--            </div>-->
<!--        </li>-->
        <li>
            <i>是否运营车辆</i>
            <div class="select-radio">
                    <span>
                        <i class="icon-cloudCar2-radio <?php echo $data['operationType']==1 ? 'icon-cloudCar2-radioactive' : ''; ?>" data-val="1"></i>
                        <em>是</em>
                    </span>
                <span>
                        <i class="icon-cloudCar2-radio <?php echo $data['operationType']==1 ? '' : 'icon-cloudCar2-radioactive'; ?>" data-val="0"></i>
                        <em>否</em>
                    </span>
                <input type="hidden" name="operationType" value="<?php echo $data['operationType']==1 ? '1' : '0'; ?>">
            </div>
        </li>
        <li>
            <i>上次年检至今造成 过人员伤亡事故</i>
            <div class="select-radio">
                    <span>
                        <i class="icon-cloudCar2-radio <?php echo $data['isAccidentCar']==1 ? 'icon-cloudCar2-radioactive' : ''; ?> " data-val="1"></i>
                        <em>是</em>
                    </span>
                <span>
                        <i class="icon-cloudCar2-radio <?php echo $data['isAccidentCar']==1 ? '' : 'icon-cloudCar2-radioactive'; ?>" data-val="0"></i>
                        <em>否</em>
                    </span>
                <input type="hidden" name="isAccidentCar" value="<?php echo $data['isAccidentCar']==1 ? '1' : '0'; ?>">
            </div>
        </li>
        <li>
            <i>车辆所有人</i>
            <div class="select-radio">
                    <span>
                        <i class="icon-cloudCar2-radio <?php echo $data['useFor']==1 ? '' : 'icon-cloudCar2-radioactive'; ?> " data-val="0"></i>
                        <em>个人</em>
                    </span>
                <span>
                        <i class="icon-cloudCar2-radio <?php echo $data['useFor']==1 ? 'icon-cloudCar2-radioactive' : ''; ?> " data-val="1"></i>
                        <em>公司</em>
                    </span>
                <input type="hidden" name="useFor" value="<?php echo $data['useFor']==1 ? '1' :'0'; ?>">
            </div>
        </li>
    </ul>
</div>
<div class="commom-submit car-info-submit send-comfirm">
    <a class="btn-block btn-primary" href="javascript:;">保&nbsp;&nbsp;存</a>
</div>

<!-- 上拉菜单 省份选择 -->
<div class="m-actionsheet" id="actionSheet-province">
    <ul class="same-actionSheet-ul province-ul">
        <?php foreach ($pro as $k => $p): ?>
        <li class="<?php if($p['desc']=='湘'){ echo 'active';} ?>" data-id="<?= $p['id'] ?>"><?= $p['desc'] ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<!-- 上拉菜单 字母选择 -->
<div class="m-actionsheet" id="actionSheet-letter">
    <ul class="same-actionSheet-ul letter-ul">
    </ul>
</div>

<!-- 选择车型品牌弹窗 -->
<div class="commom-input-place pipai-popop-outer">
    <div class="letter-layer">
        <ul class="letter-layer-ul">
            <?php foreach ($anchors as $chor): ?>
                <li <?php if ($chor == 'A'): ?>class="active"<?php endif; ?>><a href="#<?= $chor ?>"><?= $chor ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php foreach ($brands as $char => $blist): ?>
        <dl class="carType-dl">
            <dt id="<?= $char ?>"><?= $char ?></dt>
            <?php foreach ($blist as $bname): ?>
                <dd data-id="<?= $bname['id'] ?>" data-icon="<?= $bname['id'] ?>" data-name="<?= $bname['name'] ?>"><?= $bname['name'] ?></dd>
            <?php endforeach; ?>
        </dl>
    <?php endforeach; ?>
</div>
<!-- 抽屉弹窗 车类 -->
<div class="menu-commom-layer menu-drawer-layer alxg-car-series">
</div>
<!-- 选择车型弹窗 -->
<div class="commom-input-place car-type-popup alxg-car-model">
</div>
<?php $this->beginBlock('script'); ?>
<script>
    /**
     * ActionDrawer Plugin actiondrawer 抽屉
     */
    !function (window) {
        "use strict";

        var doc = window.document,
            $doc = $(doc),
            $body = $(doc.body),
            $mask = $('<div class="mask-layer"></div>');

        function ActionDrawer(element, closeElement) {
            this.$element = $(element);
            this.closeElement = closeElement;
            this.toggleClass = 'menu-layer-toggle';
        }

        ActionDrawer.prototype.open = function () {

            YDUI.device.isIOS && $('.g-scrollview').addClass('g-fix-ios-overflow-scrolling-bug');

            var _this = this;
            $body.append($mask);

            // 点击遮罩层关闭窗口
            $mask.on('click.ydui.actiondrawer.mask', function () {
                _this.close();
            });

            // 第三方关闭窗口操作
            if (_this.closeElement) {
                $doc.on('click.ydui.actiondrawer', _this.closeElement, function () {
                    _this.close();
                });
            }

            _this.$element.addClass(_this.toggleClass).trigger('open.ydui.actiondrawer');
        };

        ActionDrawer.prototype.close = function () {
            var _this = this;

            YDUI.device.isIOS && $('.g-scrollview').removeClass('g-fix-ios-overflow-scrolling-bug');

            $mask.off('click.ydui.actiondrawer.mask').remove();
            _this.$element.removeClass(_this.toggleClass).trigger('close.ydui.actiondrawer');
            //$doc.off('click.ydui.actiondrawer', _this.closeElement);
        };

        function Plugin(option) {
            var args = Array.prototype.slice.call(arguments, 1);

            return this.each(function () {
                var $this = $(this),
                    actiondrawer = $this.data('ydui.actiondrawer');

                if (!actiondrawer) {
                    $this.data('ydui.actiondrawer', (actiondrawer = new ActionDrawer(this, option.closeElement)));
                    if (!option || typeof option == 'object') {
                        actiondrawer.open();
                    }
                }

                if (typeof option == 'string') {
                    actiondrawer[option] && actiondrawer[option].apply(actiondrawer, args);
                }
            });
        }

        $doc.on('click.ydui.actiondrawer.data-api', '[data-ydui-actiondrawer]', function (e) {
            e.preventDefault();

            var options = window.YDUI.util.parseOptions($(this).data('ydui-actiondrawer')),
                $target = $(options.target),
                option = $target.data('ydui.actiondrawer') ? 'open' : options;

            Plugin.call($target, option);
        });

        $.fn.actionDrawer = Plugin;

    }(window);
</script>
<script>
    var pid = $('.province-ul').find('li[class="active"]').data('id');
    $(".letter-ul").html('');
    $.post("<?php echo Url::to(['getcitydesc'])?>", {id: pid}, function (json) {
        if (json.status === 1) {
            $(".letter-ul").html(json.data);
        }
    }, 'json');

    
    //点击选择品牌
    $('.car-info-ul>li.pinpai').on('click', function () {
        $('.pipai-popop-outer').show();
    });
    //选择车品牌字母开头
    $('.letter-layer-ul>li').on('click', function (e) {
        e.stopPropagation();
        $('.letter-layer-ul>li').removeClass('active');
        $(this).addClass('active');
    });
    //品牌选中出车系
    $('.pipai-popop-outer .carType-dl dd').click(function () {
        var bid = $(this).data('id');
        var bicon = $(this).data('icon');
        var bname = $(this).data('name');
        YDUI.dialog.loading.open('请稍候');
        $(".alxg-car-series").html('');
        $.post("<?php echo Url::to(['getcarseries'])?>", {brandId: bid}, function (json) {
            YDUI.dialog.loading.close();
            if (json.status === 1) {
                $(".alxg-car-series").html(json.data);
                $('input[name="card_brand"]').val(bname);
                $('input[name="car_brand_id"]').val(bid);
                $('input[name="car_logo"]').val(bicon);
                $('.menu-commom-layer').actionDrawer('open');
            }
        }, 'json');
    });

    //抽屉菜单选中
    $(document).on('click', '.drawer-data-ul>li', function (e) {
        e.stopPropagation();
        $('input[name="car_series_name"]').val($(this).data('name'));
        $('input[name="car_series_id"]').val($(this).data('id'));
        $('#brand').html($('input[name="card_brand"]').val()+$('input[name="car_series_name"]').val());

        $('input[name="car_model_small_id"]').val(0);
        $('input[name="car_model_small_name"]').val('');
        $('input[name="car_model_name"]').val('');
        $('input[name="car_model_id"]').val(0);
        $('input[name="car_model_small_fullname"]').val('');
        $('#bmodel').html('请选择');

        $('.menu-commom-layer').actionDrawer('close');
        $('.commom-input-place').hide();
    });

    //点击选择车型
    $('.car-info-ul>li.car-type').on('click', function () {
        var seriesId = $('input[name="car_series_id"]').val();
        if (seriesId != 0) {
            YDUI.dialog.loading.open('请稍候');
            $(".alxg-car-model").html('');
            $.post("<?php echo Url::to(['getcarmodel'])?>", {seriesId: seriesId}, function (json) {
                YDUI.dialog.loading.close();
                if (json.status === 1) {
                    $(".alxg-car-model").html(json.data);
                    $('.car-type-popup').show();
                }
            }, 'json');
        } else {
            YDUI.dialog.toast('请先选择品牌', 'none', 1500);
            return false;
        }
    });

    //车型选中
    $(document).on('click', '.car-series-ul>li', function () {
        $('input[name="car_model_small_id"]').val($(this).data('id'));
        $('input[name="car_model_small_name"]').val($(this).data('name'));
        $('input[name="car_model_name"]').val($(this).data('name'));
        $('input[name="car_model_id"]').val($(this).data('id'));
        $('input[name="car_model_small_fullname"]').val($('input[name="card_brand"]').val() + $('input[name="car_model_small_name"]').val());
        $('#bmodel').html($('input[name="car_model_small_name"]').val());
        $('.car-type-popup').hide();
    })

    //车牌类型按钮切换
    $('.chepai-radio>span').on('click', function (e) {
        $('.chepai-radio>span>i').removeClass('icon-cloudCar2-radioactive');
        $('input[name="card_type"]').val($(this).find('i').data('id'));
        $(this).find('i').addClass('icon-cloudCar2-radioactive');
    });


    //上拉省份简称选择
    $('.province-ul>li').on('click', function () {
        $('.province-ul>li').removeClass('active');
        $(this).addClass('active');
        $('input[name="card_province"]').val($(this).text());
        $('.select-province>em').text($(this).text());
        var id = $(this).data('id');
        $(".letter-ul").html('');
        $.post("<?php echo Url::to(['getcitydesc'])?>", {id: id}, function (json) {
            if (json.status === 1) {
                $(".letter-ul").html(json.data);
                $('.select-Letter>em').text('A');
            }
        }, 'json');

    });
    $('#actionSheet-province').on('click', function () {
        $(this).actionSheet('close');
    });

    //上拉字母选择
    $(document).on('touchstart','.letter-ul>li',function () {
        $('.letter-ul>li').removeClass('active');
        $(this).addClass('active');
        $('input[name="card_char"]').val($(this).text());
        $('.select-Letter>em').text($(this).text());
    })

    $('#actionSheet-letter').on('click', function () {
        $(this).actionSheet('close');
    });

    //按钮切换
    $('.select-radio>span').on('click', function () {
        $(this).parent('.select-radio').find('i').removeClass('icon-cloudCar2-radioactive');
        $(this).find('i').addClass('icon-cloudCar2-radioactive');
        $(this).parent('.select-radio').find('input').val($(this).find('i').data('val'));
    });

    var isSubmit = false;
    $(".send-comfirm").on('click', function () {
        if (isSubmit) return false;
        var card_brand = $("input[name=card_brand]").val();//车辆品牌
        var car_series = $("input[name=car_series_name]").val();//车辆品牌
        if (card_brand=='' || car_series=='') {
            YDUI.dialog.toast('请选择品牌车系', 'none', 1500);
            return false;
        }
        var cmsfname = $("input[name=car_model_small_name]").val();
        if (!cmsfname.length) {
            YDUI.dialog.toast('请选择车型', 'none', 1500);
            return false;
        }
        var card_no = $("input[name=card_no]").val();//车牌
        if (!card_no.length || card_no.length > 5) {
            YDUI.dialog.toast('请输入正确的车牌号码', 'none', 1500);
            return false;
        }

        if ($('select[name="inspectionVehicleType"]').val()=='0') {
            YDUI.dialog.toast('请选择车辆类型', 'none', 1500);
            return false;
        }
        if ($('input[name="rg_time"]').val()=='') {
            YDUI.dialog.toast('请选择注册时间', 'none', 1500);
            return false;
        }
        // if ($('input[name="carSeats"]').val()=='0') {
        //     YDUI.dialog.toast('请填写座位数', 'none', 1500);
        //     return false;
        // }

        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['bindcar'])?>", {
            card_brand: $('input[name="card_brand"]').val(),
            card_type: $('input[name="car_type"]').val(),
            card_province: $('input[name="card_province"]').val(),//车牌号
            card_char: $('input[name="card_char"]').val(),//车牌号
            card_no: $('input[name="card_no"]').val(),//车牌号
            id: $('input[name="id"]').val(),
            car_model_small_fullname: $('input[name="car_model_small_fullname"]').val(),
            car_model_small_id: $('input[name="car_model_small_id"]').val(),//车型ID
            car_model_small_name: $('input[name="car_model_small_name"]').val(),
            car_logo: $('input[name="car_logo"]').val(),
            car_model_id: $('input[name="car_model_id"]').val(),
            car_model_name: $('input[name="car_model_name"]').val(),
            car_brand_id: $('input[name="car_brand_id"]').val(),//品牌ID
            car_series_name: $('input[name="car_series_name"]').val(),
            carId: $('input[name="carId"]').val(),//车辆ID
            rg_time: $('input[name="rg_time"]').val(),//注册时间
            carSeats: $('input[name="carSeats"]').val(),//	座位数
            useFor: $('input[name="useFor"]').val(),//用途
            inspectionVehicleType: $('select[name="inspectionVehicleType"]').val(),//车辆类型
            operationType: $('input[name="operationType"]').val(),//运营车辆
            isAccidentCar: $('input[name="isAccidentCar"]').val(),//伤亡事故车
            car_series_id: $('input[name="car_series_id"]').val()//车系ID
        }, function (json) {
            isSubmit = false;
            YDUI.dialog.loading.close();
            if (json.status == 1) {
                YDUI.dialog.toast('绑定成功', 'none', 1500, function () {
                    window.location.href = json.url;
                });
            } else {
                YDUI.dialog.alert(json.msg);
            }
        }, 'json');
    });
</script>
<?php $this->endBlock('script'); ?>
