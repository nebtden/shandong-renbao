<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/17 0017
 * Time: 下午 4:06
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
    <h4>代驾订单管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="搜索兑换手机号"></div>
        <div class="form-group"><input type="text" id="coupon_sn" name="coupon_sn"  class="form-control"  placeholder="搜索优惠券号码"></div>
        <div class="form-group"><input type="text" id="orderid" name="orderid"  class="form-control"  placeholder="搜索订单ID"></div>
        <div class="form-group"><input type="text" id="order_id" name="order_id"  class="form-control"  placeholder="搜索代驾方订单ID"></div>

        <div class="form-group">
            <select id="status" name="status"  placeholder="选择订单状态"  class="form-control">
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
            <select id="company_id" name="company_id"  placeholder="供应商"  class="form-control">
                <option value="">供应商</option>
                <?php foreach ($driving_company as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="form-group"><input type="text" id="uid" name="uid"  class="form-control"  placeholder="用户ID"></div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索下单开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索下单结束时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="s_time" id="s_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="e_time" id="e_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成结束时间">
        </div>
        <div class="form-group"><input type="number" id="c_amount" name="c_amount"  class="form-control"  placeholder="优惠券面额"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="state" data-checkbox="true"></th>-->
        <th class="table-check" data-field="id">编号ID</th>
        <th  data-field="mobile">手机号</th>
        <th  data-field="coupon_sn">优惠券号</th>
        <th data-field="orderid">订单ID</th>
        <th data-field="status">订单状态</th>
        <th data-field="departure">出发地</th>
        <th data-field="destination">目的地</th>
        <th data-field="order_id">代驾方订单编号</th>
        <th  data-field="amount">订单总金额</th>
        <th  data-field="cast">应付金额</th>
        <th  data-field="start_time">订单开始时间</th>
        <th data-field="end_time">订单结束时间</th>
        <th  data-field="nickname">微信昵称</th>
        <th  data-field="uid">用户编号</th>
        <th  data-field="companyid">客户公司</th>
        <th  data-field="company_id">供应商</th>
<!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_daicar.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['order/daicarorlist']); ?>',
        durl="";

    $("#download").click(function(){
        var opt1 = $("#mobile").val();
        var opt2 = $("#coupon_sn").val();
        var opt3 = $("#orderid").val();
        var opt4 = $("#order_id").val();
        var opt5 = $("#status").val();
        var opt6 = $("#uid").val();
        var opt7 = $("#start_time").val();
        var opt8 = $("#end_time").val();
        var opt9 = $("#companyid").val();
        var opt10 = $("#company_id").val();
        var opt11 = $("#s_time").val();
        var opt12 = $("#e_time").val();
        var opt13 = $("#c_amount").val();
        var url = '<?php echo Url::to(["order/daicardownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            mobile:opt1,
            coupon_sn:opt2,
            orderid:opt3,
            order_id:opt4,
            status:opt5,
            uid:opt6,
            start_time:opt7,
            end_time:opt8,
            companyid:opt9,
            company_id:opt10,
            s_time:opt11,
            e_time:opt12,
            c_amount:opt13

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


