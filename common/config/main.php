<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
	'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
    ],
    'modules' => [
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module',
            'ajaxConversion' => false,
        ]
    ],
];
