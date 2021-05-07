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
    <h4>推荐管理 <small>&nbsp;/&nbsp;编辑推荐内容</small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:auto;">
    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['carmenu/recommendedit'])?>" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">推荐标题</label>
            <div class="col-sm-3">
                <input type="text" name="ad_title" class="form-control" value="<?php echo $data['ad_title']?$data['ad_title']:'';?>" id="inputEmail3" placeholder="推荐标题">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">图片</label>
            <div class="col-sm-3">

                <div id="hadpic"></div>
                <input type="hidden" name="ad_pic" id="ad_pic" value="<?php echo $data['ad_pic'];?>"/>
                <?php
                if($data['ad_pic']){
                    echo '<img width="130" src="'.$data['ad_pic'].'" />';
                }
                ?>
                <p class="help-block"> </p>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">链接地址</label>
            <div class="col-sm-3">
                <input type="text"  id="ad_url" for="inputError1" name="ad_url" class="form-control"  value="<?php echo $data['ad_url']?$data['ad_url']:'';?>" placeholder="排序">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">售出数量</label>
            <div class="col-sm-3">
                <input type="number"  id="workoff_num" for="inputError1" name="workoff_num" class="form-control" id="inputPassword3" value="<?php echo $data['workoff_num']?$data['workoff_num']:'0';?>"  placeholder="售出数量">
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">好评率%</label>
            <div class="col-sm-3">
                <input type="number"  id="praise_rate" for="inputError1" name="praise_rate" class="form-control" id="inputPassword3" value="<?php echo $data['praise_rate']?$data['praise_rate']:'';?>"  placeholder="好评率">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">折扣</label>
            <div class="col-sm-3">
                <input type="text"  id="discount" for="inputError1" name="discount" class="form-control" id="inputPassword3" value="<?php echo $data['discount']?$data['discount']:'';?>"  placeholder="折扣">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">市场价</label>
            <div class="col-sm-3">
                <input type="text"  id="market_price" for="inputError1" name="market_price" class="form-control" id="inputPassword3" value="<?php echo $data['market_price']?$data['market_price']:'';?>"  placeholder="市场价">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">折扣价</label>
            <div class="col-sm-3">
                <input type="text"  id="discount_price" for="inputError1" name="discount_price" class="form-control" id="inputPassword3" value="<?php echo $data['discount_price']?$data['discount_price']:'';?>"  placeholder="折扣价">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-3">
                <input type="text"  id="sort" for="inputError1" name="sort" class="form-control" id="inputPassword3" value="<?php echo $data['sort']?$data['sort']:'0';?>" pattern="^\d+$" oninput="check_lhb(this,'只能填写数字')"   placeholder="排序">
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
        uploadImg('hadpic','ad_pic');
    });
</script>