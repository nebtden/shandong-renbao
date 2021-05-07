<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18 0018
 * Time: 下午 4:43
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
    <h4>盛大洗车订单管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group"><input type="text" id="orderid" name="orderid"  class="form-control"  placeholder="搜索订单id"></div>
        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="搜索用户手机号"></div>
        <div class="form-group"><input type="text" id="coupon_sn" name="coupon_sn"  class="form-control"  placeholder="搜索优惠券码"></div>
        <div class="form-group"><input type="text" id="shopName" name="shopName"  class="form-control"  placeholder="搜索门店名称"></div>
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
            <select id="company_id" name="company_id"  placeholder="选择供应商"  class="form-control">
                <option value="">选择供应商 </option>
                <?php foreach ($wash_company as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索下单时间-始">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索下单时间—末">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="s_time" id="s_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成时间-始">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="e_time" id="e_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成时间—末">
        </div>
        <div class="form-group"><input type="text" id="coupon_batch_no" name="coupon_batch_no"  class="form-control"  placeholder="券批号"></div>
        <div class="form-group"><input type="text" id="city" name="city"  class="form-control"  placeholder="区域"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="state" data-checkbox="true"></th>-->
        <th data-field="id" class="table-check" >编号</th>
        <th data-field="outOrderNo">订单ID</th>
        <th data-field="couponId">优惠券码</th>
        <th data-field="mobile">用户手机号</th>
        <th data-field="shopName">门店名称</th>
        <th data-field="serviceName">服务名称</th>
        <th data-field="consumerCode">消费凭证</th>
        <th data-field="status">订单状态</th>
        <th data-field="c_time">下单时间</th>
        <th data-field="s_time">完成时间</th>
        <th data-field="nickname">微信昵称</th>
        <th  data-field="companyid">客户公司</th>
        <th  data-field="company_id">供应商</th>
        <th  data-field="coupon_batch_no">券批号</th>
        <th  data-field="city">区域</th>
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_dianwash.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['order/dianwashlist']); ?>',
        durl="";

    $("#download").click(function(){

        var opt1 = $("#orderid").val();
        var opt2 = $("#mobile").val();
        var opt3 = $("#coupon_sn").val();
        var opt4 = $("#userid").val();
        var opt5 = $("#status").val();
        var opt6 = $("#start_time").val();
        var opt7 = $("#end_time").val();
        var opt8 = $("#companyid").val();
        var opt9 = $("#shopName").val();
        var opt10 = $("#company_id").val();
        var opt11 = $("#s_time").val();
        var opt12 = $("#e_time").val();
        var opt13 = $("#coupon_batch_no").val();
        var opt14 = $("#city").val();
        var url = '<?php echo Url::to(["order/dianwashdownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            orderid:opt1,
            mobile:opt2,
            coupon_sn:opt3,
            userid:opt4,
            status:opt5,
            start_time:opt6,
            end_time:opt7,
            companyid:opt8,
            shopName:opt9,
            company_id:opt10,
            s_time:opt11,
            e_time:opt12,
            coupon_batch_no:opt13,
            city:opt14
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



