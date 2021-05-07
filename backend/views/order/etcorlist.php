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
    <h4>ETC订单管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="coupon_sn" name="coupon_sn"  class="form-control"  placeholder="优惠券码"></div>
        <div class="form-group"><input type="number" id="userid" name="userid"  class="form-control"  placeholder="用户编号"></div>
        <div class="form-group"><input type="text" id="cert_no" name="cert_no"  class="form-control"  placeholder="身份证号"></div>
        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="用户手机"></div>
        <div class="form-group"><input type="text" id="plate_no" name="plate_no"  class="form-control"  placeholder="车牌号"></div>
        <div class="form-group"><input type="text" id="contact" name="contact"  class="form-control"  placeholder="指定联系人姓名"></div>
        <div class="form-group"><input type="text" id="order_id" name="order_id"  class="form-control"  placeholder="山东ETC订单号"></div>
        <div class="form-group"><input type="text" id="username" name="username"  class="form-control"  placeholder="用户名"></div>
        <div class="form-group"><input type="text" id="card_no" name="card_no"  class="form-control"  placeholder="鲁通卡号"></div>
        <div class="form-group">
            <select id="status" name="status"  placeholder="订单状态"  class="form-control">
                <option value="">订单状态 </option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索创建开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索创建结束时间">
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
        <th data-field="uid">用户编号</th>
        <th data-field="coupon_id">券码</th>
        <th data-field="username">用户名</th>
        <th data-field="cert_no">身份证号</th>
        <th data-field="mobile">用户手机</th>
        <th data-field="address">收货地址</th>
        <th data-field="link_phone">联系人电话</th>
        <th data-field="link_address">联系人地址</th>
        <th data-field="contact">联系人姓名</th>
        <th data-field="card_no">鲁通卡号</th>
        <th data-field="plate_no">车牌号</th>
        <th data-field="is_receiving">收货状态</th>
        <th data-field="order_id">山东ETC订单号</th>
        <th data-field="status">订单状态</th>
        <th data-field="c_time">创建时间</th>
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_etc.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['order/etcorlist']); ?>',
        durl="";
    $("#download").click(function(){
        var opt1  = $("#coupon_sn").val();
        var opt2  = $("#userid").val();
        var opt3  = $("#cert_no").val();
        var opt4  = $("#mobile").val();
        var opt5  = $("#plate_no").val();
        var opt6  = $("#contact").val();
        var opt7  = $("#order_id").val();
        var opt8  = $("#username").val();
        var opt9 = $("#card_no").val();
        var opt10 = $("#status").val();
        var opt11 = $("#start_time").val();
        var opt12 = $("#end_time").val();
        var url = '<?php echo Url::to(["order/etcorderdownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            coupon_sn:opt1,
            userid:opt2,
            cert_no:opt3,
            mobile:opt4,
            plate_no:opt5,
            contact:opt6,
            order_id:opt7,
            username:opt8,
            card_no:opt9,
            status:opt10,
            start_time:opt11,
            end_time:opt12
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



