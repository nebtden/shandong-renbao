<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/25 0025
 * Time: 下午 2:03
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
    <h4>洗车记录管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group"><input type="text" id="servicecode" name="servicecode"  class="form-control"  placeholder="搜索服务码"></div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索记录开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索记录结束时间">
        </div>
        <div class="form-group">
            <div class="form-group">
                <input type="text" id="companyid" name="companyid"  class="form-control"  placeholder="客户公司">
            </div>
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>
<!--        <button type="button" class="btn btn-info" onclick="window.location.href='';"> 途虎数据导入</button>-->

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="state" data-checkbox="true"></th>-->
        <th data-field="id" class="table-check" >编号ID</th>
        <th data-field="shopid">核销门店id</th>
        <th data-field="shopname">核销门店名称</th>
        <th data-field="serviceid">服务ID</th>
        <th data-field="servicename">服务名称</th>
        <th data-field="verifytime">核销时间</th>
        <th data-field="price">价格</th>
        <th data-field="region">核销门店所属地区</th>
        <th data-field="servicecode">服务码</th>
        <th data-field="c_time">回调时间</th>
        <th data-field="nickname">微信昵称</th>
        <th  data-field="companyid">客户公司</th>
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/complete.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_carwash.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['order/couponedit']);?>",
        listurl='<?php echo Url::to(['order/carwashlist']); ?>',
        durl="<?php echo Url::to(['order/coupon_del']); ?>";

    $("#companyid").bigAutocomplete({
        width: 604,
        url: "<?php echo Url::to(['coupon/get-new-company']); ?>",
        before: function () {
        },
        callback: function (data) {
            $("#companyid").attr('data-id',data.id);

        }
    });
    $("#download").click(function(){

        var servicecode = $("#servicecode").val();
        var start_time = $("#start_time").val();
        var end_time = $("#end_time").val();
        var companyid = $("#companyid").attr('data-id');

        var url = '<?php echo Url::to(["order/washdownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            servicecode:servicecode,
            start_time:start_time,
            end_time:end_time,
            companyid:companyid
        },function(json){
            if(json.status == 1){
                $.each(json.data,function(){
                    content += '<li><a href="'+this.url+'">'+this.name+'</a>';
                });
                content += "</ul>";
                layer.open({
                    type: 1,
                    title: 'Excel导出',
                    area: ['600px', '360px'],
                    shadeClose: true, //点击遮罩关闭
                    content: content
                });
            }else{
                alert(json.msg);
            }
        });
    });

</script>



