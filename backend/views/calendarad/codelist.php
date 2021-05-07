<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5 0005
 * Time: 上午 10:34
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
    <h4>观看码管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <div class="form-group"><input type="text" id="code" name="code"  class="form-control"  placeholder="搜索观看码"></div>
        <div class="form-group"><input type="text" id="batch_no" name="batch_no"  class="form-control"  placeholder="搜索批号"></div>
        <div class="form-group">
            <select id="company_id" name="company_id"  placeholder="公司"  class="form-control">
                <option value="">公司</option>
                <?php foreach ($company as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['calendarad/generate']);?>';">批量生成观看码</button>
        <button type="button" id="download" class="btn btn-info">观看码Excel导出</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="code">观看码</th>
        <th  data-field="views_num">可观看次数</th>
        <th data-field="use_num">已观看次数</th>
        <th data-field="c_time">生成时间</th>
        <th data-field="u_time">最后观看时间</th>
        <th data-field="status">状态</th>
        <th data-field="batch_no">批号</th>
        <th data-field="company_id">公司</th>

    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_data_video.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl="<?php echo Url::to(['calendarad/codelist']); ?>",
        durl="";

    $("#download").click(function(){
        var opt1 = $("#code").val();
        var opt2 = $("#batch_no").val();

        var url = '<?php echo Url::to(["calendarad/codedownload"]);?>';
        var content = "<ul style='padding:10px 20px;'>";
        $.getJSON(url,{
            code:opt1,
            batch_no:opt2
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

