<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/13 0013
 * Time: 下午 3:08
 */

use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>旅游日期管理 <small>&nbsp;/&nbsp;日期编辑或添加</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['travel/edit-date']) ?>" >
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">旅游路线</label>
            <div class="col-sm-3">
                <select  name="luxian"  placeholder="旅游路线"  class="form-control">
                    <?php foreach ($luxianlist as $key =>$val){?>
                        <option value="<?php echo $key?>" <?php if($info['travel_list_id']==$key) echo 'selected'; ?>><?php echo $val?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">设置出行开始日期</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" name="date" id="date" value="<?php  echo $info['date']; ?>" readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="出行开始日期">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">设置出行截止日期</label>
            <div class="col-sm-3">
                <input type="text" class=" form-control" name="end" id="end" value="<?php  echo $info['end']; ?>" readonly="readonly" onclick="laydate({istime: true, format: 'YYYY-MM-DD'})" placeholder="出行截止日期">
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">设置出行人数</label>
            <div class="col-sm-3">
                <input type="number"  name="number" id="number" value="<?php  echo $info['number']; ?>" class="form-control"  placeholder="出行人数" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">上下架</label>
            <div class="col-sm-3">
                <select  name="status" id="status"  placeholder="上下架"  class="form-control">
                    <?php foreach ($status as $key =>$val){?>
                        <option value="<?php echo $key?>" <?php if($info['status']==$key) echo 'selected'; ?>><?php echo $val?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <?php  if($info['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $info['id']; ?>">
        <?php }?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>
<script src="../js/laydate/laydate.js" type="text/javascript"></script>


