<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
    	'urlManager' => [
    		'enablePrettyUrl' => true,
    		//'enableStrictParsing' => true,
    		'showScriptName' => false,
    		'suffix' => '.html',
    		'rules' => require(__DIR__ . '/rules.php')
    	],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'redis' =>[
			'class' => 'yii\redis\Connection',
			'hostname' => '10.10.10.80',
			'port' => 6379,
			'database' => 0
		],
//		'cache' => [
//			'class' => 'yii\redis\Cache'
//		],
		'session' => [
			'class' => 'yii\redis\Session'
		]
    ]
];
