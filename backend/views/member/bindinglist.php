<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 上午 9:42
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
    <h4>绑定车牌管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline" id="upfh">
        <div class="form-group">
            <select name="card_province" id="card_province" class="form-control">
                <option value="">选择所属省份简称</option>
                <?php foreach ($province as $key => $val):?>
                    <option value="<?=$val?>" <?php if($data['card_province']==$val) echo 'selected'; ?>><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select name="card_char" id="card_char" class="form-control">
                <option value="">选择车牌单字母</option>
                <?php foreach ($zimu as $key => $val):?>
                    <option value="<?=$val?>" <?php if($data['card_char']==$val) echo 'selected'; ?>><?=$val?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <select name="card_brand" id="card_brand" class="form-control">
                <option value="">选择汽车品牌</option>
                <?php foreach ($car_brand as $key => $val):?>
                    <option value="<?=$val['name']?>" <?php if($data['card_brand']==$val['name']) echo 'selected'; ?>><?=$val['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group"><input type="number" id="user_id" name="user_id"  class="form-control" placeholder="用户ID"></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
    <tr>
        <th class="table-check" data-field="id">编号ID
        </th>
        <th  data-field="uid">用户ID</th>
        <th  data-field="card_type">车牌类型</th>
        <th data-field="card_province">所属省份简称</th>
        <th data-field="card_char">车牌单字母</th>
        <th data-field="card_no">车牌号码</th>
        <th data-field="card_brand">汽车品牌</th>
        <th data-field="status">状态</th>
        <th data-field="c_time">创建时间</th>
        <th data-field="u_time">最后修改时间</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    </tr>
    </thead>
</table>
<script type="text/javascript">
    var type='id',
        stext,
        order='desc',
        ids='',
        imgcon=1,
        more,
        eurl="<?php echo Url::to(['member/bindingedit']);?>",
        listurl='<?php echo Url::to(['member/bindinglist']); ?>',
        durl="";
</script>
<script src="../js/handle_car_binding.js" ></script>



