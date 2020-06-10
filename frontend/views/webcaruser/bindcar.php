<?php

use yii\helpers\Url;

?>
<div class="select-plateNumber">
    <input type="hidden" name="id" value="<?php echo $data ? $data['id'] : 0; ?>">
    <ul class="same-cell-ul">
        <li>
            <a class="link-selectCar" href="javascript:;" style="border-bottom:1px solid #eee;">
                <div class="div-left"><i>选择品牌</i></div>
                <div class="div-right"><span><?php echo $data ? $data['card_brand'] : '请选择'; ?></span><i
                            class="iconfont icon-car-jiantou"></i></div>
                <input type="hidden" name="card_brand" value="<?php echo $data ? $data['card_brand'] : ''; ?>">
                <input type="hidden" name="brand_id" value="<?php echo $data ? $data['car_brand_id'] : ''; ?>">
            </a>
        </li>
    </ul>
    <ul class="same-cell-ul">
        <li>
            <a class="link-selectCarModel" href="javascript:;">
                <div class="div-left"><i>选择车型</i></div>
                <div class="div-right"><span><?php echo $data ? $data['car_model_small_fullname'] : '请选择'; ?></span><i
                            class="iconfont icon-car-jiantou"></i></div>
                <input type="hidden" name="car_model_small_fullname"
                       value="<?php echo $data ? $data['car_model_small_fullname'] : ''; ?>">
                <input type="hidden" name="car_model_name" value="<?php echo $data ? $data['car_model_name'] : ''; ?>">
                <input type="hidden" name="car_model_id" value="<?php echo $data ? $data['car_model_id'] : ''; ?>">
                <input type="hidden" name="car_model_small_id"
                       value="<?php echo $data ? $data['car_model_small_id'] : ''; ?>">
                <input type="hidden" name="car_model_small_name"
                       value="<?php echo $data ? $data['car_model_small_name'] : ''; ?>">
                <input type="hidden" name="car_logo" value="<?php echo $data ? $data['car_logo'] : ''; ?>">
            </a>
        </li>
    </ul>
    <div class="plateNumber-type">
        <div class="plateNumber-title">车牌类型</div>
        <div class="plateNumber-radio">
            <?php foreach ($car_types as $k => $ct): ?>
                <input type="radio" name="card_type" value="<?= $k ?>" <?php if ($data && $data['card_type'] == $k) {
                    echo 'checked';
                } elseif ($k == 1) {
                    echo "checked";
                } ?> /><?= $ct ?>
            <?php endforeach; ?>
        </div>
        <div class="plateNumber-input">
            <span>车牌号码</span>
            <span class="select-province"
                  data-ydui-actionsheet="{target:'#actionSheet-province',closeElement:'#cancel'}">
                    <em><?php echo $data ? $data['card_province'] : '湘'; ?></em><i
                        class="iconfont icon-car-zelvxuanzefeiyongdaosanjiaoxingfandui"></i>
                </span>
            <input type="hidden" name="card_province" value="<?php echo $data ? $data['card_province'] : '湘'; ?>">
            <span class="select-Letter" data-ydui-actionsheet="{target:'#actionSheet-letter',closeElement:'#cancel'}">
                    <em><?php echo $data ? $data['card_char'] : 'A'; ?></em><i
                        class="iconfont icon-car-zelvxuanzefeiyongdaosanjiaoxingfandui"></i>
                </span>
            <input type="hidden" name="card_char" value="<?php echo $data ? $data['card_char'] : 'A'; ?>">
            <input type="text" name="card_no" placeholder="请输入车牌后5位"
                   value="<?php echo $data ? $data['card_no'] : ''; ?>">
        </div>
    </div>
</div>
<div class="send-comfirm">
    <button type="button" class="btn-block btn-primary">保存</button>
</div>
<!-- 上拉菜单 省份选择 -->
<div class="m-actionsheet" id="actionSheet-province">
    <ul class="same-actionSheet-ul province-ul">
        <li>京</li>
        <li>沪</li>
        <li>浙</li>
        <li>苏</li>
        <li>粤</li>
        <li>鲁</li>
        <li>晋</li>
        <li>冀</li>
        <li>豫</li>
        <li>川</li>
        <li>渝</li>
        <li>辽</li>
        <li>吉</li>
        <li>黑</li>
        <li>皖</li>
        <li>鄂</li>
        <li>湘</li>
        <li>赣</li>
        <li>闽</li>
        <li>陕</li>
        <li>甘</li>
        <li>宁</li>
        <li>蒙</li>
        <li>津</li>
        <li>贵</li>
        <li>云</li>
        <li>桂</li>
        <li>琼</li>
        <li>青</li>
        <li>新</li>
        <li>藏</li>
    </ul>
</div>
<!-- 上拉菜单 字母选择 -->
<div class="m-actionsheet" id="actionSheet-letter">
    <ul class="same-actionSheet-ul letter-ul">
        <li>A</li>
        <li>B</li>
        <li>C</li>
        <li>D</li>
        <li>E</li>
        <li>F</li>
        <li>G</li>
        <li>H</li>
        <li>I</li>
        <li>J</li>
        <li>k</li>
        <li>L</li>
        <li>M</li>
        <li>N</li>
        <li>O</li>
        <li>P</li>
        <li>Q</li>
        <li>R</li>
        <li>S</li>
        <li>T</li>
        <li>U</li>
        <li>V</li>
        <li>W</li>
        <li>X</li>
        <li>Y</li>
        <li>Z</li>
    </ul>
