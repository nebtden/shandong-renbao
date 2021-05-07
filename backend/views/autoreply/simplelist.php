<?php
use yii\helpers\Url;
?>
<style>
    .fixed-table-body{height: 90%;}
</style>
<div class="page-header am-fl am-cf">
    <h4>微信自动回复 <small>&nbsp;/&nbsp;文字回复</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['autoreply/simplereply']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
        <div class="form-group"><input type="text" id="keywords"   class="form-control" placeholder="关键词"></div>

        <button type="button" class="btn btn-info" id="rsousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="keywords">关键词</th>
        <th data-field="details">回复内容</th>
        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['autoreply/simplereply']);?>",
        listurl='<?php echo Url::to(['autoreply/simplelist']); ?>',
        durl="<?php echo Url::to(['autoreply/simpledelete']); ?>",
        upturl='<?php echo Url::to(['autoreply/uptdata']);?>',height = $(window).height()-90;
</script>
<script src="../js/handle_data.js" ></script>
