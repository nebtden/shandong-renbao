<?php

use yii\helpers\Url;
?>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
        <button type="button" class="btn btn-default"  onclick="window.location.href = '<?php echo Url::to(['news/news_edit']); ?>';">
            <i class="glyphicon glyphicon-plus"></i>
        </button>
        <button type="button" class="btn btn-default">
            <i class="glyphicon glyphicon-heart"></i>
        </button>
        <button type="button" class="btn btn-default" id="remove">
            <i class="glyphicon glyphicon-trash" ></i>
        </button>
        <div class="form-group"><input type="text" id="uname"   class="form-control" placeholder="标题名"></div>
        <div class="form-group"> <select id="sec"   placeholder="用户ID"  class="form-control" >
                <option value="">请选择</option>
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

                listCates($cates, $cates[0], 0, $news['cate_id']);
                ?>
            </select></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>
    </form>
</div>
<table class="table table-bordered"  style="margin-top:10px;">
    <thead>
        <tr>
            <th data-field="state" data-checkbox="true"></th>
            <th class="table-check" data-field="id">编号ID
               <!-- <input type="checkbox" id="allCheck"/>-->
            </th>
            <th data-field="img">缩略图</th>
            <th data-field="title">资讯名称</th>
            <th data-field="categroy">所属分类</th>
            <th width="400px;" data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">操作</th>
    <!--        <th data-formatter="runningFormatter" data-sortable="true">序号</th>-->
    <!--        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>-->
    <!--        <th data-field="username" data-align="center" data-sortable="true">用户名</th>-->
    <!--        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>-->
    <!--        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>-->
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var type = 'id', stext, order = 'desc', ids = '', imgcon = 1, more, eurl = "<?php echo Url::to(['news/news_edit']); ?>",
            listurl = '<?php echo Url::to(['news/list']); ?>',
            durl = "<?php echo Url::to(['news/news_del']); ?>";
</script>
<script src="../js/handle_data.js" ></script>



