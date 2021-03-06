<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">
    <div class="body-content text-center">
        <div class="row">
            <div class="col-xs-12">
                <h1><?= Html::encode($this->title) ?></h1>

                <p class="lead"><?= Yii::t('app', 'Seems like I\'ve lost the page you are looking for!'); ?></p>


                <?= Html::a(Yii::t('app', 'New request'), ['site/index'], ['class'=>'btn btn-lg btn-primary']); ?>

            </div>
        </div>
    </div>
</div>
