<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/6 0006
 * Time: 下午 4:22
 */

use yii\helpers\Url;
use yii\helpers\Html;
?>

<div class="page-header am-fl am-cf">
    <h4>视频管理 <small>&nbsp;/&nbsp;编辑信息</small></h4>
</div>

<div class="container-fluid" style="padding-top: 15px;height:auto;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['calendarad/videoedit'])?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">标题</label>
            <div class="col-sm-3">
                <input type="text" name="title" class="form-control" value="<?php echo $data['title']?$data['title']:'';?>" id="inputEmail3" placeholder="标题">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-3">
                <input type="number"  id="inputError1" for="inputError1" name="sort" class="form-control"  value="<?php echo $data['sort']?$data['sort']:1;?>" pattern="^\d+$" oninput="check_lhb(this,'只能填写数字')" placeholder="排序">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">视频地址</label>
            <div class="col-sm-3">
                <input type="text" name="path" class="form-control" value="<?php echo $data['path']?$data['path']:'';?>" id="inputEmail3" placeholder="视频地址">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">封面</label>
            <div class="col-sm-3">
                <div id="hadpic"></div>
                <input type="hidden" name="pic" id="pic" value="<?php echo $data['pic'];?>"/>
                <?php
                if($data['pic']){
                    echo '<img width="414" src="'.$data['pic'].'" />';
                }
                ?>
                <p class="help-block"> </p>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">简介</label>
            <div class="col-sm-3">
                <textarea required name="introduction" id="introduction" style="width: 800px;height: 400px;" placeholder="简介"><?php echo $data['introduction'];?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">公司</label>
            <div class="col-sm-3">
                <select id="company_id" name="company_id"  placeholder="公司"  class="form-control">
                    <?php foreach ($company as $key => $val):?>
                        <option value="<?=$val['id']?>" <?php if($data['company_id']==$val['id']) echo 'selected'; ?>><?=$val['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">是否显示简介</label>
            <div class="col-sm-3">
                <select id="show_desc" name="show_desc"  placeholder="是否显示简介"  class="form-control">
                    <?php foreach ($show_desc as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['show_desc'] == $key){echo 'selected';}?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">状态</label>
            <div class="col-sm-3">
                <select id="status" name="status"  placeholder="状态"  class="form-control">
                    <?php foreach ($status as $key => $val):?>
                        <option value="<?=$key?>" <?php if($data['status'] == $key){echo 'selected';}elseif($key == 1){echo 'selected';}?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
        <?php  if($data['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $data['id']; ?>">
        <?php }?>
    </form>


</div>
<script>
    $(function(){
        uploadImg('hadpic','pic');
    });
</script>