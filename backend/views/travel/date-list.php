<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/13 0013
 * Time: 下午 2:25
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
    <h4>旅游日期管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['travel/edit-date']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
<!--        <button type="button" class="btn btn-default" id="remove">-->
<!--            <i class="glyphicon glyphicon-trash" ></i>-->
<!--        </button>-->
        <div class="form-group">
            <select id="luxian" name="luxian"  placeholder="旅游路线"  class="form-control">
                <option value="">旅游路线 </option>
                <?php foreach ($luxianlist as $key => $val):?>
                    <option value="<?=$key?>" <?php if($info['travel_list_id'] == $key) echo 'selected'; ?>><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="搜索出行开始日期">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="搜索出行结束日期">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <!--        <button type="button" id="download" class="btn btn-info">报名数据导出</button>-->
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID</th>
        <th data-field="luxian">旅游路线</th>
        <th data-field="date">出行开始日期</th>
        <th data-field="end">出行截止日期</th>
        <th data-field="number">可出行总人数</th>
        <th data-field="locked">锁定人数</th>
        <th data-field="prenum">实际报名人数</th>
        <th data-field="ctime">添加时间</th>
        <th data-field="status">上下架状态</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_travel_enroll.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['travel/edit-date']); ?>",
        listurl="<?php echo Url::to(['travel/date-list']); ?>",
        durl="";
</script>



