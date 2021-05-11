<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/12 0012
 * Time: 下午 2:30
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
    <h4>太保推送记录管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group"><input type="text" id="ticket_id" name="ticket_id"  class="form-control"  placeholder="太保订单号"></div>
        <div class="form-group"><input type="text" id="apply_name" name="apply_name"  class="form-control"  placeholder="客户姓名"></div>
        <div class="form-group"><input type="text" id="apply_phone" name="apply_phone"  class="form-control"  placeholder="客户电话"></div>
        <div class="form-group"><input type="text" id="car_no" name="car_no"  class="form-control"  placeholder="车牌号"></div>
        <div class="form-group"><input type="text" id="encrypt_code" name="encrypt_code"  class="form-control"  placeholder="洗车凭证"></div>
        <div class="form-group"><input type="text" id="shop_name" name="shop_name"  class="form-control"  placeholder="门店名称"></div>
        <div class="form-group">
            <select id="status" name="status"  placeholder="状态"  class="form-control">
                <option value="">状态 </option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="form-group">
            <input type="text" class=" form-control" name="s_time" id="s_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索创建开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="e_time" id="e_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索创建结束时间">
        </div>

        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成结束时间">
        </div>


        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="state" data-checkbox="true"></th>-->
        <th data-field="id" class="table-check" >编号</th>
        <th data-field="ticket_id">太保订单号</th>
        <th data-field="apply_name">客户姓名</th>
        <th data-field="apply_phone">客户电话</th>
        <th data-field="car_rental_vehicle_no">车牌号</th>
        <th data-field="point_time">预约时间</th>
        <th data-field="address">门店区域</th>
<!--        <th data-field="addressdesc">门店地址</th>-->
        <th data-field="shop_name">门店名称</th>
        <th data-field="encrypt_code">洗车凭证</th>
        <th data-field="consumer_code">盛大核销码</th>
        <th data-field="status">状态</th>
        <th data-field="c_time">创建时间</th>
        <th data-field="u_time">完成时间</th>
        <!--<th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_taibao.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['order/taibaoorlist']); ?>',
        durl="";
    $("#download").click(function(){
        var opt1  = $("#ticket_id").val();
        var opt2  = $("#apply_name").val();
        var opt3  = $("#apply_phone").val();
        var opt4  = $("#car_no").val();
        var opt5  = $("#encrypt_code").val();
        var opt6 = $("#status").val();
        var opt7 = $("#start_time").val();
        var opt8 = $("#end_time").val();
        var opt9 = $("#s_time").val();
        var opt10 = $("#e_time").val();
        var opt11 = $("#shop_name").val();
        var url = '<?php echo Url::to(["order/taibaodownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            ticket_id:opt1,
            apply_name:opt2,
            apply_phone:opt3,
            car_no:opt4,
            encrypt_code:opt5,
            status:opt6,
            start_time:opt7,
            end_time:opt8,
            s_time:opt9,
            e_time:opt10,
            shop_name:opt11

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



