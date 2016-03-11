<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Request has been sent';
?>
<div class="site-done">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-success">
        <p>
            Everything is fine!!!
        </p>
    </div>

    <?= Html::a('New request', Url::to(['site/index'])); ?>

</div>
