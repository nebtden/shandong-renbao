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
    <h4>推荐管理 <small>&nbsp;/&nbsp;推荐列表</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">

        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['carmenu/recommendedit']);?>';">
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
        <th  data-field="ad_title">推荐标题</th>
        <th  data-field="ad_pic">缩略图</th>
        <th  data-field="ad_url">链接地址</th>
        <th  data-field="workoff_num">售出数量</th>
        <th  data-field="praise_rate">好评率</th>
        <th  data-field="discount">折扣</th>
        <th  data-field="market_price">市场价</th>
        <th  data-field="discount_price">折扣价</th>
        <th  data-field="c_time">添加时间</th>
        <th  data-field="sort">排序</th>

        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>

<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,
        listurl='<?php echo Url::to(['carmenu/recommendlist']);?>';
    var durl="<?php echo Url::to(['carmenu/delrelist']); ?>",
        upturl="",
        eurl= '<?php echo Url::to(['carmenu/recommendedit']);?>';
</script>
<script src="../js/handle_car_recommend.js"></script>