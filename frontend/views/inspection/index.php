<?php

use yii\helpers\Url;

?>
<div class="year-testing-header commom-img">
    <img src="/frontend/web/cloudcarv2/images/daibannianjian-bg.png">
</div>
<div class="year-testing-wrapper">
    <ul class="year-testing-ul">
        <li>
            <i>年检车辆选择</i>
            <div class="right">
                <select name="carId">
                    <option value="0">请选择年检车辆</option>
                    <?php if ($carinfo['status'] == SUCCESS_STATUS): ?>
                        <?php unset($carinfo['status']); ?>
                        <?php foreach ($carinfo as $v): ?>
                            <option value="<?= $v['carId'] ?>"><?= $v['carNum'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
        <li>
            <i>车辆类型</i>
            <div class="right">
                <select name="inspectionVehicleType" id="inspectionVehicleType" disabled>
                    <option value="0"></option>
                    <option value="1">6座及以下车辆</option>
                    <option value="10">7座及以上或面包车</option>
                </select>
                <!--                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>-->
            </div>
        </li>
        <li>
            <i>车辆注册日期</i>
            <div class="right">
                <input name="registerDate" id="registerDate" readonly>
                <!--                <input type="date" name="registerDate" id="date" placeholder="年/月/日">-->
                <!--                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>-->
            </div>
        </li>
        <li>
            <i>是否运营车辆</i>
            <div class="select-radio" id="operationType">
                <span data-type="1">
                    <i class="icon-cloudCar2-radio "></i>
                    <em>是</em>
                </span>
                <span data-type="0">
                    <i class="icon-cloudCar2-radio "></i>
                    <em>否</em>
                </span>
                <input type="hidden" name="operationType">
            </div>
        </li>
        <li>
            <i>上次年检至今造成 过人员伤亡事故</i>
            <div class="select-radio" id="isAccidentCar">
                <span data-type="1">
                    <i class="icon-cloudCar2-radio "></i>
                    <em>是</em>
                </span>
                <span data-type="0">
                    <i class="icon-cloudCar2-radio "></i>
                    <em>否</em>
                </span>
                <input type="hidden" name="isAccidentCar">
            </div>
        </li>
        <li>
            <i>车辆所有人</i>
            <div class="select-radio" id="useFor">
                <span data-type="0">
                    <i class="icon-cloudCar2-radio "></i>
                    <em>个人</em>
                </span>
                <span data-type="1">
                    <i class="icon-cloudCar2-radio "></i>
                    <em>公司</em>
                </span>
                <input type="hidden" name="useFor">
            </div>
        </li>
    </ul>
</div>
<div class="commom-submit checktype">
    <button class="btn-block btn-primary">判断年检类型</button>
</div>
<div class="commom-popup-outside  small-popup-outside" style="display:none;" >
    <div class="commom-popup">
        <div class="title title-nobg">
<!--            <i class="icon-error"></i>-->
        </div>
        <div class="content">
            <div class="up">
                您需要完善车辆信息
            </div>
            <div class="commom-submit need-submit">
                <a class="btn-block btn-primary small-popup-btn" href="<?php echo Url::to(['caruser/carlist'])?>" >去完善</a>
            </div>
        </div>
    </div>
</div>
<?php $this->beginBlock('script'); ?>

<script>
    var isall = <?= $isAll ?>;
    if(isall!=1){
        $('.small-popup-outside').show();
    }
    var carInfo = <?= $carjson ?>;
    if (carInfo['status'] == <?php echo ERROR_STATUS;?>) {
        YDUI.dialog.toast(carInfo['msg'], 1500);
    }
    $('select[name="carId"]').val(0);
    $('#inspectionVehicleType').val(0);
    $('#registerDate').val('');
    $('.select-radio').find('i').removeClass('icon-cloudCar2-radioactive');
    //车辆切换
    $('select[name="carId"]').change(function () {
        var carId = $(this).val();
        $('.select-radio').find('i').removeClass('icon-cloudCar2-radioactive');
        if (carId !='0') {
            var oneCar = carInfo[carId];
            $('#inspectionVehicleType').val(oneCar['inspectionVehicleType']);
            $('#registerDate').val(oneCar['registerDate']);
            $('#operationType>span[data-type=' + oneCar["operationType"] + ']').find('i').addClass('icon-cloudCar2-radioactive');
            $('#isAccidentCar>span[data-type=' + oneCar["isAccidentCar"] + ']').find('i').addClass('icon-cloudCar2-radioactive');
            $('#useFor>span[data-type=' + oneCar["useFor"] + ']').find('i').addClass('icon-cloudCar2-radioactive');
            $('input[name="operationType"]').val(oneCar["operationType"]);
            $('input[name="isAccidentCar"]').val(oneCar["isAccidentCar"]);
            $('input[name="useFor"]').val(oneCar["useFor"]);
        }
    });
    //解决placeholder在微信端显示
    $("#date").on("input", function () {
        if ($(this).val().length > 0) {
            $(this).addClass("full");
        }
        else {
            $(this).removeClass("full");
        }
    });
    $('.checktype').on('click', function () {
        var carId = $('select[name="carId"]').val();
        if (carId == '0') {
            YDUI.dialog.toast('请先选择年检车辆', 1500);
            return false;
        }
        window.location.href = "<?php echo Url::to(['checktype'])?>" + "?carId=" + carId;
    })
</script>
<?php $this->endBlock('script'); ?>
