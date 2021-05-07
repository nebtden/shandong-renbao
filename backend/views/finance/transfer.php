<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>
<style>
    .alxg-info {
        padding-top: 7px;
        display: inline-block;
    }
</style>
<div class="page-header am-fl am-cf">
    <h4>
        转账
        <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal" method="post" action="<?php echo Url::to(['finance/transfer']) ?>">
        <input type="hidden" name="<?= $token['name'] ?>" value="<?= $token['value'] ?>">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">账户类型</label>
            <div class="col-sm-3">
                <span class="alxg-info"><?= $type[(int)$data['account_type']] ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">账号</label>
            <div class="col-sm-3">
                <span class="alxg-info"><?= $data['payee_account'] ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">开户名</label>
            <div class="col-sm-3">
                <span class="alxg-info"><?= $data['realname'] ?></span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">转账金额(至少0.1元)</label>
            <div class="col-sm-3">
                <input type="text" required name="amount" class="form-control" value=""
                       placeholder="请输入转账金额">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">转账人(如云车驾到)</label>
            <div class="col-sm-3">
                <input type="text" name="payer_show_name" class="form-control" value="云车驾到"
                       placeholder="云车驾到">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">转账备注(收款人可见)</label>
            <div class="col-sm-3">
                <input type="text" name="remark" class="form-control" value=""
                       placeholder="备注，可不填">
            </div>
        </div>
        <!--        <div class="form-group">-->
        <!--            <label for="inputPassword3" class="col-sm-2 control-label">经办人</label>-->
        <!--            <div class="col-sm-3">-->
        <!--                <input type="text" required name="agent_name" class="form-control" value=""-->
        <!--                       placeholder="请输入转账操作人的姓名">-->
        <!--            </div>-->
        <!--        </div>-->
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">确认转账</button>
            </div>
        </div>
        <?php if (isset($data['id'])): ?>
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <?php endif; ?>
    </form>
</div>
<script>
    var payee_account = "<?= $data['payee_account']?>";
    var isSubmit = false;
    $('form').submit(function () {
        if (isSubmit) return false;
        var url = $(this).attr('action');
        var amount = $("input[name=amount]").val();
        if (isNaN(amount)) {
            alert('转账金额输入错误');
            return false;
        }
        amount = new Number(amount);
        amount = amount.toFixed(2);
        if (amount < 0.1) {
            alert('转账金额至少为0.1元');
            return false;
        }
        $("input[name=amount]").val(amount);
        var r = confirm('是否确认向' + payee_account + '转账' + amount + '元？');
        if (!r) return false;
        isSubmit = true;
        var data = $(this).serialize();
        $.post(url, data, function (json) {
            isSubmit = false;
            alert(json.msg);
            if (json.status === 1) {
                window.location.href = json.url;
            }
        }, 'json');
        return false;
    });
</script>