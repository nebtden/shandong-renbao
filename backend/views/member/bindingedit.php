<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 上午 9:42
 */
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        绑定车牌管理 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['member/bindingedit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">用户id</label>
            <div class="col-sm-3">
                <input type="text" required name="uid" class="form-control" value="<?php echo $data['uid'];?>"  placeholder="请输入用户id">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">车牌类型</label>
            <div class="col-sm-3">
                <select name="card_type" class="form-control">

                    <?php foreach ($cartype as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['card_type']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">所属省份简称</label>
            <div class="col-sm-3">
                <select name="card_province" class="form-control">

                    <?php foreach ($province as $key => $val):?>
                        <option value="<?=$val?>" <?php if($data['card_province']==$val) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">车牌单字母</label>
            <div class="col-sm-3">
                <select name="card_char" class="form-control">

                    <?php foreach ($zimu as $key => $val):?>
                        <option value="<?=$val?>" <?php if($data['card_char']==$val) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">车牌号码</label>
            <div class="col-sm-3">
                <input type="text" name="card_no" class="form-control" value="<?php echo $data['card_no'];?>"  placeholder="车牌号码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">汽车品牌</label>
            <div class="col-sm-3">
                <select name="card_brand" class="form-control">
                    <?php foreach ($car_brand as $key => $val):?>
                        <option value="<?=$val['name']?>" <?php if($data['card_brand']==$val['name']) echo 'selected'; ?>><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-3">
                <select name="status" class="form-control">
                    <option value="1" <?php if($data['status']==1) echo 'selected'; ?>>正常</option>
                    <option value="0" <?php if($data['status']== 0) echo 'selected';  ?>>用户删除</option>
                    <option value="-1" <?php if($data['status']== -1) echo 'selected';  ?>>管理员删除</option>
                </select>
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
