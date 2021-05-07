<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/10 0010
 * Time: 上午 11:36
 */

use yii\helpers\Url;
?>
<style>
    .layui-layer-content{
        padding-left:23px;
    }
    .layui-layer-rim{ font-size: 14px;}
</style>
<div class="page-header am-fl am-cf">
    <h4>诚泰客户信息管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group"><input type="text" id="customer_name" name="customer_name"  class="form-control"  placeholder="姓名"></div>
        <div class="form-group"><input type="text" id="customer_code" name="customer_code"  class="form-control"  placeholder="身份证号"></div>
        <div class="form-group"><input type="text" id="coupon_batch_no" name="coupon_batch_no"  class="form-control"  placeholder="优惠券批号"></div>
        <div class="form-group"><input type="text" id="batch_no" name="batch_no"  class="form-control"  placeholder="诚泰兑换码批号"></div>
        <div class="form-group">
            <select id="status" name="status"  placeholder="状态"  class="form-control">
                <option value="">状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select id="company_id" name="company_id"  placeholder="公司"  class="form-control">
                <option value="">选择公司</option>
                <?php foreach ($companylist as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索添加开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索添加结束时间">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/leadinctcode']);?>';"> 导诚泰客户信息</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th  data-field="state" data-checkbox="true"></th>
        <th  class="table-check" data-field="id">编号ID</th>
        <th  data-field="customer_name">客户姓名</th>
        <th  data-field="customer_code">身份证号</th>
        <th  data-field="customer_mobile">客户手机</th>
        <th  data-field="package_batch_no">优惠券批号</th>
        <th  data-field="c_time">添加时间</th>
        <th  data-field="u_time">兑换时间</th>
        <th  data-field="batch_no">人保兑换码批号</th>
        <th  data-field="company_id">公司名称</th>
        <th  data-field="uid">用户ID</th>
        <th  data-field="status">状态</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_car_ctcode.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/ctcodeedit']); ?>",
        listurl='<?php echo Url::to(['coupon/ctlist']); ?>',
        durl="";
</script>



