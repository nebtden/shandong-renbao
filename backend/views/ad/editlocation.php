<?php
use yii\helpers\Url;
?>
<div class="page-header am-fl am-cf">
    <h4>广告管理 <small>&nbsp;/&nbsp;编辑广告位置</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['ad/editlocation']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">广告位置名称</label>
            <div class="col-sm-3">
                <input type="text" name="name" class="form-control" value="<?php echo $data['name'];?>" id="inputEmail3" placeholder="名称">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">广告类型</label>
            <div class="col-sm-3">
                <select name="type" >
                    <option value="0">--请选择--</option>
                    <?php foreach($cates as $k=> $v){
                        if($k>0){?>
                <option value="<?php echo $k; ?>"  <?php if($k==$data['type']) echo 'selected'; ?>><?php echo $v; ?></option>
                    <?php } }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">广告宽度</label>
            <div class="col-sm-3">
                <input type="text" name="width" class="form-control" value="<?php echo $data['width'];?>" id="inputEmail3" placeholder="名称">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">广告高度</label>
            <div class="col-sm-3">
                <input type="text" name="height" class="form-control" value="<?php echo $data['height'];?>" id="inputEmail3" placeholder="名称">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">播放状态</label>
            <div class="col-sm-3">
                开启<input name="show" value="1" type="radio" <?php if($data['show']==1)  echo 'checked';?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                关闭<input name="show" value="0" type="radio" <?php if($data['show']==0)  echo 'checked';?>>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
        <?php  if($_REQUEST['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>"><?php }?>
    </form>
</div>