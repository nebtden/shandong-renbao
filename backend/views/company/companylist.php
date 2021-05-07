<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 上午 10:52
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
    <h4>公司管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['company/companyedit']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group"><input type="text" id="name" name="name"  class="form-control"  placeholder="搜索公司名称"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="name">公司名称</th>
        <th  data-field="webpage_title">网页标题</th>
        <th  data-field="background_pic">背景图片</th>
        <th data-field="c_time">添加时间</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_company.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['company/companyedit']);?>",
        listurl='<?php echo Url::to(['company/companylist']); ?>',
        durl="";
</script>



