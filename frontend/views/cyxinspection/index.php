<?php

use yii\helpers\Url;

?>
<div class="year-testing-header commom-img">
    <img src="/frontend/web/cloudcarv2/img/h5-banner.jpg">
</div>
<div class="year-testing-wrapper">
    <ul class="year-testing-ul car-testing">
        <li>
            <i>车辆选择</i>
            <div class="right">
                <select name="carId">
                    <option value="0">请选择年检车辆</option>
                        <?php foreach ($carinfo as $v): ?>
                            <option value="<?= $v['id'] ?>"><?= $v['card_province'].$v['card_char'].$v['card_no'] ?></option>
                        <?php endforeach; ?>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
            <a class="right addCar" href="<?php echo Url::to(['caruser/bindcar'])?>">
                添加车辆
            </a>
        </li>
        <li>
            <i>年检有效期</i>
            <div class="right">
                <select name="checkDate">
                    <option value="0">请选择</option>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
    </ul>
    <div class="deal-process">
        <p>代办流程</p>
        <ul class="deal-process-item">
            <li>
                <div class="process-img">
                    <img src="/frontend/web/cloudcarv2/img/h5-car.png">
                </div>
                <p>选择车辆<br />判断年检类型</p>
            </li>
            <li>
                <div class="process-img">
                    <img src="/frontend/web/cloudcarv2/img/h5-order.png">
                </div>
                <p>提交订单<br />上传材料</p>
            </li>
            <li>
                <div class="process-img">
                    <img src="/frontend/web/cloudcarv2/img/h5-deal.png">
                </div>
                <p>代办<br />年检</p>
            </li>
            <li>
                <div class="process-img">
                    <img src="/frontend/web/cloudcarv2/img/h5-finish.png">
                </div>
                <p>材料寄回<br />服务完成</p>
            </li>
        </ul>
    </div>
</div>
<div class="commom-submit checktype">
    <button class="btn-block btn-primary">判断年检类型</button>
</div>
<?php $this->beginBlock('script'); ?>

<script>

    $('select[name="carId"]').val(0);
    $('select[name="carId"]').on('change',function () {
        var carId = $('select[name="carId"]').val();
        var str = '<option value="0">请选择</option>';
        $('select[name="checkDate"]').empty().append(str);
        if(carId!='0'){
            YDUI.dialog.loading.open('加载中...');
            $.post("<?php echo Url::to(['checklist'])?>", {
                carId:carId,
            }, function (res) {
                YDUI.dialog.loading.close();
                if(res){
                    str+=res;
                    $('select[name="checkDate"]').empty().append(str);
                }else{
                    YDUI.dialog.toast('请求出错', 800);
                }
            }, 'html');
        }
    });

    $('.checktype').on('click', function () {
        var carId = $('select[name="carId"]').val();
        var checkDate = $('select[name="checkDate"]').val();
        if (carId == '0') {
            YDUI.dialog.toast('请先选择年检车辆', 1500);
            return false;
        }
        if (checkDate == '0') {
            YDUI.dialog.toast('请先选择年检有效期', 1500);
            return false;
        }
        window.location.href = "<?php echo Url::to(['checktype'])?>" + "?carId=" + carId+"&checkDate="+checkDate;
    })
</script>
<?php $this->endBlock('script'); ?>
