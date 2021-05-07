<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/12 0012
 * Time: 下午 5:07
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
    <h4>旅游报名管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['travel/enroll-edit']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
        <div class="form-group"><input type="text" id="name" name="name"  class="form-control"  placeholder="游客姓名"></div>
        <div class="form-group"><input type="text" id="code" name="code"  class="form-control"  placeholder="身份证号"></div>
        <div class="form-group"><input type="text" id="mobile" name="mobile"  class="form-control"  placeholder="手机号"></div>
        <div class="form-group"><input type="text" id="yincode" name="yincode"  class="form-control"  placeholder="营销员编号"></div>
        <div class="form-group">
            <select id="luxian" name="luxian"  placeholder="旅游路线"  class="form-control">
                <option value="">旅游路线 </option>
                <?php foreach ($luxianlist as $key => $val):?>
                    <option value="<?=$key?>"><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
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

        <div class="form-group">
            <input type="text" class=" form-control" name="start_time" id="start_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="搜索出行开始日期">
        </div>
        <div class="form-group">
            <input type="text" class=" form-control" name="end_time" id="end_time"  readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="搜索出行结束日期">
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" id="download" class="btn btn-info">报名数据导出</button>
        <button type="button" class="btn btn-info"  onclick="window.location.href='<?php echo Url::to(['travel/leadin']);?>';">报名数据导入</button>
        <button type="button" class="btn btn-info" onclick="downloadExcel()">导入模板下载</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID</th>
        <th  data-field="name">游客姓名</th>
        <th  data-field="luxian">游客路线</th>
        <th  data-field="travel_date">出行日期</th>
        <th  data-field="code">身份证号</th>
        <th data-field="mobile">手机号</th>
        <th data-field="username">营销员姓名</th>
        <th data-field="usercode">营销员代码</th>
        <th data-field="compaypname">中支公司</th>
        <th data-field="compayname">机构</th>
        <th data-field="ctime">添加时间</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_travel_enroll.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['travel/enroll-edit']); ?>",
        listurl="<?php echo Url::to(['travel/enroll-list']); ?>",
        durl="<?php echo Url::to(['travel/delenroll']); ?>";

    $("#download").click(function(){
        var opt1 = $("#name").val();
        var opt2 = $("#code").val();
        var opt3 = $("#mobile").val();
        var opt4 = $("#start_time").val();
        var opt5 = $("#end_time").val();
        var opt6 = $("#luxian").val();
        var opt7 = $("#orgain").val();
        var opt8 = $("#jigou").val();
        var opt9 = $("#yincode").val();
        var url = '<?php echo Url::to(["travel/enrolldownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            name:opt1,
            code:opt2,
            mobile:opt3,
            start_time:opt4,
            end_time:opt5,
            luxian:opt6,
            orgain:opt7,
            jigou:opt8,
            yincode:opt9

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

    function downloadExcel(){
        window.location.href='<?php echo Url::to(['travel/dexcelenroll']);?>';
    }
</script>


