<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\Rate;

/* @var $this yii\web\View */
/* @var $model common\models\Request */
/* @var $ratesDataProvider \yii\data\DataProviderInterface */

$this->title = $model->getOriginPlaceName() . ' - ' . $model->getDestinationPlaceName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-view">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= Html::encode($this->title) ?></h2>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'origin',
                        'value' => $model->getOriginPlaceName(),
                    ],
                    [
                        'attribute' => 'destination',
                        'value' => $model->getDestinationPlaceName(),
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => $model->getStatusBadge(),
                    ],
                    'currency',
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'there_start_date:date',
                    'there_end_date:date',
                    'travel_period_start',
                    'travel_period_end',
                ],
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= GridView::widget([
                'dataProvider' => $ratesDataProvider,
                'columns' => [
                    [
                        'attribute' => 'origin_city',
                        'value' => function (Rate $model) {
                            return $model->getOriginCityName();
                        }
                    ],
                    [
                        'attribute' => 'destination_city',
                        'value' => function (Rate $model) {
                            return $model->getDestinationCityName();
                        }
                    ],
                    'there_date:datetime',
                    'back_date:datetime',
                    [
                        'attribute' => 'airline',
                        'value' => function (Rate $model) {
                            return $model->getAirlineName();
                        }
                    ],
                    'flight_number',
                    'price',
                    'currency',
                ]
            ]); ?>
        </div>
    </div>
</div>
