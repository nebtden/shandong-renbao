<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/6 0006
 * Time: 下午 4:04
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
    <h4>优惠券管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="搜索兑换手机号"></div>
        <div class="form-group"><input type="text" id="coupon_sn" name="coupon_sn"  class="form-control"  placeholder="搜索优惠券号码"></div>
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
        <th  data-field="coupon_type">优惠券类型</th>
        <th  data-field="name">优惠券名称</th>
        <th data-field="amount">优惠券面额</th>
        <th data-field="used_num">已用次数</th>
        <th data-field="coupon_sn">优惠券号码</th>
        <th data-field="mobile">手机号</th>
        <th  data-field="active_time">激活时间</th>
        <th  data-field="use_time">使用时间</th>
        <th  data-field="use_limit_time">过期时间</th>
        <th  data-field="c_time">生成时间</th>
        <th  data-field="status">状态</th>
        <th  data-field="batch_no">批号</th>
        <th  data-field="company">平台</th>
        <th  data-field="companyid">客户公司</th>
        <th  data-field="is_mensal">月卡</th>
        <th  data-field="source">用户来源</th>
    </tr>
    </thead>
</table>
<script src="../js/handle_data_coupon.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['service/couponlist']); ?>',
        durl="";
</script>

