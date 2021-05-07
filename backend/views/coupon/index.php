<?php
use yii\helpers\Url;
?>
<style>
    .layui-layer-content{
        padding-left:23px;
    }
    .layui-layer-rim{ font-size: 14px;}
</style>
<div class="page-header am-fl am-cf">
    <h4>优惠券管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <div class="form-group">
            <select id="coupon_type" name="coupon_type"  placeholder="优惠券的类型"  class="form-control">
                <option value="">选择优惠券类型</option>
                <?php foreach ($coupon_type as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select id="coupon_status" name="coupon_status"  placeholder="状态"  class="form-control">
                <option value="">选择优惠券状态</option>
                <?php foreach ($coupon_status as $key => $val):?>
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
        <div class="form-group"><input type="text" id="coupon_sn" name="coupon_sn"  class="form-control"  placeholder="搜索优惠券号码"></div>
        <div class="form-group"><input type="text" id="coupon_name" name="coupon_name"  class="form-control"  placeholder="优惠券名称"></div>
        <div class="form-group"><input type="number" id="amount" name="amount"  class="form-control"  placeholder="次数限制或面额"></div>
        <div class="form-group"><input type="text" id="batch_no" name="batch_no"  class="form-control"  placeholder="优惠券批号"></div>
        <div class="form-group"><input type="number" id="coupon_id" name="coupon_id"  class="form-control"  placeholder="优惠券ID"></div>
        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索优惠券使用开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索优惠券使用结束时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="s_time" id="s_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索优惠券生成开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="e_time" id="e_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索优惠券生成结束时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="acs_time" id="acs_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索优惠券激活开始时间">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="ace_time" id="ace_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" placeholder="搜索优惠券激活结束时间">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/generate']);?>';">批量生成优惠券</button>
        <button type="button" class="btn btn-info" id="download"> 导成excel</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/leadin']);?>';"> 导入优惠券</button>
        <button type="button" class="btn btn-info" onclick="downloadExcel()">下载导入模板</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/leadinjiutian']);?>';"> 导入九天汽车救援券</button>
        <button type="button" class="btn btn-info" onclick="downloadExceljt()">下载九天汽车救援券导入模板</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/batchnomerge']);?>';">批号合并</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="coupon_type">优惠券类型</th>
        <th  data-field="name">优惠券名称</th>
        <th data-field="amount">优惠券面额</th>
        <th data-field="used_num">已用次数</th>
        <th data-field="coupon_sn">优惠券号码</th>
        <th data-field="mobile">手机号</th>
        <th  data-field="active_time">激活时间</th>
        <th  data-field="use_time">使用时间</th>
        <th  data-field="use_limit_time">过期时间</th>
        <th  data-field="c_time">生成时间</th>
        <th  data-field="status">状态</th>
        <th  data-field="batch_no">批号</th>
        <th  data-field="company">平台</th>
        <th  data-field="companyid">客户公司</th>
        <th  data-field="is_mensal">月卡</th>
        <th  data-field="source">用户来源</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th> 
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_data_coupon.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/couponedit']);?>",
        listurl='<?php echo Url::to(['coupon/index']); ?>',
        durl="<?php echo Url::to(['coupon/coupon_del']); ?>";

    $("#download").click(function(){
        var opt1 = $("#coupon_type").val();
        var opt2 = $("#mobile").val();
        var opt3 = $("#coupon_sn").val();
        var opt4 = $("#coupon_status").val();
        var opt5 = $("#coupon_name").val();
        var opt6 = $("#amount").val();
        var opt7 = $("#batch_no").val();
        var opt8 = $("#coupon_id").val();
        var opt9 = $("#companyid").val();
        var opt10 = $("#start_time").val();
        var opt11 = $("#end_time").val();
        var opt12 = $("#s_time").val();
        var opt13 = $("#e_time").val();
        var opt14 = $("#acs_time").val();
        var opt15 = $("#ace_time").val();

        var url = '<?php echo Url::to(["coupon/download"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            coupon_type:opt1,
            mobile:opt2,
            coupon_sn:opt3,
            coupon_status:opt4,
            coupon_name:opt5,
            amount:opt6,
            batch_no:opt7,
            coupon_id:opt8,
            companyid:opt9,
            start_time:opt10,
            end_time:opt11,
            s_time:opt12,
            e_time:opt13,
            acs_time:opt14,
            ace_time:opt15
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
    function downloadExcel(){
        window.location.href = "<?php echo Url::to(['coupon/dexceldriving']) ?>";
    }
    function downloadExceljt(){
        window.location.href = "<?php echo Url::to(['coupon/dexceldrivingjt']) ?>";
    }

</script>

