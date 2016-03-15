<?php
return [
    'name' => 'Ticket Tracker',
    'language' => 'en',
    'bootstrap' => ['langManager'],
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
        'langManager' => [
            'class' => 'yii\components\LangManager',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceLanguage' => 'en',
                ],
            ],
        ],
    ],
    'modules' => [
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module',
            'ajaxConversion' => false,
        ]
    ],
];