</div>
<!-- 选择品牌弹窗 -->
<div class="selectCar-popop-outer alxg-car-brand">
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
                <dd data-id="<?= $bname['id'] ?>"><?= $bname['name'] ?></dd>
            <?php endforeach; ?>
        </dl>
    <?php endforeach; ?>
</div>

<!-- 选择车型弹窗 -->
<div class="selectCar-popop-outer alxg-car-model">
</div>
<?php $this->beginBlock('script'); ?>
<script>
    var getCarModel = function () {
        var id = $("input[name=brand_id]").val();
        $.post("<?php echo Url::to(['getcarmodel'])?>", {id: id}, function (json) {
            if (json.status === 1) {
                $(".alxg-car-model").html(json.data);
            }
        }, 'json');
    };
    <?php if($data):?>
    getCarModel();
    <?php endif;?>
    //上拉省份简称选择
    $('.province-ul>li').on('touchstart', function () {
        $('.province-ul>li').removeClass('active');
        $(this).addClass('active');
        $('.select-province>em').text($(this).text());
        $("input[name=card_province]").val($(this).text());
    });
    $('#actionSheet-province').on('click', function () {
        $(this).actionSheet('close');
    });
    //上拉字母选择
    $('.letter-ul>li').on('touchstart', function () {
        $('.letter-ul>li').removeClass('active');
        $(this).addClass('active');
        $('.select-Letter>em').text($(this).text());
        $("input[name=card_char]").val($(this).text());
    });
    $('#actionSheet-letter').on('click', function () {
        $(this).actionSheet('close');
    });
    //选择车类型字母开头
    $('.letter-layer-ul>li').on('touchstart', function (e) {
        e.stopPropagation();
        $('.letter-layer-ul>li').removeClass('active');
        $(this).addClass('active');
    });
    //选择品牌内容弹窗显示
    $('.link-selectCar').on('click', function (e) {
        e.stopPropagation();
        $('.alxg-car-brand').show();
    });
    //选择车型内容弹窗显示
    $('.link-selectCarModel').on('click', function (e) {
        e.stopPropagation();
        var brand = $("input[name=brand_id]").val();
        if(!brand.length){
            YDUI.dialog.toast('请选择品牌', 'none', 1500);
            return false;
        }
        $('.alxg-car-model').show();
    });
    $('.alxg-car-brand>.carType-dl>dd').on('click', function (e) {
        e.stopPropagation();
        $('.link-selectCar>.div-right>span').html($(this).text());
        $("input[name=card_brand]").val($(this).text());
        $("input[name=brand_id]").val($(this).data('id'));
        getCarModel();
        $('.alxg-car-brand').hide();
    });
    $('.alxg-car-model').on('click', '.carType-dl>dd', function (e) {
        e.stopPropagation();
        $('.link-selectCarModel>.div-right>span').html($(this).text());
        $("input[name=car_model_small_fullname]").val($(this).text());
        $("input[name=car_model_small_id]").val($(this).data('id'));
        $("input[name=car_model_small_name]").val($(this).data('name'));
        $("input[name=car_logo]").val($(this).data('logo'));
        $("input[name=car_model_id]").val($(this).siblings('dt').data('id'));
        $("input[name=car_model_name]").val($(this).siblings('dt').text());
        $('.alxg-car-model').hide();
    });
    //获得车型
    var isSubmit = false;
    $(".send-comfirm").on('click', function () {
        if (isSubmit) return false;
        var card_brand = $("input[name=card_brand]").val();//车辆品牌
        if (!card_brand.length) {
            YDUI.dialog.toast('请选择品牌', 'none', 1500);
            return false;
        }
        var cmsfname = $("input[name=car_model_small_fullname]").val();
        if (!cmsfname.length) {
            YDUI.dialog.toast('请选择车型', 'none', 1500);
            return false;
        }
        var card_no = $("input[name=card_no]").val();//车牌
        if (!card_no.length || card_no.length > 5) {
            YDUI.dialog.toast('请输入车牌号码', 'none', 1500);
            return false;
        }
        var card_type = $("input:radio[name=card_type]:checked").val();//车牌类型
        var card_province = $("input[name=card_province]").val();//省份简称
        var card_char = $("input[name=card_char]").val();//大写
        var id = $("input[name=id]").val();

        var cmsid = $("input[name=car_model_small_id]").val();
        var cmsname = $("input[name=car_model_small_name]").val();
        var car_logo = $("input[name=car_logo]").val();
        var cmid = $("input[name=car_model_id]").val();
        var cmname = $("input[name=car_model_name]").val();
        var cbid = $('input[name=brand_id]').val();
        isSubmit = true;
        YDUI.dialog.loading.open('正在提交');
        $.post("<?php echo Url::to(['bindcar'])?>", {
            card_brand: card_brand,
            card_type: card_type,
            card_province: card_province,
            card_char: card_char,
            card_no: card_no,
            id: id,
            car_model_small_fullname: cmsfname,
            car_model_small_id: cmsid,
            car_model_small_name: cmsname,
            car_logo: car_logo,
            car_model_id: cmid,
            car_model_name: cmname,
            car_brand_id: cbid
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
