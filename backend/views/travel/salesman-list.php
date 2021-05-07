<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/13 0013
 * Time: 下午 4:23
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
    <h4>营销员管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['travel/salesman-edit']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
        <div class="form-group"><input type="text" id="name" name="name"  class="form-control"  placeholder="营销员姓名"></div>
        <div class="form-group"><input type="text" id="code" name="code"  class="form-control"  placeholder="营销员代号"></div>
        <div class="form-group">
            <select id="orgain" name="orgain"  placeholder="中支公司"  class="form-control" onchange="changejigou()">
                <option value="">中支公司</option>
                <?php foreach ($orgain as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select id="jigou" name="jigou"  placeholder="机构"  class="form-control">
                <option value="">机构</option>
            </select>
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <!--        <button type="button" id="download" class="btn btn-info">报名数据导出</button>-->
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID</th>
        <th  data-field="name">营销员姓名</th>
        <th  data-field="code">营销员代码</th>
        <th data-field="compaypname">中支公司</th>
        <th data-field="compayname">机构</th>
        <th data-field="ctime">添加时间</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_travel_user.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['travel/salesman-edit']) ?>",
        listurl="<?php echo Url::to(['travel/salesman-list']); ?>",
        durl="<?php echo Url::to(['travel/deluser']); ?>";

    function changejigou(){
        var orgain = $("#orgain").val();
        var url = "<?php echo Url::to(['getjigou']);?>";
        var html = "";
        if(!orgain){
            $('#jigou').html('<option value="">机构</option>');
            return;
        }else{
            $('#jigou').html('<option value="">机构</option>');
        }
        $.post(url,{orgain:orgain},function(json){
            if(json.status == 1){
                console.log(json.data);
                html += '<option value="">机构</option>';
                $.each(json.data,function(key,val){
                    html += '<option value="'+key+'">'+val+'</option>';
                });
                $('#jigou').html(html);
            }else{
                alert(json.msg);
            }
        });
    }
</script>


