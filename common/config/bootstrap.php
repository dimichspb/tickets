<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api'); // add api alias

Yii::$classMap['yii\helpers\CurlHelper'] = '@common/components/CurlHelper.php';
Yii::$classMap['yii\components\ProgressBar'] = '@common/components/ProgressBar.php';
Yii::$classMap['yii\components\LangManager'] = '@common/components/LangManager.php';