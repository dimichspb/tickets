<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Request */

use kartik\widgets\Typeahead;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use kartik\touchspin\TouchSpin;

$this->registerJsFile('js/init.js', ['depends' => 'yii\web\JqueryAsset']);
$layout = '
<span class="input-group-addon kv-date-calendar hidden-xs" title="Select date"><i class="glyphicon glyphicon-calendar"></i></span>
{input}';

$this->registerJs('
    $("#request-there_start_date-disp").on("change", function() {
        var startInput = $(this);
        var endInput = $("#request-there_end_date-disp");
        var firstValue = startInput.val().split(".");
        var secondValue = endInput.val().split(".");

        var firstDate=new Date();
        firstDate.setFullYear(firstValue[2],(firstValue[1] - 1 ),firstValue[0]);
        
        var secondDate=new Date();
        secondDate.setFullYear(secondValue[2],(secondValue[1] - 1 ),secondValue[0]); 
        
        if (firstDate > secondDate) {
            endInput.val(startInput.val());
        };
    });
');

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <?php $form = ActiveForm::begin(['id' => 'form-request']); ?>
    <div class="row hidden-xs hidden-sm">
        <div class="col-xs-12">
            <h1><?= Yii::t('app', 'Whole day and night'); ?></h1>

            <p class="lead"><?= Yii::t('app', 'I will track the best fares for your flight'); ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <?= '<label class="control-label">'.Yii::t('app', 'Origin').'</label>'; ?>
            <?= Typeahead::widget([
                    'id' => 'origin-input',
                    'name' => 'origin_text',
                    'options' => [
                        'placeholder' => Yii::t('app', 'City, country, continent ...'),
                    ],
                    'pluginOptions' => [
                        'highlight' => true,
                        'hint' => false,
                    ],
                    'pluginEvents' => [
                        'typeahead:select' => 'function(data, item) {document.getElementById("request-origin").value = item.id; }',
                        'typeahead:autocompleted' => 'function(data, item) {document.getElementById("request-origin").value = item.id; }',
                    ],
                    'scrollable' => true,
                    'dataset' => [
                        [
                            'display' => 'name',
                            'value' => 'id',
                            'remote' => [
                                'url' => '//'.Yii::$app->params['api']['domain'].'/'.Yii::$app->params['api']['currentVersion'].'/places?l=' . Yii::$app->language . '&q=%QUERY',
                                'wildcard' => '%QUERY'
                            ],
                        ],
                    ],
                ]);
            ?>
            <?= $form->field($model, 'origin')->hiddenInput()->label(false);?>
        </div>
        <div class="col-xs-12 col-md-6">
            <?= '<label class="control-label">'.Yii::t('app', 'Destination').'</label>';?>
            <?= Typeahead::widget([
                    'name' => 'destination_text',
                    'options' => [
                        'placeholder' => Yii::t('app', 'City, country, continent ...'),
                    ],
                    'pluginOptions' => [
                        'highlight' => true,
                        'hint' => false,
                    ],
                    'pluginEvents' => [
                        'typeahead:select' => 'function(data, item) {document.getElementById("request-destination").value = item.id; }',
                        'typeahead:autocompleted' => 'function(data, item) {document.getElementById("request-destination").value = item.id; }',
                    ],
                    'scrollable' => true,
                    'dataset' => [
                        [
                            'display' => 'name',
                            'value' => 'id',
                            'remote' => [
                                'url' => '//'.Yii::$app->params['api']['domain'].'/'.Yii::$app->params['api']['currentVersion'].'/places?l=' . Yii::$app->language . '&q=%QUERY',
                                'wildcard' => '%QUERY',
                            ],
                        ],
                    ],
                ])
            ?>
            <?= $form->field($model, 'destination')->hiddenInput()->label(false);?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 col-md-3">
            <?= $form->field($model, 'there_start_date')->widget(DateControl::className(), [
                'saveFormat'=>'php:Y-m-d',
                'displayFormat' => 'php:d.m.Y',
                'options'=>[
                    'pluginOptions' => ['autoclose' => true,  'orientation' => 'top left'],
                    'layout' => $layout,
                ],
            ]); ?>
        </div>
        <div class="col-xs-6 col-md-3">
            <?= $form->field($model, 'there_end_date')->widget(DateControl::className(), [
                'saveFormat'=>'php:Y-m-d',
                'displayFormat' => 'php:d.m.Y',
                'options'=>[
                    'pluginOptions' => ['autoclose' => true,  'orientation' => 'top right'],
                    'layout' => $layout,
                ],
            ]); ?>
            <?php
            /*echo FieldRange::widget([
                'form' => $form,
                'model' => $model,
                'label' => Yii::t('app', 'Flight dates range'),
                'attribute1' => 'there_start_date',
                'attribute2' => 'there_end_date',
                'separator' => ' - ',
                'type' => FieldRange::INPUT_WIDGET,
                'widgetClass' => DateControl::className(),
                'widgetOptions1' => [
                    'saveFormat'=>'php:Y-m-d',
                    'displayFormat' => 'php:d.m.Y',
                    'options'=>[
                        'pluginOptions' => ['autoclose' => true,],
                        'layout' => $layout,
                    ],
                ],
                'widgetOptions2' => [
                    'saveFormat'=>'php:Y-m-d',
                    'displayFormat' => 'php:d.m.Y',
                    'options'=>[
                        'pluginOptions' => ['autoclose' => true,],
                        'layout' => $layout,
                    ],
                ],
            ]);*/
            ?>
        </div>
        <div class="col-xs-6 col-md-3">
            <?= $form->field($model, 'travel_period_start')->widget(TouchSpin::className()); ?>
        </div>
        <div class="col-xs-6 col-md-3">
            <?= $form->field($model, 'travel_period_end')->widget(TouchSpin::className()); ?>
            <?php
            /* FieldRange::widget([
                'form' => $form,
                'model' => $model,
                'label' => Yii::t('app', 'Travel period range'),
                'attribute1' => 'travel_period_start',
                'attribute2' => 'travel_period_end',
                'separator' => Yii::t('app', 'days'),
                'type' => FieldRange::INPUT_SPIN,
            ]) */
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Start tracking'), ['class' => 'btn btn-lg btn-success btn-raised', 'name' => 'signup-button']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
