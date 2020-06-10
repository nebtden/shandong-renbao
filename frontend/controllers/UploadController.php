<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class UploadController extends BaseController{
    //文件上伟传
    //public $enableCsrfValidation = false;
    public function actionFile(){
        $files = UploadedFile::getInstanceByName('file');
        $baseroot = Yii::$app->params['rootPath'];
        $truepath = './static/tpl/'.date("Ymd").'/';
        $pathdir = $baseroot.$truepath;
        $ext = substr($files->name,strripos($files->name,'.'));
        $filename = str_replace($ext,'',$files->name);

        $filename = md5($filename.time()).$ext;
        FileHelper::createDirectory($pathdir);
        $path = $pathdir.$filename;
        if($files->saveAs($path)){
            $truepath .= $filename;
            $msg['status'] = 1;
            $msg['path'] = $truepath;
        }else{
            $msg['status'] = 0;
        }
        return json_encode($msg);
    }
}