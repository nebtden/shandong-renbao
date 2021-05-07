<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6 0006
 * Time: 上午 11:53
 */
use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
</style>
<div class="page-header am-fl am-cf">
    <h4>新闻管理 <small>&nbsp;/&nbsp;编辑或添加新闻</small></h4>
</div>
<div class="ulcontent" id="content">
    <form class="form-horizontal" id="form"  method="post" action="<?php echo Url::to(['carnews/newsedit']) ?>" >
        <div class="div" id="content_1" style="display: block">
            <div class="tabTop clearfix">

                <div class="formConfirm webkitbox">
                    <label>新闻标题</label>
                    <input type="text" required name="title" class="spName" placeholder="请输入资讯标题" value="<?php echo $news['title'];?>">
                    <span>*必填</span>
                </div>
                <div class="formConfirm webkitbox">
                    <label>新闻摘要</label>
                    <input type="text" required name="short_desc" class="spName" placeholder="请输入资讯摘要" value="<?php echo $news['short_desc'];?>">
                    <span>*必填</span>
                </div>
                <div class="formConfirm webkitbox">
                    <label for="inputPassword3" class="col-sm-2 control-label">封面</label>
                    <div class="col-sm-3">
                        <div id="hadpic"></div>
                        <input type="hidden" name="news_img" id="news_img" value="<?php echo $news['img'];?>"/>
                        <?php if($news['img']) echo '<img  src="'.$news['img'].'" />';?>
                        <p class="help-block"> </p>
                    </div>
                </div>
            </div>
            <div class="tabBot clearfix">
                <div class="formConfirm webkitbox">
                    <label>新闻详情</label>
                    <textarea required name="content" id="discrible" style="width: 800px;height: 400px;" placeholder="资讯详情描述"><?php echo $news['content'];?></textarea>
                </div>
                <div class="formConfirm webkitbox">
                    <label>排序</label>
                    <input type="text" class="spName"   name="sort" type="number"  placeholder="50" value="<?php echo $news['sort'];?>">
                </div>

            </div>
            <div class="baocunBot webkitbox clearfix">
                <input name="id"  type="hidden" value="<?php echo $news['id']; ?>">
                <div class="baocunB  am-btn-success"  onclick="document.getElementById('form').submit();">保存</div>
                <div class="fanhuiB" onclick="history.back()">返回</div>
            </div>
        </div>
    </form>
    <link href="../../../static/kindeditor/themes/default/default.css"/>
    <script type="text/javascript" charset="utf-8" src="../../../static/kindeditor/kindeditor-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="../../../static/kindeditor/lang/zh_CN.js"></script>
    <script>
        $(function(){
            uploadImg('hadpic','news_img');
        });

        var editor;
        KindEditor.ready(function(K){
            editor = K.create("#discrible",{
                allowFileManager:true,
                afterBlur : function(){this.sync();}//需要添加的
            });
        });

    </script>
    <script>
//        var editor;
//        var ue1 = UE.getEditor('parameter');
//        var ue2 = UE.getEditor('discrible');
//
//
//        KindEditor.ready(function(K){
//            editor = K.create("#discrible",{
//                allowFileManager:true,
//                afterBlur : function(){this.sync();}//需要添加的
//            });
//
//        });

        //--------start
        function handleFiles(obj) {
            window.URL = window.URL || window.webkitURL;
            var fileList = $('<span class="splist"><i></i></span>');
            var files = obj.files,
                img = new Image();
            if(window.URL){
                img.src = window.URL.createObjectURL(files[0]);
                img.onload = function(e) {
                    window.URL.revokeObjectURL(this.src);
                }
                fileList.append(img);
                $(obj).parent().after(fileList);
            }
            fileList.find('i').on('click', function () {
                $(this).parents('form')[0].reset();//注意这个顺序
                $(this).parent().remove();
            });
        }

    </script>