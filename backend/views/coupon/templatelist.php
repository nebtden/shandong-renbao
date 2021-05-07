<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/23 0023
 * Time: 下午 4:53
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>首页管理 <small>&nbsp;/&nbsp;目录列表</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['coupon/templateedit']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
<!--        <button type="button" class="btn btn-default" id="remove">-->
<!--            <i class="glyphicon glyphicon-trash" ></i>-->
<!--        </button>-->
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">模板ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="content">内容</th>
        <th  data-field="coupon_type">优惠券类型</th>
        <th  data-field="company">供应商</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>

<script type="text/javascript">

    var type='id',stext,order='desc',ids='',imgcon=1,more,
        listurl='<?php echo Url::to(['coupon/templatelist']);?>';
    var durl="<?php echo Url::to(['coupon/dellist']); ?>",
        upturl="",
        eurl= '<?php echo Url::to(['coupon/templateedit']);?>';
</script>
<script src="../js/handle_car_template.js"></script>