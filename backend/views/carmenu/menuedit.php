<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8 0008
 * Time: 上午 10:33
 */
use yii\helpers\Url;
use yii\helpers\Html;
?>
    <div class="page-header am-fl am-cf">
        <h4>目录管理 <small>&nbsp;/&nbsp;编辑目录</small></h4>
    </div>
    <div class="container-fluid" style="padding-top: 15px;height:auto;">
        <form class="form-horizontal"   method="post" action="<?php echo Url::to(['carmenu/menuedit'])?>" >
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">目录名称</label>
                <div class="col-sm-3">
                    <input type="text" name="menu_name" class="form-control" value="<?php echo $data['menu_name']?$data['menu_name']:'';?>" id="inputEmail3" placeholder="目录名称">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
                <div class="col-sm-3">
                    <input type="text"  id="inputError1" for="inputError1" name="sort" class="form-control"  value="<?php echo $data['sort']?$data['sort']:1;?>" pattern="^\d+$" oninput="check_lhb(this,'只能填写数字')" placeholder="排序">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">目录图标</label>
                <div class="col-sm-3">

                    <div id="hadpic"></div>
                    <input type="hidden" name="menu_img" id="menu_img" value="<?php echo $data['menu_img'];?>"/>
                    <?php
                    if($data['menu_img']){
                        echo '<img width="130" src="'.$data['menu_img'].'" />';
                    }
                    ?>
                    <p class="help-block"> </p>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">链接地址</label>
                <div class="col-sm-3">
                    <input type="text"  id="inputError1" for="inputError1" name="menu_url" class="form-control" id="inputPassword3" value="<?php echo $data['menu_url']?$data['menu_url']:'';?>"  placeholder="链接地址">
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
            uploadImg('hadpic','menu_img');
        });
    </script>