<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11 0011
 * Time: 上午 11:21
 */
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>轮播管理 <small>&nbsp;/&nbsp;轮播列表</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['carmenu/banneredit']);?>';">
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
        <th class="table-check" data-field="id">ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="b_pic">轮播图片</th>
        <th data-field="url">链接</th>
        <th data-field="c_time">添加时间</th>
        <th data-field="sort">排序</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,
        listurl='<?php echo Url::to(['carmenu/bannerlist']);?>';
    var durl="<?php echo Url::to(['carmenu/bannerdel']); ?>",
        upturl="",
        eurl= '<?php echo Url::to(['carmenu/banneredit']);?>';
</script>
<script src="../js/handle_car_banner.js"></script>