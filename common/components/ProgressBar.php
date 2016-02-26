<?php

namespace yii\components;

use yii\helpers\Console;

/**
 * Class ProgressBar
 *
 * Console progress bar
 *
 * TODO:: implement using only array of objects as param. Then store counters in static attribute
 *
 * @package yii\components
 */
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