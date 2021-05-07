<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 下午 4:54
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
    <h4>套餐券管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group"><input type="text" id="package_sn" name="package_sn"  class="form-control"  placeholder="搜索套餐券号码"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>


    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="uid">用户ID</th>
        <th  data-field="name">套餐券名称</th>
        <th  data-field="package_sn">套餐券号码</th>
        <th  data-field="package_pwd">套餐券兑换码</th>
        <th data-field="meal_id">可选套餐编码</th>
        <th data-field="mealsname">所选套餐</th>
        <th data-field="use_limit_time">过期时间</th>
        <th data-field="use_time">使用时间</th>
        <th data-field="nickname">微信昵称</th>
        <th  data-field="status">状态</th>
        <th  data-field="batch_no">批号</th>
        <th  data-field="companyname">所属公司</th>
        <th  data-field="remarks">备注</th>

<!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_coupon_meal.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['service/couponmeallist']); ?>',
        durl="";
</script>



