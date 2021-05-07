<?php
use yii\helpers\Url;
?>
<style>
<!--
.form-group {
  margin-bottom: 8px;
}
input{
    width:400px;
}
textarea{
    width:400px;
}

-->
</style>
<div class="page-header am-fl am-cf">
    <h4>微信自动回复 <small>&nbsp;/&nbsp;图文回复-<?php  if($_REQUEST['id']) echo '修改' ;else echo '添加';?></small></h4>
</div>
<div class="container-fluid" style="padding-top: 15px;height:800px;">

    <form class="form-horizontal"   method="post" action="<?php echo Url::to(['autoreply/mixreply']);?>"  data-am-validator  id="form">

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">关键词</label>
                        <div style="width:10%;float:left;">
                            <input type="text"  name="keyword"  class="am-form-field am-round"  value="<?php echo $data['keywords'];?>" placeholder="输入关键词"  required>
                        </div>
                    </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">标题</label>
                        <div  style="width:25%;float:left;">
                            <input type="text"  name="title[]"  placeholder="标题" value="<?php echo $data['title'];?>" required>
                        </div>
                    </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">摘要</label>
                        <div  style="width:25%;float:left;">
                            <textarea rows="6" name="details" maxlength="100" class="input-xlarge validate[required,maxSize[100]]" required><?php echo $data['details'];?></textarea>
                        </div>
                    </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">链接</label>
                        <div  style="width:25%;float:left;">
                            <input type="text"  name="url[]" value="<?php echo $data['url'];?>"  placeholder="链接URL" required>
                        </div>
                    </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">封面</label>
                        <div style="width: 30%;float:left;" >
                            <div id="hadpic0"   ></div>
                            <input type="hidden" name="hadimg0" id="hadimg0" value="" required/>
                            <?php if($data['imageurl']){?><img  src="<?php echo $data['imageurl'];?>" class="am-thumbnail" width="100px" height="100px">
                                <input name="imageurl"  type="hidden" value="<?php echo $data['imageurl'];?>">
                            <?php }?>
                        </div>

                    </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">排序</label>
                        <div  style="width:4%;float:left;margin-bottom: 10px;">
                            <input type="text" name="order[]" value="0"  readonly required>
                        </div>
                    </div>

                <div  style="width:40%;margin-left:15%;" class=" pad-1">  <button type="submit" class="am-btn am-btn-primary">
                        <?php  if(!isset($data['act'])) echo '添加'; else  echo '修改';?>
                    </button>   <a id="add-ac-con">+添加一条</a> </div>

                <input type="hidden" name="act" value="<?php echo $data['act']; ?>">
                <input type="hidden" name="rid" value="<?php echo $data['rid']; ?>">
            </form>

            <div class="am-u-sm-4" >



            </div>


        </div>

    <script>
        $(function() {

            var  $nav = $('.form-group').last();
            var  bcount=0;

            uploadImg('hadpic0','hadimg0');
            function addTab() {
                bcount++;
                var  strf='<div class="form-group plus_b"  style="border:1px solid pink;margin:5px;padding:10px;clear:left;line-height: 25px;"><a  id="delplus"  inex="'+bcount+'"  style="margin-top:0px;float:right;right:10%;" >删除<a>';
                var  str1='<div class="form-group"> <label for="inputEmail3" class="col-sm-2 control-label">标题</label> <div  style="width:25%;float:left;"> <input type="text"  name="title[]"  placeholder="标题" required> </div> </div>';
                var  str2='<div class="form-group"> <label for="inputEmail3" class="col-sm-2 control-label">链接</label> <div  style="width:25%;float:left;"> <input type="text"  name="url[]"   placeholder="链接URL" required> </div> </div>';
                var  str3=' <div class="form-group"> <label  for="inputEmail3" class="col-sm-2 control-label">封面</label> <div style="width: 30%;float:left;" > <div id="hadpic'+bcount+'" ></div> <input type="hidden" name="hadimg'+bcount+'" id="hadimg'+bcount+'" value="" required/> </div> </div>' ;
                var  str4=' <div class="form-group"> <label for="inputEmail3" class="col-sm-2 control-label">排序</label> <div  style="width:4%;float:left;"> <input type="text" name="order[]" value="1" pattern="^[1-9]{1}\d*$" onmouseout="this.value=parseInt(this.value);" required data-foolish-msg="大于0！"  > </div> </div>';
                var  stre='</div>';
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