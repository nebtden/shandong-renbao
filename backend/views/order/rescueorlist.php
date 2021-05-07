<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 上午 11:45
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
    <h4>道路救援订单管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group">
            <select id="coupon_type" name="coupon_type"  placeholder="施救方式"  class="form-control">
                <option value="">选择施救方式 </option>
                <?php foreach ($coupon_faulttype as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select id="status" name="status"  placeholder="订单状态"  class="form-control">
                <option value="">订单状态 </option>
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

        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="搜索兑换手机号"></div>
        <div class="form-group"><input type="text" id="carno" name="carno"  class="form-control"  placeholder="搜索车牌号"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
<!--        <th data-field="state" data-checkbox="true"></th>-->
        <th class="table-check" data-field="id">编号ID</th>
        <th  data-field="orderid">救援单号</th>
        <th  data-field="calltime">求救时间</th>
        <th data-field="customername">联系人姓名</th>
        <th data-field="carno">车牌号</th>
        <th data-field="phone">联系电话</th>
        <th data-field="carbrand">车辆品牌</th>
        <th data-field="carmodel">车型</th>
        <th  data-field="faultaddress">故障地址</th>
        <th  data-field="rescueway">施救方式</th>
        <th data-field="c_time">生成时间</th>
        <th data-field="acceptance_time">受理时间</th>
        <th  data-field="complete_time">完成时间</th>
        <th  data-field="nickname">微信昵称</th>
        <th data-field="status">订单状态</th>
        <th  data-field="companyid">客户公司</th>

<!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>-->
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_data_rescueor.js" ></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['order/couponedit']);?>",
        listurl='<?php echo Url::to(['order/rescueorlist']); ?>',
        durl="<?php echo Url::to(['order/coupon_del']); ?>";

    $("#download").click(function(){
        var opt1 = $("#coupon_type").val();
        var opt2 = $("#status").val();
        var opt3 = $("#companyid").val();
        var opt4 = $("#mobile").val();
        var opt5 = $("#carno").val();

        var url = '<?php echo Url::to(["order/download"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            coupon_type:opt1,
            status:opt2,
            companyid:opt3,
            mobile:opt4,
            carno:opt5
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


