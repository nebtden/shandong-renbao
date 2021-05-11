<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>
<style>
    .formConfirm span{margin-left: 0px;}
    .select2-selection__choice{line-height: 30px;}
</style>
<div class="ulcontent" id="content">
    <form class="form-horizontal" id="form"  method="post" action="<?php echo Url::to(['news/news_edit']) ?>" >
        <div class="div" id="content_1" style="display: block">
            <div class="tabTop clearfix">

                <div class="formConfirm webkitbox">
                    <label>资讯标题</label>
                    <input type="text" name="title" class="spName" placeholder="请输入资讯标题" value="<?php echo $news['title']; ?>">
                    <span>*必填</span>
                </div>
                <div class="formConfirm webkitbox">
                    <label>来源</label>
                    <input type="text" class="spName"   name="source" type="text"  placeholder="来源" value="<?php echo $news['source']; ?>">
                </div>
                <div class="formConfirm webkitbox">
                    <label>资讯摘要</label>
                    <input type="text" name="short_desc" class="spName" placeholder="请输入资讯摘要" value="<?php echo $news['short_desc']; ?>">
                    <span>*必填</span>
                </div>


                <div class="formConfirm webkitbox">
                    <label>分类</label>
                    <select name="category" data-am-selected="{ btnWidth: '100%'}" class="form-control"  style="width: 367px;">
                        <?php

                        function listCates($cates, $list, $i = 0, $category) {
                            $str = str_repeat('- - ', $i);
                            ;
                            foreach ($list as $v) {
                                if ($category == $v['id'])
                                    echo '<option value="' . $v['id'] . '" selected="selected">' . $str . $v['name'] . '</option>';
                                else
                                    echo '<option value="' . $v['id'] . '">' . $str . $v['name'] . '</option>';
                                if (is_array($cates[$v['id']]))
                                    listCates($cates, $cates[$v['id']], $i + 1, $category);
                            }
                        }

                        listCates($cates, $cates[0], 0, $product['categroy']);
                        ?>
                    </select>
                    <em class="addC">添加</em>
                    <span>*必填</span>
                </div>


            </div>


            <div class="tabBot clearfix">
                <div class="formConfirm clearfix">
                    <label class="imgFloat">资讯图片</label>


                    <div style="float: left;">
                        <?php
                        $style = '';
                        if ($news['pic']) {
                            $pics = explode('|', $news['pic']);
                            echo '<div id="curpiclist" style="height: 140px;">';
                            foreach ($pics as $v) {
                                echo '<ul   style="background:url(' . $v . ');background-size:cover;background-position:center;background-repeat:no-repeat; width:150px; height:130px; float:left; margin-right:10px;"><img src="../images/imgclose1.png"     onclick="delpic(this,\'' . $v . '\')"  style="float:right;padding:5px;"  ></ul>';
                            }
                            $style = 'style="margin-top:10px;"';
                            echo '</div>';
                        }
                        ?> 
                        <div>
                            <div id="hadpic" <?php echo $style; ?>></div>
                            <input type="hidden" name="pic" id="pic" value="<?php echo $news['pic']; ?>"/>
                        </div>
                    </div>

                    <!--                    <div class="formConfirm clearfix">
                                            <label class="imgFloat">商品图片</label>
                                            <div style="float: left;">
                    <?php
                    $style = '';
                    if ($product['pic']) {
                        $pics = explode('|', $product['pic']);
                        echo '<div id="curpiclist" style="height: 140px;">';
                        foreach ($pics as $v) {
                            echo '<ul   style="background:url(' . $v . ');background-size:cover;background-position:center;background-repeat:no-repeat; width:150px; height:130px; float:left; margin-right:10px;"><img src="../images/imgclose1.png"     onclick="delpic(this,\'' . $v . '\')"  style="float:right;padding:5px;"  ></ul>';
                        }
                        $style = 'style="margin-top:10px;"';
                        echo '</div>';
                    }
                    ?> 
                                                <div>
                                                    <div id="hadpic" <?php echo $style; ?>></div>
                                                    <input type="hidden" name="pic" id="pic" value="<?php echo $product['pic']; ?>"/>
                                                </div>
                                            </div>
                                        </div>-->


                    <div class="formConfirm webkitbox">
                        <label>资讯详情</label>
                        <textarea  name="content" id="discrible" style="width: 800px;" placeholder="资讯详情描述"><?php echo $news['content']; ?></textarea>
                    </div>
                    <div class="formConfirm webkitbox">
                        <label>排序</label>
                        <input type="text" class="spName"   name="sort" type="text"  placeholder="50" value="<?php echo $news['sort']; ?>">
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
                        var editor;
                        KindEditor.ready(function (K) {
                            editor = K.create("#discrible", {
                                allowFileManager: true,
                                afterBlur: function () {
                                    this.sync();
                                }//需要添加的
                            });

                        });

    </script>
    <script>
        uploadImg_r2('hadpic', 'pic', 100, 100);
        var ue1 = UE.getEditor('parameter');
        var ue2 = UE.getEditor('discrible');
        //--------start
        function handleFiles(obj) {
            window.URL = window.URL || window.webkitURL;
            var fileList = $('<span class="splist"><i></i></span>');
            var files = obj.files,
                    img = new Image();
            if (window.URL) {
                img.src = window.URL.createObjectURL(files[0]);
                img.onload = function (e) {
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
        function delpic(obj, pic) {
            var restr = pic;
            var str = $('#pic').val();
            var picArr = str.split("|");
            picArr.splice($.inArray(restr, picArr), 1);
            picVal = picArr.join("|");
            $('#pic').val(picVal);
            $(obj).parents('ul').remove();
            if ($("#curpiclist>ul").length < 1) {
                $("#curpiclist").remove();
            }
        }

    </script>