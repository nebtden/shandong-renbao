<?php
namespace common\components;

class CExcel{
	public static function getExcel($file){
		require(__DIR__ . '/../../vendor/PHPExcel/Excel.php');
		return $excel = new \Excel($file);
	}
}
