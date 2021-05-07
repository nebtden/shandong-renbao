<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26 0026
 * Time: 上午 11:19
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
    <h4>套餐管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['coupon/mealedit']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group">
            <select id="status" name="status"  placeholder="状态"  class="form-control">
                <option value="">选择套餐状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group"><input type="text" id="meal_name" name="meal_name"  class="form-control"  placeholder="搜索套餐名称"></div>
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
        <th  data-field="name">套餐名称</th>
        <th data-field="edaicar">e代驾券（面值，数量，批号）</th>
        <th data-field="roadrescue">道路救援券（可用次数，数量，批号，场景）</th>
        <th data-field="carwash">洗车券（可用次数，数量，批号）</th>
        <th data-field="oilinfo">油券（面额，数量，批号）</th>
        <th  data-field="status">状态</th>
        <th  data-field="remarks">备注</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_meal.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/mealedit']);?>",
        listurl='<?php echo Url::to(['coupon/meallist']); ?>',
        durl="<?php echo Url::to(['coupon/meal_del']); ?>";
</script>



