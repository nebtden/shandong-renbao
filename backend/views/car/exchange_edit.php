<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10 0010
 * Time: 上午 10:25
 */
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>兑换记录管理 <small>&nbsp;/&nbsp;审核</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['car/exchange_edit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">兑换卡号</label>
            <div class="col-sm-3">
                <input type="text" name="exchange_card_num" class="form-control" value="<?php echo $data['exchange_card_num'];?>" id="inputEmail3" placeholder="兑换卡号" disabled="disabled" >
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">兑换卡的面值</label>
            <div class="col-sm-3">
                <input type="text" name="exchange_card_amount" class="form-control" value="<?php echo $data['exchange_card_amount'];?>" id="inputEmail3" placeholder="兑换卡的面值" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">兑换时间</label>
            <div class="col-sm-3">
                <input type="text" name="exchange_time" class="form-control" value="<?php echo $data['exchange_time'];?>" id="inputEmail3" placeholder="名称" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">对换者手机号</label>
            <div class="col-sm-3">
                <input type="text" name="exchange_tel" class="form-control" value="<?php echo $data['exchange_tel'];?>" id="inputEmail3" placeholder="对换者手机号" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">兑换商户名称</label>
            <div class="col-sm-3">
                <input type="text" name="exchange_name" class="form-control" value="<?php echo $data['exchange_name'];?>" id="inputEmail3" placeholder="兑换商户名称" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">提成系数</label>
            <div class="col-sm-3">
                <input type="text" name="t_amount" class="form-control" value="<?php echo $data['t_amount'];?>" id="inputEmail3" placeholder="提成系数" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">兑换商户名称</label>
            <div class="col-sm-3">
                <input type="text" name="exchange_name" class="form-control" value="<?php echo $data['exchange_name'];?>" id="inputEmail3" placeholder="兑换商户名称" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">兑换车牌号</label>
            <div class="col-sm-3">
                <input type="text" name="carno" class="form-control" value="<?php echo $data['carno'];?>" id="inputEmail3" placeholder="兑换车牌号" disabled="disabled">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">车牌照片</label>
            <div class="col-sm-3" style="float: left;">
                <img src="<?php echo $data['carpic'];?>" style="width: 100%; margin-bottom: 5px; " >
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-3">
                <select id="status" name="status"  placeholder="状态"  class="form-control">
                    <?php foreach ($logstatus as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['status']==$key) echo 'selected'; ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>



        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <?php if ($data['status']=='1'):?>
                    <button type="submit" class="btn btn-default">保存</button>
                <?php endif;?>
            </div>
        </div>
        <?php  if($data['id']) {?><input type="hidden"  name="id"  value="<?php echo $data['id']; ?>"><?php }?>
    </form>
</div>
<script>


</script>

