<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Signup');
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
?>
<div class="site-signup">
    <div class="body-content text-center">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php if($session->has('newRequestId')):?>
        <p><?= Yii::t('app', 'I need your e-mail to send search results:'); ?></p>
        <?php else: ?>
        <p><?= Yii::t('app', 'Please fill out the following fields to signup'); ?></p>
        <?php endif?>
        <div class="row">
            <div class="col-xs-12 col-md-10 col-lg-6 col-md-offset-1 col-lg-offset-3">
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'email') ?>

                <div class="form-group">
                    <?= Html::a(Yii::t('app', 'More details'), '#', ['data-toggle'=>'collapse', 'data-target'=>'#more-details']); ?>
                </div>

                <div id="more-details" class="form-group collapse">
                    <?= $form->field($model, 'first_name') ?>

                    <?= $form->field($model, 'last_name') ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Signup'), ['class' => 'btn btn-lg btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
