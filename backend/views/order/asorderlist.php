<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28 0028
 * Time: 上午 11:26
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
    <h4>年检订单管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group"><input type="text" id="orderid" name="orderid"  class="form-control"  placeholder="搜索订单id"></div>
        <div class="form-group"><input type="text" id="carresp" name="carresp"  class="form-control"  placeholder="进度手机号"></div>
        <div class="form-group"><input type="text" id="carnum" name="carnum"  class="form-control"  placeholder="搜索车牌号"></div>
        <div class="form-group"><input type="text" id="coupon_sn" name="coupon_sn"  class="form-control"  placeholder="搜索优惠券号码"></div>
        <div class="form-group"><input type="number" id="userid" name="userid"  class="form-control"  placeholder="搜索用户ID"></div>

        <div class="form-group">
            <select id="status" name="status"  placeholder="订单状态"  class="form-control">
                <option value="">选择订单状态 </option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select id="companyid" name="companyid"  placeholder="客户公司"  class="form-control">
                <option value="">客户公司</option>
                <?php foreach ($companys as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索支付开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索支付结束时间">
        </div>

        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="id" class="table-check" >编号</th>-->
        <th data-field="uid">用户ID</th>
        <th data-field="orderid">订单ID</th>
        <th data-field="carresp">进度手机号</th>
        <th data-field="couponid">优惠券码</th>
        <th data-field="carnum">车牌号</th>
        <th data-field="carcity">年检城市</th>
        <th data-field="amount">金额</th>
        <th data-field="status">订单状态</th>
        <th data-field="c_time">下单时间</th>
        <th data-field="s_time">完成时间</th>
        <th data-field="nickname">微信昵称</th>
        <th data-field="express">快递公司</th>
        <th data-field="expressno">快递单号</th>
        <th  data-field="companyid">客户公司</th>
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_asorder.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['order/asorderlist']); ?>',////////
        durl="";

    $("#download").click(function(){

        var opt1 = $("#orderid").val();
        var opt2 = $("#carresp").val();
        var opt3 = $("#carnum").val();
        var opt4 = $("#coupon_sn").val();
        var opt5 = $("#userid").val();
        var opt6 = $("#status").val();
        var opt7 = $("#companyid").val();
        var opt8 = $("#start_time").val();
        var opt9 = $("#end_time").val();
        var url = '<?php echo Url::to(["order/asorderdownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            orderid:opt1,
            carresp:opt2,
            carnum:opt3,
            coupon_sn:opt4,
            userid:opt5,
            status:opt6,
            companyid:opt7,
            start_time:opt8,
            end_time:opt9,
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



