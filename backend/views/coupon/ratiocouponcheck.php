<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31 0031
 * Time: 下午 3:11
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
    <h4>优惠券使用率查询 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">

    <form class="form-inline">
        <div class="form-group">
            <select id="companyid" name="companyid"  placeholder="客户公司"  class="form-control">
                <option value="">客户公司</option>
                <?php foreach ($companys as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group"><input type="text" id="batch_no" name="batch_no"  class="form-control"  placeholder="搜索券批号"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="sousuo" onclick="window.location.href='<?php echo Url::to(['coupon/checkcoupon']);?>';"></span> 优惠券总的使用情况</button>
        <button type="button" class="btn btn-info" id="sousuo" onclick="window.location.href='<?php echo Url::to(['coupon/ratiowashcheck']);?>';"></span> 洗车券使用率查询</button>
    </form>

</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th data-field="total">优惠券总数</th>
        <th data-field="not_used">未使用数</th>
        <th data-field="not_used_percent">未使用百分比%</th>
        <th data-field="activation">激活数</th>
        <th data-field="activation_percent">激活数百分比%</th>
        <th data-field="already_used">已使用数</th>
        <th data-field="already_used_percent">已使用百分比%</th>
        <th data-field="expired">已过期数</th>
        <th data-field="expired_percent">已过期百分比%</th>
        <th data-field="batch_no">优惠券批号</th>
        <th data-field="name">优惠券名称</th>
        <th data-field="amount">优惠券面值</th>
        <th data-field="companyid">客户公司</th>

    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_ratio.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['coupon/ratiocouponcheck']); ?>',
        durl="";
</script>



