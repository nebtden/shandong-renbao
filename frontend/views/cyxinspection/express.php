<?php
use yii\helpers\Url;
?>
<div class="commom-regulate">
    <ul class="commom-address-ul">
        <li class="wuliu-company">
            <i>物流公司</i>
            <div class="right">
                <select name="express">
                    <option value="">请选择物流公司</option>
                    <?php foreach ($expressinfo as $v): ?>
                        <option value="<?= $v['no'] ?>"><?= $v['com'] ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="icon icon-cloudCar2-sanjiaoxing2"></span>
            </div>
        </li>
        <li>
            <i>物流单号</i>
            <input type="text" name="expressno"  placeholder="请填写物流单号">
        </li>
    </ul>
    <input type="hidden" name="id" value="<?= $id ?>">
</div>
<div class="danhao-tip">注：请谨慎填写，填写后将无法修改</div>
<div class="commom-submit">
    <button type="button" class="btn-block">保&nbsp;存</button>
</div>
<?php $this->beginBlock('script'); ?>
<script>
    var isSubmit=false;
    $('.commom-submit').click(function () {
        if(isSubmit){
            return false;
        }
        isSubmit = true;
        var express = $('select[name="express"]').find("option:selected").text();
        var expresscom = $('select[name="express"]').val();
        var expressno = $('input[name="expressno"]').val();
        var id = $('input[name="id"]').val();
        if(express==""||expressno==""){
            YDUI.dialog.toast('请填写物流信息', 1000);
            return false;
        }
        YDUI.dialog.confirm('提示', '填写后将无法修改', function () {
            $.post("<?php echo Url::to(['express'])?>", {
                express: express,
                expresscom: expresscom,
                expressno: expressno,
                id: id,
            }, function (json) {
                isSubmit = false;
                if (json.status == 1) {
                    window.history.go(-1);
                } else {
                    YDUI.dialog.toast(json.msg,'none',1500);
                }
            }, 'json');
        });
    });
</script>
<?php $this->endBlock('script'); ?>
