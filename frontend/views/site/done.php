<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Well done!';
?>
<div class="site-done">
    <div class="body-content text-center">
        <h1><?= Html::encode($this->title) ?></h1>

        <p class="lead">Relax while I'm doing my work!</p>

        <?= Html::a('New request', ['site/index'], ['class'=>'btn btn-lg btn-primary']); ?>

    </div>

</div>
