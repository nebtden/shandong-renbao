<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 下午 3:58
 */
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>洗车券管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="servicecode" name="servicecode"  class="form-control"  placeholder="搜索洗车券服务码"></div>
       <div class="form-group"><input type="text" id="batch_no" name="batch_no"  class="form-control"  placeholder="搜索批号"></div>
        <div class="form-group">
            <select id="status" name="status"  placeholder="选择洗车券状态"  class="form-control">
                <option value="">选择洗车券状态</option>
                <?php foreach ($status as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
             <input type="text" class=" form-control" name="month" id="month"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM'})" placeholder="搜索洗车券可使用的月份">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="搜索洗车券使用开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="搜索洗车券使用结束时间">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
<!--        <button type="button" class="btn btn-info" id="download"> 导成excel</button>-->
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/leadinwash']);?>';"> 导入洗车券</button>
        <button type="button" class="btn btn-info" onclick="downloadExcel()">下载导入模板</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="servicecode">服务码</th>
        <th data-field="month">洗车券使用的月份</th>
        <th data-field="startdate">开始时间</th>
        <th data-field="enddate">过期时间</th>
        <th data-field="use_time">兑换时间</th>
        <th data-field="mobile">兑换手机号</th>
        <th data-field="shopname">核销门店</th>
        <th data-field="status">状态</th>
        <th data-field="batch_no">批号</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_carwash.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/carwashedit']);?>",
        listurl='<?php echo Url::to(['coupon/carwashlist']); ?>',
        durl="<?php echo Url::to(['coupon/coupon_del']); ?>";

    $("#download").click(function(){
        var coupon_type = $("#coupon_type").val();
        var mobile = $("#mobile").val();
        var coupon_sn = $("#coupon_sn").val();
        var batch_no = $("#batch_no").val();
        var url = '<?php echo Url::to(["coupon/download"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{coupon_type:coupon_type,mobile:mobile,coupon_sn:coupon_sn,batch_no:batch_no},function(json){
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


    function downloadExcel(){
        window.location.href = "<?php echo Url::to(['coupon/dexcelwash']) ?>";
    }
</script>


