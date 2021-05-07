<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6 0006
 * Time: 上午 9:26
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>新闻管理 <small>&nbsp;/&nbsp;新闻列表</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['carnews/newsedit']);?>';">
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
        <th class="table-check" data-field="id">新闻ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="title">新闻标题</th>
        <th  data-field="short_desc">内容摘要</th>
        <th  data-field="browse_number">浏览数</th>
        <th  data-field="point_number">点赞数</th>
        <th  data-field="sort">排序</th>
        <th  data-field="c_time">添加时间</th>
        <th  data-field="status">状态</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>

<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,
        listurl='<?php echo Url::to(['carnews/newslist']);?>';
    var durl="<?php echo Url::to(['carnews/dellist']); ?>",
        upturl="",
        eurl= '<?php echo Url::to(['carnews/newsedit']);?>';
</script>
<script src="../js/handle_car_news.js"></script>