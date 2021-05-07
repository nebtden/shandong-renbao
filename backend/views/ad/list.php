<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>广告管理 <small>&nbsp;/&nbsp;广告列表</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['ad/location']);?>';">
            <i class="glyphicon glyphicon">返回</i>
        </button>
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['ad/editlist','nid'=>$_REQUEST['nid'],'type'=>$_REQUEST['type']]);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">广告ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="title">广告标题</th>
        <th  data-field="picurl">缩略图</th>
        <th  data-field="catname">广告位置</th>
        <th  data-field="sort">排序</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>

<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,
        listurl='<?php echo Url::to(['ad/list','nid'=>$_REQUEST['nid'],'type'=>$_REQUEST['type']]);?>';
    var durl="<?php echo Url::to(['ad/dellist']); ?>",
        upturl="<?php echo Url::to(['ad/editlist','nid'=>$_REQUEST['nid'],'type'=>$_REQUEST['type']]); ?>",
        eurl= '<?php echo Url::to(['ad/editlist','nid'=>$_REQUEST['nid'],'type'=>$_REQUEST['type']]);?>';
</script>
<script src="../js/handle_ad_data.js"></script>