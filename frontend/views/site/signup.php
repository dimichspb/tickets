<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
?>
<div class="site-signup">
    <div class="body-content text-center">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php if($session->has('newRequestId')):?>
        <p>I need your e-mail to send search results:</p>
        <?php else: ?>
        <p>Please fill out the following fields to signup:</p>
        <?php endif?>
        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'email') ?>

                <div class="form-group">
                    <?= Html::a('More details', '#', ['data-toggle'=>'collapse', 'data-target'=>'#more-details']); ?>
                </div>

                <div id="more-details" class="form-group collapse">
                    <?= $form->field($model, 'first_name') ?>

                    <?= $form->field($model, 'last_name') ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-lg btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
