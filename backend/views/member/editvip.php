<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="page-header am-fl am-cf">
    <h4>
        VIP管理 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['member/editvip']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户id</label>
            <div class="col-sm-3">
                <input type="text" required name="uid" class="form-control" value="<?php echo $data['uid'];?>"  placeholder="请输入用户id">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户真实姓名</label>
            <div class="col-sm-3">
                <input type="text" required name="realname" class="form-control" value="<?php echo $data['realname'];?>"  placeholder="请输入用户真实姓名">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户手机</label>
            <div class="col-sm-3">
                <input type="text" required name="mobile" class="form-control" value="<?php echo $data['mobile'];?>"  placeholder="请输入用户手机号">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户邮箱</label>
            <div class="col-sm-3">
                <input type="email" name="email" class="form-control" value="<?php echo $data['email'];?>"  placeholder="请输入用户邮箱">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">所属公司</label>
            <div class="col-sm-3">
                <input type="text" name="company" class="form-control" value="<?php echo $data['company'];?>"  placeholder="请输入公司名称">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">职务</label>
            <div class="col-sm-3">
                <input type="text" name="duties" class="form-control" value="<?php echo $data['duties'];?>"  placeholder="请输入用户职务">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">是否设置为VIP</label>
            <div class="col-sm-3">
                <select name="is_vip" class="form-control">
                    <?php foreach ($level as $key => $val):?>
                    <option value="<?=$key?>" <?php if($data['is_vip']==$key) echo 'selected'; ?>>设置为<?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-3">
                <select name="status" class="form-control">
                    <?php foreach ($status as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['status']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">上级id</label>
            <div class="col-sm-3">
                <input type="number" min="0" max="99999999999" name="pid" class="form-control" value="<?php echo $data['pid'];?>"  placeholder="上级id">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
        <?php if(isset($data['id'])): ?>
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <?php endif; ?>
    </form>
</div>
<script>
    $('form').submit(function(){
        var error=0;
        $('input[type="text"]').each(function(){
            if($(this).val().length==0) {
                $(this).parent().parent().addClass('has-error');
                error = 1;
            }
            else{
                $(this).parent().parent().removeClass('has-error');
            }
        });
        if(error>0) return false;
        else  return true;
    });
</script>