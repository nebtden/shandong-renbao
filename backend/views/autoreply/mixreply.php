<?php
use yii\helpers\Url;
?>
<style>
    <!--
    input{
        width:400px;
    }
    textarea{
        width:400px;
    }
    -->
</style>
<div class="page-header am-fl am-cf">
    <h4>微信自动回复 <small>&nbsp;/&nbsp;图文回复-修改</small></h4>
</div>

<div class="container-fluid">

    <form class="form-horizontal" method="post" action="<?php echo Url::to(['/autoreply/mixedit/','id'=>$id]) ?>" data-am-validator  >
        <div class="ac-1">
            <?php foreach($rows as $k=>$data) { ?>
                <div class="form-group">
                    <?php if($k==0) {?>
                        <label for="inputEmail3" class="col-sm-2 control-label">关键词</label>
                        <div style="width:10%;float:left;">
                            <input type="text"  readonly  name="keyword"  class="am-form-field am-round"  value="<?php echo $data['keywords'];?>" placeholder="输入关键词"  required>
                        </div>
                    <?php }?>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">标题</label>
                    <div  style="width:25%;float:left;">
                        <input type="text"  name="title[]"  placeholder="标题" value="<?php echo $data['title'];?>" required>
                    </div>
                </div>
                <?php if($k==0) {?>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">摘要</label>
                        <div  style="width:25%;float:left;">
                            <textarea rows="6" name="details" maxlength="100" class="input-xlarge validate[required,maxSize[100]]" required><?php echo $data['details'];?></textarea>
                        </div>
                    </div>
                <?php }?>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">链接</label>
                    <div  style="width:25%;float:left;">
                        <input type="text"  name="url[]" value="<?php echo $data['url'];?>"  placeholder="链接URL" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">封面</label>
                    <div style="width: 30%;float:left;" >
                        <div id="hadpic<?php echo $k; ?>"></div>
                        <input type="hidden" name="hadimg<?php echo $k; ?>" id="hadimg<?php echo $k; ?>" value="<?php echo $data['imageurl'];?> " required/>
                        <?php if($data['imageurl']){?><img  src="<?php echo $data['imageurl'];?>" class="am-thumbnail" width="100px" height="100px">
                            <input name="imageurl[]"  type="hidden" value="<?php echo $data['imageurl'];?>">
                        <?php }?>
                    </div>

                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">排序</label>
                    <div  style="width:4%;float:left;margin-bottom: 10px;">

                        <input type="hidden"  name="pid[]" value="<?php echo $data['pid']; ?>">

                        <input type="text" name="order[]"  <?php if($k!=0) echo 'onmouseout="this.value=parseInt(this.value);"  pattern="^[1-9]{1}\d*$"' ;?>   <?php if($k==0) echo 'readonly "' ;?>   value="<?php echo $data['order']; ?>">

                    </div>
                </div>
                <input type="hidden" name="id[]" value="<?php echo $data['id']; ?>">
            <?php } ?>
        </div>
        <div  style="width:40%;margin-left:15%;" class=" pad-1">
            <button type="submit" class="am-btn am-btn-primary">
                <?php  if(!isset($act)) echo '添加'; else  echo '修改';?>
            </button>
            <a id="add-ac-con">+添加一条</a>
        </div>
        <input type="hidden" name="act" value="<?php echo $act; ?>">
    </form>
    <div class="am-u-sm-4"></div>
</div>

<script>
    $(function() {
        var  $nav = $('.ac-1');
        var  bcount=<?php echo count($rows);?>-1;
        <?php foreach($rows as $k=>$data) {?>
        uploadImg('hadpic<?php echo $k;?>','hadimg<?php echo $k;?>');
        <?php }?>
        function addTab() {
            bcount++;
            var  strf='<div class="form-group plus_b"  style="border:1px solid pink;margin:5px;padding:10px;clear:left;line-height: 25px;"><a  id="delplus"  inex="'+bcount+'"  style="margin-top:0px;float:right;right:10%;" >删除<a>';
            var  str1='<div class="form-group"> <label for="inputEmail3" class="col-sm-2 control-label">标题</label> <div  style="width:25%;float:left;"> <input type="text"  name="title[]"  placeholder="标题" required> </div> </div>';
            var  str2='<div class="form-group"> <label for="inputEmail3" class="col-sm-2 control-label">链接</label> <div  style="width:25%;float:left;"> <input type="text"  name="url[]"   placeholder="链接URL" required> </div> </div>';
            var  str3=' <div class="form-group"> <label  for="inputEmail3" class="col-sm-2 control-label">封面</label> <div style="width: 30%;float:left;" > <div id="hadpic'+bcount+'" ></div> <input type="hidden" name="hadimg'+bcount+'" id="hadimg'+bcount+'" value="" required/> </div> </div>' ;
            var  str4=' <div class="form-group"> <label for="inputEmail3" class="col-sm-2 control-label">排序</label> <div  style="width:4%;float:left;"> <input type="text" name="order[]" value="1" pattern="^[1-9]{1}\d*$" onmouseout="this.value=parseInt(this.value);" required data-foolish-msg="大于0！"  > </div> </div>';
            var  stre='<input type="hidden" name="id[]" value="<?php echo $id; ?>"></div>';
            var  content=strf+str1+str2+str3+str4+stre;
            $nav.append(content );
            uploadImg('hadpic'+bcount,'hadimg'+bcount);
        }

        // 动态添加标签页   // 移除标签页

        $('#add-ac-con').on('click', function() {
            addTab();
            $('.plus_b').each(function(){
                $(this).find('#delplus').click(function(){
                    $(this).parent().remove();
                });
            });
        });



    });
</script>
<style type="text/css">
    .parentFileBox{margin-left:197px;}
    .fixed-layout .admin-main{overflow: scroll;}
    .labc{float:left;}

</style>