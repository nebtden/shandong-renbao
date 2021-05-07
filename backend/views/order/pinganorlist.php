<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/22 0022
 * Time: 下午 3:31
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
    <h4>平安洗车订单管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="coupon_code" name="coupon_code"  class="form-control"  placeholder="平安兑换码"></div>
        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="联系手机号"></div>
        <div class="form-group"><input type="text" id="partner_order" name="partner_order"  class="form-control"  placeholder="盛大订单编号"></div>
        <div class="form-group"><input type="text" id="order_id" name="order_id"  class="form-control"  placeholder="平安订单编号"></div>
        <div class="form-group">
            <select id="status" name="status"  placeholder="卡券核销状态"  class="form-control">
                <option value="">核销状态 </option>
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
        <div class="form-group">
            <input type="text" class=" form-control" name="s_time" id="s_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="e_time" id="e_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索完成结束时间">
        </div>
        <div class="form-group">
            <select id="companyid" name="companyid"  placeholder="客户公司"  class="form-control">
                <option value="">客户公司</option>
                <?php foreach ($companys as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
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
        <th data-field="coupon_id">卡券编号</th>
        <th data-field="coupon_code">兑换码</th>
        <th data-field="mobile">手机号</th>
        <th data-field="product_name">商品名称</th>
        <th data-field="start_time">生效时间</th>
        <th data-field="end_time">失效时间</th>
        <th data-field="coupon_status">卡券状态</th>
        <th data-field="partner_order">盛大订单编号</th>
        <th data-field="store_name">洗车门店名称</th>
        <th data-field="verifytime">核销时间</th>
        <th data-field="order_id">平安订单编号</th>
        <th data-field="status">核销状态</th>
        <th data-field="c_time">创建时间</th>
        <th data-field="s_time">完成时间</th>
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script src="../js/handle_data_pinganwash.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl='<?php echo Url::to(['order/pinganorlist']); ?>',
        durl="";
    $("#download").click(function(){
        var opt1  = $("#coupon_code").val();
        var opt2  = $("#mobile").val();
        var opt3  = $("#partner_order").val();
        var opt4  = $("#order_id").val();
        var opt5  = $("#status").val();
        var opt6  = $("#start_time").val();
        var opt7  = $("#end_time").val();
        var opt8  = $("#s_time").val();
        var opt9 = $("#e_time").val();
        var opt10 = $("#companyid").val();
        var url = '<?php echo Url::to(["order/porderdownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            coupon_code:opt1,
            mobile:opt2,
            partner_order:opt3,
            order_id:opt4,
            status:opt5,
            start_time:opt6,
            end_time:opt7,
            s_time:opt8,
            e_time:opt9,
            companyid:opt10
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



