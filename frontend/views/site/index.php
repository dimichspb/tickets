<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Request */

use kartik\widgets\Typeahead;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;

$this->registerJsFile('js/init.js', ['depends' => 'yii\web\JqueryAsset']);

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <?php $form = ActiveForm::begin(['id' => 'form-request']); ?>
    <div class="row">
        <div class="col-xs-12">
            <h1><?= Yii::t('app', 'Congratulations!'); ?></h1>

            <p class="lead"><?= Yii::t('app', 'You are in one step to get the best air fares!'); ?></p>
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
            <?php echo '<label class="control-label">'.Yii::t('app', 'Destination').'</label>';?>
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
        
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Track now!'), ['class' => 'btn btn-lg btn-success btn-raised', 'name' => 'signup-button']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
