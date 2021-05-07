<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 上午 9:53
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
    <h4>油券订单管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group">
            <select id="status" name="status"  placeholder="订单状态"  class="form-control">
                <option value="">选择订单状态 </option>
                <?php foreach ($oilorstatus as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select id="oil_company" name="oil_company"  placeholder="选择油卡供应商"  class="form-control">
                <option value="">选择油卡供应商 </option>
                <?php foreach ($oil_company as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="form-group"><input type="text" id="orderid" name="orderid"  class="form-control"  placeholder="搜索订单号"></div>
        <div class="form-group"><input type="text" id="card_no" name="card_no"  class="form-control"  placeholder="搜索油卡卡号"></div>
        <div class="form-group"><input type="text" id="coupon_sn" name="coupon_sn"  class="form-control"  placeholder="搜索优惠券码"></div>
        <div class="form-group">
            <select id="companyid" name="companyid"  placeholder="客户公司"  class="form-control">
                <option value="">客户公司</option>
                <?php foreach ($companys as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="搜索用户手机"></div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索订单创建开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索订单创建结束时间">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="state" data-checkbox="true"></th>-->
        <th class="table-check" data-field="id">编号ID</th>
        <th  data-field="nickname">微信昵称</th>
        <th  data-field="mobile">用户手机</th>
        <th  data-field="coupon_id">优惠券码</th>
        <th data-field="orderid">订单编号</th>
        <th data-field="card_no">充值的油卡卡号</th>
        <th data-field="card_type">油卡类型</th>
        <th data-field="amount">充值金额（元）</th>
        <th data-field="c_time">订单创建时间</th>
        <th  data-field="s_time">订单完成时间</th>
        <th  data-field="status">订单状态</th>
        <th  data-field="companyid">客户公司</th>
        <th  data-field="company_id">供应商</th>
        <th  data-field="errmsg">错误信息</th>

<!--        <th data-field="itemname">商品名称</th>-->
<!--        <th  data-field="price">供货价</th>-->
<!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_car_oilor.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['order']);?>",
        listurl='<?php echo Url::to(['order/oilorlist']); ?>',
        durl="<?php echo Url::to(['order']); ?>";

    $("#download").click(function(){
        var opt1 = $("#status").val();
        var opt2 = $("#orderid").val();
        var opt3 = $("#card_no").val();
        var opt4 = $("#start_time").val();
        var opt5 = $("#end_time").val();
        var opt6 = $("#companyid").val();
        var opt7 = $("#coupon_sn").val();
        var opt8 = $("#oil_company").val();
        var opt9 = $("#mobile").val();
        var url = '<?php echo Url::to(["order/oilordownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            status:opt1,
            orderid:opt2,
            card_no:opt3,
            start_time:opt4,
            end_time:opt5,
            companyid:opt6,
            coupon_sn:opt7,
            oil_company:opt8,
            mobile:opt9
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


