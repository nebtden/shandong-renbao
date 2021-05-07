<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24 0024
 * Time: 下午 2:40
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
    <h4>券包使用率查询 <small>&nbsp;/&nbsp;列表页面</small></h4>
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
        <button type="button" class="btn btn-info" id="sousuo" onclick="window.location.href='<?php echo Url::to(['coupon/checkpackage']);?>';"></span> 券包总的使用情况</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th data-field="total">券包总数</th>
        <th data-field="not_used">未使用数</th>
        <th data-field="not_used_percent">未使用百分比%</th>
        <th data-field="already_used">已使用数</th>
        <th data-field="already_used_percent">已使用百分比%</th>
        <th data-field="expired">已过期数</th>
        <th data-field="expired_percent">已过期百分比%</th>
        <th data-field="batch_nb">券包批号</th>
        <th data-field="companyid">客户公司</th>
        <th data-field="c_time">券包生成时间</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_ratio.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/mealedit']);?>",
        listurl='<?php echo Url::to(['coupon/ratiocheck']); ?>',
        durl="<?php echo Url::to(['coupon/meal_del']); ?>";
</script>



