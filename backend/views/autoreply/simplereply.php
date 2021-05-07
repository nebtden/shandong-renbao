<?php
use yii\helpers\Url;
?>

<div class="page-header am-fl am-cf">
    <h4>微信自动回复 <small>&nbsp;/&nbsp;文字回复-<?php  if($_REQUEST['id']) echo '修改' ;else echo '添加';?></small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal"   method="post" action="<?php  if($_REQUEST['id']) echo Url::to(['autoreply/simpleupt']);  else echo Url::to(['autoreply/simplereply']); ?>">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">关键词</label>
            <div class="col-sm-3">
                <input type="text" placeholder="关键词"  class="form-control" name="keywords" <?php if($act) {?> value="<?php echo $data['keywords']; ?>" readonly <?php }?> ></p>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">回复内容</label>
            <div class="col-sm-3">
                <?php  if($_REQUEST['id']) {?>
                    <textarea id="doc-vld-ta-2" minlength="10"  class="form-control"  placeholder="回复内容" name="details"><?php echo  trim($data['details']); ?></textarea>
                <?php } else {?>
                    <textarea id="doc-vld-ta-2" minlength="10"   class="form-control" placeholder="回复内容不少于5个字符" name="details"></textarea>
                <?php }?>

            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10" style="margin-top: 10px;">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>

                <button type="submit" class="btn btn-default">
                    <?php  if($_REQUEST['id']) {?>修改
                    <?php } else{ ?>添加
                    <?php }?></button>
            </div>
        </div>
        <?php  if($_REQUEST['id']) {?>
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
        <?php }?>
    </form>
</div>
<script>
    $('form').submit(function(){
        var keywords=$('input[name="keywords"]').val();
        if(keywords.length==0){
            $('input[name="keywords"]').parent().parent().addClass('has-error');
            return false;
        }
        else
        {
            $('input[name="keywords"]').parent().parent().removeClass('has-error');
        }
        return true;

    });

</script>