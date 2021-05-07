<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="container-fluid" style="padding-top: 15px;height:800px;">
    <form class="form-horizontal" method="post" action="<?php echo Url::to(['authority/group_edit']); ?>" data-am-validator >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">名称</label>
            <div class="col-sm-3">
                <input type="text" name="group_name" class="form-control" value="<?php echo $group['group_name'];?>" id="inputEmail3" placeholder="名称">
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-3">
                <input type="text"  name='sort' for="inputError1"  class="form-control" id="inputPassword3" value="<?php echo $group['sort']?$group['sort']:1;?>" pattern="^\d+$" oninput="check_lhb(this,'只能填写数字')" placeholder="排序">
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">操作模块:</label>
            <div class="col-sm-3" >
                <?php $i=1;foreach ($menu as $k=>$v) {?>
                    <div class="allbox">
                        <div class="options">
                            <input <?php if(in_array($k,$modules)){ echo 'checked';} ?> name="module" type="checkbox" class="am-checkbox-inline" value="<?php echo $k; ?>">
                            <?php  echo $k;?>
                        </div>
                        <div class="option-item" style="padding-left:20px; display: flex; white-space: nowrap; flex-wrap: wrap;">
                            <div>
                                <?php $j=1; foreach ($v['subs'] as $key=>$value){?>
                                    <span>
                                        <input <?php if(in_array($key,$subdirectory)){ echo 'checked';} ?>  name="subdirectory" type="checkbox" class="am-checkbox-inline" value="<?php echo $key;?>">
                                        <?php  echo $key.'&nbsp;&nbsp;&nbsp;&nbsp;';?>
                                    </span>
                                <?php if( $j%2 == 0 && count($v['subs']) > 2 ) echo '</div><div>';$j++; }?>
                            </div>
                        </div>
                    </div>
                <?php if($i%2==0) echo '</br>';$i++;}?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" class="btn btn-default" onclick="history.go(-1);">返回</button>&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-info">保存</button>
            </div>
        </div>
        <?php  if($_REQUEST['id']) {?>
            <input type="hidden"  name="id"  value="<?php echo $_REQUEST['id']; ?>"><?php }?>
        <input name="act"  type="hidden" value="<?php if($_REQUEST['id']) {echo 'edit';}else {echo 'add';}?>">
    </form>
</div>
<script>


    $(function(){
//        全选
        $('.options .am-checkbox-inline').click(function () {
            if($(this).is(':checked')){
                $(this).parents('.allbox').find('.option-item').find('.am-checkbox-inline').prop("checked",true)
            }else{
                $(this).parents('.allbox').find('.option-item').find('.am-checkbox-inline').prop("checked",false)
            }
        })

        var $_len = $('.option-item').find('.am-checkbox-inline')
        $_len.each(function(){
            $(this).click(function(){
                if ($(this).is(':checked')){
                    $(this).parents('.allbox').find('.options').children('input').prop("checked",true)
                }else{
                    //判断：所有单个是否勾选
                    var len = $(this).parents('.allbox').find('.option-item').find('.am-checkbox-inline').length;
                    var num = len;
                    $(this).parents('.allbox').find('.option-item').find('.am-checkbox-inline').each(function () {
                        if ($(this).is(':checked') == false) {
                            num--;
                        }
                    });
                    if (num == 0) {
                        $(this).parents('.allbox').find('.options').children('input').prop("checked",false)
                    }

                }
            })
        })

    });

    $('.col-sm-offset-2 .btn-info') .click(function (){
        var group_name   = $("input[name=group_name]").val();
        var sort         = $("input[name=sort]").val();
        var id           = $("input[name=id]").val();
        var _this        = ".col-sm-3 .options .am-checkbox-inline:checked";
        var modules      =  '[';
        var num          = 0;
        var length=$(_this).length;
        $(_this).each(function (index, item) {
            modules += '{"modules":"'+$(this).val() + '",';
            $(this).parents('.allbox').find('.option-item').find('.am-checkbox-inline:checked').each(function (key,val) {
                num = $(this).parents('.allbox').find('.option-item').find('.am-checkbox-inline:checked').length;
                if(index == length - 1){
                    if(key == 0 && num - 1 == 0){
                        modules += '"subdirectory":["'+$(this).val() + '"]}';
                    }else if(key == 0){
                        modules += '"subdirectory":["'+$(this).val() + '",';
                    }else if(key == num - 1){
                        modules += '"'+$(this).val() + '"]}';
                    }else{
                        modules += '"'+$(this).val() + '",';
                    }
                }else{
                    if(key == 0 && num - 1 == 0){
                        modules += '"subdirectory":["'+$(this).val() + '"]},';
                    }else if(key == 0){
                        modules += '"subdirectory":["'+$(this).val() + '",';
                    }else if(key == num - 1){
                        modules += '"'+$(this).val() + '"]},';
                    }else{
                        modules += '"'+$(this).val() + '",';
                    }
                }
            });

        });
        modules  +=  ']';
        $.post('<?php echo Url::to(["authority/group_edit"]); ?>',{
            id:id,
            group_name:group_name,
            sort:sort,
            modules : modules

        },function(json){
            alert(json.msg);
            //window.parent.frames[1].location.reload()
            if(json.status == 2) window.location.href = json.url;
            //window.location.reload();

        },'json');
    });
    
</script>