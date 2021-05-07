<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/6 0006
 * Time: 下午 4:12
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
    <h4>视频管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['calendarad/videoedit']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group"><input type="text" id="title" name="title"  class="form-control"  placeholder="标题"></div>
        <div class="form-group">
            <select id="company_id" name="company_id"  placeholder="公司"  class="form-control">
                <option value="">公司</option>
                <?php foreach ($company as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span>搜索</button>

    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="title">标题</th>
        <th  data-field="sort">排序</th>
        <th data-field="show_desc">是否显示简介</th>
        <th data-field="path">视频地址</th>
        <th data-field="pic">封面</th>
        <th data-field="c_time">最后观看时间</th>
        <th data-field="company_id">公司</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_data_video2.js" ></script>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>
<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="",
        listurl="<?php echo Url::to(['calendarad/videolist']); ?>",
        eurl="<?php echo Url::to(['calendarad/videoedit']); ?>";
</script>

