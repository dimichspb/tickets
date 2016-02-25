<?php

namespace yii\components;

use yii\helpers\Console;

class ProgressBar
{
    public static function step($totalItems, &$currentItem = 0, $step = 10)
    {
        if ($currentItem++ > $totalItems / $step) {
            Console::stdout('.');
            $currentItem = 0;
        }
    }
}