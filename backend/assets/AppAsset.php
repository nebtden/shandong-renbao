<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public  $css = [
       /* 'css/bootstrap.min.css',
        'css/bootstrap-table.css',
        'css/tangCss.css',
        'css/IEtangCss.css'*/
    ];
    public $js = [
      // 'js/jquery-1.10.1.js',
        'js/bootstrap.min.js',
        'js/bootstrap-table.js',
        'js/bootstrap-table-zh-CN.js',
        'js/bootstrap-table-export.js',
        'js/tableExport.js'
    ];
    public $depends = [
      //  'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
