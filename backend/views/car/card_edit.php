<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>兑换卡管理 <small>&nbsp;/&nbsp;编辑提成系数</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['car/card_edit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡号</label>
            <div class="col-sm-3">
                <input type="text" name="card_num" class="form-control" value="<?php echo $data['card_num'];?>"  placeholder="卡号" disabled="disabled" >
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡的面值</label>
            <div class="col-sm-3">
                <input type="text" name="card_amount" class="form-control" value="<?php echo $data['card_amount'];?>"  placeholder="卡的面值" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡的密码</label>
            <div class="col-sm-3">
                <input type="text" name="card_password" class="form-control" value="<?php echo $data['card_password'];?>"  placeholder="卡的密码" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡的区域</label>
            <div class="col-sm-3">
                <input type="text" name="card_region" class="form-control" value="<?php echo $data['card_region'];?>" id="inputEmail3" placeholder="卡的区域" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡的兑换时间</label>
            <div class="col-sm-3">
                <input type="text" name="card_time" class="form-control" value="<?php echo $data['card_time'];?>"  placeholder="卡的兑换时间" disabled="disabled">
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">卡的状态</label>
            <div class="col-sm-3">
                <input type="text" name="card_status" class="form-control" value="<?php echo $data['card_status'];?>"  placeholder="卡的状态" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">提成系数</label>
            <div class="col-sm-3">
                <input type="text"  name="t_sum" class="form-control" value="<?php echo $data['t_sum'];?>"  onkeyup="value=value.replace(/[^\d\.]/g,'')" placeholder="提成系数">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
        <?php  if($_REQUEST['id']) {?><input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>"><?php }?>
    </form>
</div>