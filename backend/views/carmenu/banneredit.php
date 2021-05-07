<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11 0011
 * Time: 上午 11:22
 */

use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="page-header am-fl am-cf">
    <h4>轮播管理 <small>&nbsp;/&nbsp;编辑轮播</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:auto;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['carmenu/banneredit'])?>" >
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">图片</label>
            <div class="col-sm-3">
                <div id="hadpic"></div>
                <input type="hidden" name="b_pic" id="b_pic" value="<?php echo $data['b_pic'];?>"/>
                <?php
                if($data['b_pic']){
                    echo '<img width="130" src="'.$data['b_pic'].'" />';
                }
                ?>
                <p class="help-block"> </p>
            </div>
        </div>
        <div class="form-group">
            <label for="sort" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-3">
                <input type="text"  id="sort" name="sort" class="form-control" value="<?php echo $data['sort']?$data['sort']:'1';?>" pattern="^\d+$" oninput="check_lhb(this,'只能填写数字')"   placeholder="排序">
            </div>
        </div>
        <div class="form-group">
            <label for="sort" class="col-sm-2 control-label">链接</label>
            <div class="col-sm-3">
                <input type="text"  id="url" name="url" class="form-control" value="<?php echo $data['url']?$data['url']:'';?>" placeholder="链接">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
        <?php  if($_REQUEST['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>">
        <?php }?>
    </form>
</div>
<script>
    $(function(){
        uploadImg('hadpic','b_pic');
    });
</script>