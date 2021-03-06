<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div class="body-content text-center">
        <div class="row">
            <div class="col-xs-12">
                <h1><?= Html::encode($this->title) ?></h1>

                <p><?= Yii::t('app', 'I need to know your email to send link to login'); ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-10 col-lg-6 col-md-offset-1 col-lg-offset-3">
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'email') ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-lg btn-primary', 'name' => 'login-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
