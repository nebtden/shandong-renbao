<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/5 0005
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
    <h4>ETC设备码管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="code" name="code"  class="form-control"  placeholder="搜索设备编码"></div>
        <div class="form-group"><input type="text" id="batch_no" name="batch_no"  class="form-control"  placeholder="批号"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/leadinetccode']);?>';">导入ETC设备码</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID</th>
        <th  data-field="code">设备码</th>
        <th  data-field="batch_no">批号</th>
        <th data-field="c_time">添加时间</th>
        <th data-field="u_time">修改时间</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_etccode.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['coupon/etccodelist']); ?>',
        durl="";
</script>



