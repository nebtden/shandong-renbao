<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13 0013
 * Time: 下午 3:30
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
    <h4>免兑换手机管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <div class="form-group"><input type="text" id="mobile" name="mobile" value="<?= $getparams['mobile']?>"  class="form-control"  placeholder="手机号"></div>
        <div class="form-group"><input type="text" id="coupon_batch_no" name="coupon_batch_no" value="<?= $getparams['coupon_batch_no']?>"  class="form-control"  placeholder="券包批号"></div>
        <div class="form-group"><input type="text" id="batch_no" name="batch_no" value="<?= $getparams['batch_no']?>"  class="form-control"  placeholder="手机批号"></div>
        <div class="form-group"><input type="text" id="city" name="city" value="<?= $getparams['city']?>"  class="form-control"  placeholder="区域"></div>
        <div class="form-group">
            <select id="company_id" name="company_id"  placeholder="公司"  class="form-control">
                <option value="">选择公司</option>
                <?php foreach ($companylist as $key => $val):?>
                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/leadinmobile']);?>';"> 导入手机号</button>
        <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo Url::to(['coupon/batchnoedit']);?>';"> 批号修改</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th class="table-check" data-field="id">编号ID
            <!-- <input type="checkbox" id="allCheck"/>-->
        </th>
        <th  data-field="mobile">手机号</th>
        <th  data-field="coupon_batch_no">券包批号</th>
        <th data-field="c_time">添加时间</th>
        <th  data-field="batch_no">手机批号</th>
        <th  data-field="company_id">公司名称</th>
        <th  data-field="city">区域</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script src="../js/layer/layer.js"></script>
<script src="../js/handle_car_mobile.js" ></script>

<script type="text/javascript">
    var type='id',stext,order='desc',ids='',imgcon=1,more,eurl="<?php echo Url::to(['coupon/mobileedit']);?>",
        listurl='<?php echo Url::to(['coupon/mobilelist']); ?>',
        durl="<?php echo Url::to(['coupon/']); ?>";
</script>



