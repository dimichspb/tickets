<?php

/* @var $this yii\web\View */

use kartik\widgets\Typeahead;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;

$this->title = 'Tickets Tracker';
?>
<div class="site-index">

    <div class="body-content text-center">
        <h1><?= Yii::t('app', 'Congratulations!'); ?></h1>

        <p class="lead"><?= Yii::t('app', 'You are in one step to get the best air fares!'); ?></p>

        <?php $form = ActiveForm::begin(['id' => 'form-request']); ?>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <?php echo '<label class="control-label">'.Yii::t('app', 'Origin').'</label>'; ?>
                <?= Typeahead::widget([
                        'name' => 'origin_text',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Where are you from ...'),
                        ],
                        'pluginOptions' => [
                            'highlight' => true,
                        ],
                        'pluginEvents' => [
                            'typeahead:select' => 'function(data, item) {document.getElementById("request-origin").value = item.id; }',
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
            <div class="col-md-6 col-sm-12">
                <?php echo '<label class="control-label">'.Yii::t('app', 'Destination').'</label>';?>
                <?= Typeahead::widget([
                        'name' => 'destination_text',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Where are you going to ...'),
                        ],
                        'pluginOptions' => [
                            'highlight' => true,
                        ],
                        'pluginEvents' => [
                            'typeahead:select' => 'function(data, item) {document.getElementById("request-destination").value = item.id; }',
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
            <div class="col-sm-12 col-md-6">
                <?php
                $layout = <<< HTML
                    <span class="input-group-addon kv-date-calendar hidden-xs" title="Select date"><i class="glyphicon glyphicon-calendar"></i></span>
                    {remove}
                    {input}
HTML;

                echo FieldRange::widget([
                    'form' => $form,
                    'model' => $model,
                    'label' => Yii::t('app', 'Flight date range'),
                    'attribute1' => 'there_start_date',
                    'attribute2' => 'there_end_date',
                    'type' => FieldRange::INPUT_WIDGET,
                    'widgetClass' => DateControl::className(),
                    'widgetOptions1' => [
                        'saveFormat'=>'php:Y-m-d',
                        'displayFormat' => 'php:Y-m-d',
                        'options'=>[
                            'pluginOptions' => ['autoclose' => true,],
                            'layout' => $layout,
                        ],
                    ],
                    'widgetOptions2' => [
                        'saveFormat'=>'php:Y-m-d',
                        'displayFormat' => 'php:Y-m-d',
                        'options'=>[
                            'pluginOptions' => ['autoclose' => true,],
                            'layout' => $layout,
                        ],
                    ],
                ]);
                ?>
            </div>
            <div class="col-sm-12 col-md-6">
                <?= FieldRange::widget([
                    'form' => $form,
                    'model' => $model,
                    'label' => Yii::t('app', 'Travel period range'),
                    'attribute1' => 'travel_period_start',
                    'attribute2' => 'travel_period_end',
                    'type' => FieldRange::INPUT_SPIN,
                ])
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Track now!'), ['class' => 'btn btn-lg btn-success', 'name' => 'signup-button']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
