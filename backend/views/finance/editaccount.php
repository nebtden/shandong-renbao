<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="page-header am-fl am-cf">
    <h4>
        用户账户编辑
        <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal" method="post" action="<?php echo Url::to(['finance/editaccount']) ?>">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">账户类型</label>
            <div class="col-sm-3">
                <select name="account_type" class="form-control">
                    <?php foreach ($type as $key => $t): ?>
                        <option value="<?= $key ?>" <?php if ($data['account_type'] == $key) echo 'selected'; ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">账号</label>
            <div class="col-sm-3">
                <input type="text" required name="payee_account" class="form-control" value="<?php echo $data['payee_account']; ?>"
                       placeholder="请输入用户的账号">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">开户名</label>
            <div class="col-sm-3">
                <input type="text" required name="realname" class="form-control"
                       value="<?php echo $data['realname']; ?>" placeholder="请输入开户名">
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-3">
                <select name="status" class="form-control">
                    <option value="1" <?php if ($data && $data['status'] == 1) echo 'selected'; ?>>正常</option>
                    <option value="0" <?php if ($data && $data['status'] == 0) echo 'selected'; ?>>不可用</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
        <?php if (isset($data['id'])): ?>
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <?php endif; ?>
    </form>
</div>
<script>
    $('form').submit(function () {
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.post(url,data,function(json){
            alert(json.msg);
            if(json.status === 1){
                window.location.href = json.url;
            }
        },'json');
        return false;
    });
</script>