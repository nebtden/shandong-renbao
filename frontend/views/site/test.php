<?php
/* @var $this yii\web\View */
$this->title = 'My Yii Application';
?>
<link href="../../../static/kindeditor/themes/default/default.css"/>
<script type="text/javascript" charset="utf-8" src="../../../static/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" charset="utf-8" src="../../../static/kindeditor/lang/zh_CN.js"></script>
<script>
	var editor;
	KindEditor.ready(function(K){
		editor = K.create("#content",{
			allowFileManager:true
		});
	});
</script>
<textarea id="content"></textarea>