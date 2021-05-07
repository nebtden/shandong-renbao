<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<?=Html::cssFile('../diyUpload/css/webuploader.css');?>
<?=Html::cssFile('../diyUpload/css/diyUpload.css');?>
<?=Html::jsFile('../diyUpload/js/webuploader.html5only.min.js') ?>
<?=Html::jsFile('../diyUpload/js/diyUpload.js')?>
<?=Html::jsFile('../js/my.js');?>

<div class="container-fluid" style="padding-top: 15px;height:800px;">
<form class="form-horizontal" method="post" action="<?php echo Url::to(['news/category_edit']); ?>" data-am-validator >
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">名称</label>
        <div class="col-sm-3">
            <input type="text" name="name" class="form-control" value="<?php echo $category['name'];?>" id="inputEmail3" placeholder="名称">
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">上级分类</label>
        <div class="col-sm-3">
            <select name="pid" data-am-selected="{ btnWidth: '100%'}"   class="form-control">
                <option value="0">顶级分类</option>
                <?php
                function listCates($cates,$list,$i = 0,$category){
                    $str = str_repeat('- - ',$i); ;
                    foreach($list as $v){
                        if($category['pid']==$v['id'])
                            echo '<option value="'.$v['id'].'" selected="selected">'.$str.$v['name'].'</option>';
                        else
                            echo '<option value="'.$v['id'].'">'.$str.$v['name'].'</option>';
                        if(is_array($cates[$v['id']]))listCates($cates,$cates[$v['id']],$i+1,$category);
                    }
                }
                listCates($cates,$cates[0],0,$category);
                ?>
            </select>
        </div>
    </div>
   
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
        <div class="col-sm-3">
            <input type="text"  name='sort' id="inputError1" for="inputError1"  class="form-control" id="inputPassword3" value="<?php echo $category['sort']?$category['sort']:1;?>" pattern="^\d+$" oninput="check_lhb(this,'只能填写数字')" placeholder="排序">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>    <button type="submit" class="btn btn-default">保存</button>
        </div>
    </div>
    <?php  if($_REQUEST['id']) {?>
    <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>"><?php }?>
    <input name="act"  type="hidden" value="<?php if($_REQUEST['id']) {echo 'edit';}else {echo 'add';}?>">
</form>
    </div>
<script>
    $(function(){
        uploadImg('hadpic','pic');
        uploadImg_r2('picList','showpic',100,100);
    });

    $('form').submit(function(){
        var name=$('input[name="name"]').val();
        if(name.length==0){
            $('input[name="name"]').parent().parent().addClass('has-error');
            return false;
        }
        else
        {
            $('input[name="name"]').parent().parent().removeClass('has-error');
        }

        return true;

    });

</script>