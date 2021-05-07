<?php
use yii\helpers\Url;
?>

<div class="page-header am-fl am-cf">
            <h4>权限管理 <small>&nbsp;/&nbsp;列表页面</small></h4>
</div>
<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">用户管理</a></li>
        <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" onclick="window.parent.frames[1].location.reload()">用户组管理</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">
            <iframe src="<?php echo Url::to(['authority/list']); ?>" width="100%" height='790' frameborder="0">
            </iframe>
        </div>
        <div role="tabpanel" class="tab-pane" id="profile">
            <iframe src="<?php echo Url::to(['authority/group']); ?>" width="100%" height='790' frameborder="0">
            </iframe></div>
    </div>
</div>
