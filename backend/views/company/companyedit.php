<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 上午 11:03
 */

use yii\helpers\Url;
use yii\helpers\Html;
use Faker\date;
?>
<div class="page-header am-fl am-cf">
    <h4>优惠券管理 <small>&nbsp;/&nbsp;优惠券编辑</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['company/companyedit']) ?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">公司名称</label>
            <div class="col-sm-3">
                <input type="text" required placeholder="公司名称" name="name"   value="<?php  echo $info['name']; ?>" class="form-control"  />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">网页标题</label>
            <div class="col-sm-3">
                <input type="text" required placeholder="网页标题" name="webpage_title"   value="<?php  echo $info['webpage_title']; ?>" class="form-control"  />
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">背景图片</label>
            <div class="col-sm-3">
                <div id="hadpic"></div>
                <input type="hidden" name="background_pic" id="background_pic" value="<?php echo $info['background_pic'];?>"/>
                <?php
                if($info['background_pic']){
                    echo '<img width="414" src="'.$info['background_pic'].'" />';
                }
                ?>
                <p class="help-block"> </p>
            </div>
        </div>
            <input type="hidden"  name="id"  value="<?php echo $info['id']; ?>">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>
                <button type="submit" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>

<script>
    $(function(){
        uploadImg('hadpic','background_pic');
    });
</script>

