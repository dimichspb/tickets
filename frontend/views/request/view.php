<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Request */

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
</div>
