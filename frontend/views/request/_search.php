<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RequestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'create_date') ?>

    <?= $form->field($model, 'user') ?>

    <?= $form->field($model, 'origin') ?>

    <?= $form->field($model, 'destination') ?>

    <?php // echo $form->field($model, 'there_start_date') ?>

    <?php // echo $form->field($model, 'there_end_date') ?>

    <?php // echo $form->field($model, 'travel_period_start') ?>

    <?php // echo $form->field($model, 'travel_period_end') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'mailing_processed') ?>

    <?php // echo $form->field($model, 'route_offset') ?>

    <?php // echo $form->field($model, 'rate_offset') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
