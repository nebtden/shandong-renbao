<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
    <div class="page-header am-fl am-cf">
        <h4>广告管理 <small>&nbsp;/&nbsp;编辑广告</small></h4>
    </div>
    <div class="container-fluid" style="padding-top: 15px;height:auto;">
        <form class="form-horizontal"   method="post" action="<?php echo Url::to(['ad/editlist','nid'=>$_REQUEST['nid'],'type'=>$_REQUEST['type']]) ?>" >
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">广告标题</label>
                <div class="col-sm-3">
                    <input type="text" name="title" class="form-control" value="<?php echo $data['title'];?>" id="inputEmail3" placeholder="广告标题">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
                <div class="col-sm-3">
                    <input type="text"  id="inputError1" for="inputError1" name="sort" class="form-control" id="inputPassword3" value="<?php echo $data['sort']?$data['sort']:1;?>" pattern="^\d+$" oninput="check_lhb(this,'只能填写数字')" placeholder="排序">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">广告图片</label>
                <div class="col-sm-3">

                    <div id="hadpic"></div>
                    <input type="hidden" name="picurl" id="picurl" value="<?php echo $data['picurl'];?>"/>
                    <?php
                    if($data['picurl']){
                        echo '<img width="130" src="'.$data['picurl'].'" />';
                    }
                    ?>
                    <p class="help-block"> </p>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">广告位置</label>
                <div class="col-sm-3">
                    <select name="nid"  id="nid">
                        <option value="0">--请选择--</option>
                        <?php foreach($cates as $v){ ?>
                            <option value="<?php echo $v['id'] ?>"  <?php if($v['id']==$data['nid']) echo 'selected'; ?>><?php echo $v['name']; ?></option>
                        <?php  }?>
                        </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">链接地址</label>
                <div class="col-sm-3">
                    <input type="text"  id="inputError1" for="inputError1" name="url" class="form-control" id="inputPassword3" value="<?php echo $data['url']?$data['url']:'';?>"  placeholder="排序">
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
            uploadImg('hadpic','picurl');
           // uploadImg_r2('picList','showpic',100,100);
            var ue = UE.getEditor('content');
        });
        $('form').submit(function(){
            var name=$('input[name="title"]').val();
            if(name.length==0){
                $('input[name="title"]').parent().parent().addClass('has-error');
                return false;
            }
            else
            {
                $('input[name="title"]').parent().parent().removeClass('has-error');

            }
           var nid=$('#nid').val();
            if(nid==0){
                $('#nid').parent().parent().addClass('has-error');
                return false;
            }
            return true;


        });

    </script>
<?php

?>