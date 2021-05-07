<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11 0011
 * Time: 下午 2:21
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
    <h4>平安洗车网点管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="outlet_id" name="outlet_id"  class="form-control"  placeholder="平安网点id"></div>
        <div class="form-group"><input type="text" id="store_name" name="store_name"  class="form-control"  placeholder="网点名称"></div>
<!--        <div class="form-group">-->
<!--            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索创建开始时间">-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索创建结束时间">-->
<!--        </div>-->
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['order/leadinwangdian']);?>';"> 导入平安洗车网点</button>
        <button type="button" class="btn btn-info" onclick="downloadExcel()">下载平安洗车网点导入模板</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="state" data-checkbox="true"></th>-->
        <th data-field="id" class="table-check" >编号</th>
        <th data-field="outlet_id">平安网点id</th>
        <th data-field="store_name">门店名称</th>
        <th data-field="province">省</th>
        <th data-field="city">市</th>
        <th data-field="district">区或县</th>
        <th data-field="address">门店地址</th>
        <th data-field="c_time">导入时间</th>
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_wangdian.js" ></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['order/wangdianlist']); ?>',
        durl="";


    function downloadExcel(){
        window.location.href = "<?php echo Url::to(['order/dexcel']) ?>";
    }
</script>



