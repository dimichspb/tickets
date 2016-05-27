<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Request */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'create_date')->textInput() ?>

    <?= $form->field($model, 'user')->textInput() ?>

    <?= $form->field($model, 'origin')->textInput() ?>

    <?= $form->field($model, 'destination')->textInput() ?>

    <?= $form->field($model, 'there_start_date')->textInput() ?>

    <?= $form->field($model, 'there_end_date')->textInput() ?>

    <?= $form->field($model, 'travel_period_start')->textInput() ?>

    <?= $form->field($model, 'travel_period_end')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
