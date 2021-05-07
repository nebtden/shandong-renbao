<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>
        支付配置 <small>&nbsp;&nbsp;/列表</small>
    </h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href='<?php echo Url::to(['payment/edit']);?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
    </form>
</div>
<table class="table table-bordered table-condensed table-responsive"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th  data-field="pay_type">支付方式
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th data-field="account">支付账号</th>
        <th data-field="payment_key">PartnerKey</th>
        <th data-field="pass_key">PaySignKey</th>
        <th data-field="status">状态</th>
        <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
        <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
        <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
        <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
        <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
        <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
    </tr>
    </thead>
</table>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['payment/edit']);?>",
        listurl='<?php echo Url::to(['payment/index']); ?>',
        durl="<?php echo Url::to(['payment/del']); ?>",height = $(window).height()-120;
</script>
<script src="../js/handle_data.js" ></script>



