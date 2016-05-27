<?php
return [
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
				'done' => 'site/done',
				'error' => 'site/error',
                'login' => 'site/login',
                'logout' => 'site/logout',
		        'signup' => 'site/signup',
		        'request' => 'site/request',
				'<lang:[\w\-]{2}>' => 'site/lang',

				'requests' => 'request/index',
				'request/<id:\d+>' => 'request/view',

                '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
                '<_c:[\w\-]+>' => '<_c>/index',
                '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_c>/<_a>',

            ],
        ],
    ],
];

