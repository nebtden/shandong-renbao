<?php

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>广告管理 <small>&nbsp;/&nbsp;广告位置</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href = '<?php echo Url::to(['ad/editlocation']); ?>';">
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
            <th class="table-check" data-field="id">编号ID
                <!-- <input type="checkbox" id="allCheck"/>-->
            </th>
            <th  data-field="name">广告位置名称</th>
            <th  data-field="type">广告类型</th>
            <th  data-field="script">广告脚本</th>
            <th  data-field="show">播放状态</th>
    <!--        <th  data-field="adlist">广告内容</th>-->
            <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var type = 'id', stext, order = 'desc', ids = '', imgcon = 1, more,
            listurl = '<?php echo Url::to(['ad/location']); ?>';
    var durl = "<?php echo Url::to(['ad/dellocation']); ?>",
            upturl = "<?php echo Url::to(['ad/editlocation']); ?>",
            eurl = '<?php echo Url::to(['ad/editlocation']); ?>';
    var checkurl = '<?php echo Url::to(['ad/list']); ?>';
</script>
<script src="../js/handle_ad.js"></script>