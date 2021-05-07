<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/19 0019
 * Time: 上午 9:52
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        优惠券编辑 <small>&nbsp;&nbsp;/表单信息</small>
    </h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['coupon/couponedit']) ?>" >


        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">优惠券的类型</label>
            <div class="col-sm-3">
                <select id="coupon_type" name="coupon_type"  placeholder="优惠券的类型"  class="form-control" onchange="changescene(this)">
                    <?php foreach ($coupon_type as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['coupon_type']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">券名</label>
            <div class="col-sm-3">
                <input type="text"  name="name" value="<?= $data['name'] ?>" required class="form-control" placeholder="请输入券名" >
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">优惠券面额</label>
            <div class="col-sm-3">
                <input type="number" min="1" max="9999999999"  required name="amount" value="<?= $data['amount'] ?>" class="form-control" placeholder="优惠券面额">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">使用限制</label>
            <div class="col-sm-3">
                <input type="number" min="0" max="999999999"  name="use_limit" value="<?= $data['use_limit'] ?>" class="form-control" placeholder="使用限制,0为无限制">
            </div>
        </div>

        <div class="form-group uprescue">
            <label for="inputPassword3" class="col-sm-2 control-label">券为道路救援时指定类型</label>
            <div class="col-sm-3">
                <select  name="use_scene"  placeholder="类型"  class="form-control" >
                    <?php foreach ($coupon_faulttype as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['use_scene']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">激活后多少天有效0表示无限制</label>
            <div class="col-sm-3">
                <input type="number" min="0" max="36500"  required name="expire_days" value="<?= $data['expire_days'] ?>" class="form-control" placeholder="使用期限,0为无限制">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">过期时间</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" name="use_limit_time" value="<?php echo  !empty($data['use_limit_time'])?date("Y-m-d H:i:s",$data['use_limit_time']):''; ?>" id="use_limit_time"  onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="不填为永久有效">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">优惠券号码</label>
            <div class="col-sm-3">
                <input type="text"  name="coupon_sn" required value="<?= $data['coupon_sn'] ?>"  class="form-control" placeholder="优惠券号码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">优惠券兑换码</label>
            <div class="col-sm-3">
                <input type="text"  name="coupon_pwd"  value="<?= $data['coupon_pwd'] ?>" class="form-control" placeholder="优惠券兑换码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">激活手机号</label>
            <div class="col-sm-3">
                <input type="text"  name="mobile" value="<?= $data['mobile'] ?>"  class="form-control" placeholder="激活手机号">
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-3">
                <select  name="status"  placeholder="状态"  class="form-control" <?php if($data['status'] == 2 || $data['status'] == 3) echo 'disabled="disabled"';?>>
                    <?php foreach ($coupon_status as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['status']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-3">
                <input type="text"  name="remark" value="<?= $data['remark'] ?>"  class="form-control" placeholder="备注">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">修改时间</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" name="u_time" value="<?php echo  !empty($data['u_time'])?date("Y-m-d H:i:s",$data['u_time']):'--'; ?>" disabled="disabled">
            </div>
        </div>
        <input type="hidden"  name="id"  value="<?php echo $data['id']; ?>">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>
<script src="/backend/web/js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    is_show='<?php echo $data['coupon_type']?>'
    if(is_show=='2'){
        $('.uprescue').show();
    }else{
        $('.uprescue').hide();
    }
    function changescene(_this) {
        if($(_this).val()=='2'){
            $('.uprescue').show();
        }else {
            $('.uprescue').hide();
        }
    }
</script>

