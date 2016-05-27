<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Request;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'My requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-index">

    <h2><?= Html::encode($this->title) ?></h2>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'options' => ['class' => 'request-grid-container'],
        'headerRowOptions' => ['class' => 'requests-grid-header'],
        'rowOptions' => ['class' => 'requests-grid-row'],
        'columns' => [
            [
                'attribute' => 'origin',
                'value' => function (Request $model) {
                    return $model->getOriginOne()->getPlaceName();
                },
            ],
            [
                'attribute' => 'destination',
                'value' => function (Request $model) {
                    return $model->getDestinationOne()->getPlaceName();
                },
            ],
            'there_start_date:date',
            'there_end_date:date',
            'travel_period_start',
            'travel_period_end',
            'currency',
            'status',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {pause} {delete}',
                'buttons' => [
                    'pause' => function ($url, $model, $key) {
                        switch ($model->status) {
                            case Request::STATUS_INACTIVE:
                                return Html::a('<i class="glyphicon glyphicon-play"></i>', ['request/start'], ['title' => Yii::t('app', 'Start request')]);
                                break;
                            case Request::STATUS_ACTIVE:
                                return Html::a('<i class="glyphicon glyphicon-pause"></i>', ['request/pause'], ['title' => Yii::t('app', 'Pause request')]);
                                break;
                            default:
                                return '';
                        }
                    },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
