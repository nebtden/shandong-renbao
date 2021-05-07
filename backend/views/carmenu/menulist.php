<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8 0008
 * Time: 上午 10:33
 */
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>首页管理 <small>&nbsp;/&nbsp;目录列表</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['carmenu/menuedit']);?>';">
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
        <th  data-field="menu_name">目录名称</th>
        <th  data-field="menu_img">缩略图</th>
        <th  data-field="menu_url">链接地址</th>
        <th  data-field="c_time">添加时间</th>
        <th  data-field="sort">排序</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>

<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,
        listurl='<?php echo Url::to(['carmenu/menulist']);?>';
    var durl="<?php echo Url::to(['carmenu/dellist']); ?>",
        upturl="",
        eurl= '<?php echo Url::to(['carmenu/menuedit']);?>';
</script>
<script src="../js/handle_car_menu.js"></script>