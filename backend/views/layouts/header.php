<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <title><?php echo \Yii::$app->params['backendTitle']?></title>
        <link rel = "shortcut icon" href="/static/gwpc/images/logo.ico">
        <?= Html::cssFile('../css/pubilc.css'); ?>
        <?= Html::cssFile('../css/bootstrap.min.css'); ?>
        <?= Html::cssFile('../css/bootstrap-table.css'); ?>
        <?= Html::cssFile('../css/tangCss.css'); ?>
        <!--  --><?/*=Html::cssFile('../css/bootstrap-multiselect.css');*/?>
        <?= Html::cssFile('../css/select2.min.css'); ?>
        <!--[if lte IE 9]>
        <?= Html::cssFile('../css/IEtangCss.css') ?>
        <![endif]-->
<?= Html::jsFile('../js/jquery-1.10.1.js'); ?>
<?= Html::jsFile('../js/tab.js'); ?>
        <script type="text/javascript" charset="utf-8" src="/static/ueditor/ueditor.config.js"></script> 
        <script type="text/javascript" charset="utf-8" src="/static/ueditor/ueditor.all.min.js"></script> 
        <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
        <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
        <script type="text/javascript" charset="utf-8" src="/static/ueditor/lang/zh-cn/zh-cn.js"></script>
        <?= Html::cssFile('../diyUpload/css/webuploader.css'); ?>
        <?= Html::cssFile('../diyUpload/css/diyUpload.css'); ?>
        <?= Html::jsFile('../diyUpload/js/webuploader.html5only.min.js') ?>
        <?= Html::jsFile('../diyUpload/js/diyUpload.js') ?>
<?= Html::jsFile('../js/my.js'); ?>
<?= Html::jsFile('../js/select2.full.js'); ?>
        <!-- --><?/*=Html::jsFile('../js/bootstrap-multiselect.js');*/?>
        <style type="text/css">
            .table>tbody>tr>td{vertical-align:middle;}
            .fixed-table-header>.table {margin-bottom: 10px;}
            .page-header {margin: 20px 0 10px;}

            .file {
                position: relative;
                display: inline-block;
                background: #5bc0de;
                border: 1px solid #99D3F5;
                border-radius: 4px;
                padding: 4px 12px;
                overflow: hidden;
                color: #fff;
                text-decoration: none;
                text-indent: 0;
                line-height: 20px;
                vertical-align: middle;
            }
            .file input {
                position: absolute;
                font-size: 100px;
                right: 0;
                top: 0;
                opacity: 0;
            }
            .file:hover {
                background: #AADFFD;
                border-color: #78C3F3;
                color: #004974;
                text-decoration: none;
            }
        </style>
    </head>
    <body>

        <!--<header class="yhtHeader webkitbox clearfix">
            <div class="rightH webkitbox">
                <div class="adminH">admin</div>
                <div class="allsecreen">开启全屏</div>
            </div>
            <div class="adminLa">
                <div class="downsj"></div>
                <ul>
                    <li><a href="#">资料</a></li>
                    <li><a href="#">设置</a></li>
                    <li><a href="<?php /* echo Url::to(['site/logout']); */ ?>">退出</a></li>
                </ul>
            </div>
        </header>-->