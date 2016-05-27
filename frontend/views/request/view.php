<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Request */

$this->title = $model->getOriginOne()->getPlaceName() . ' - ' . $model->getDestinationOne()->getPlaceName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-view">

    <h2><?= Html::encode($this->title) ?></h2>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'origin',
                        'value' => $model->getOriginOne()->getPlaceName(),
                    ],
                    [
                        'attribute' => 'destination',
                        'value' => $model->getDestinationOne()->getPlaceName(),
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
