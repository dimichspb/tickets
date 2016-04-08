<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "request_travel_period".
 *
 * @property integer $request
 * @property integer $period
 * @property integer $status
 *
 * @property Request $request0
 */
class RequestTravelPeriod extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_PROCESSED = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_travel_period';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request', 'period'], 'required'],
            [['request', 'period', 'status'], 'integer'],
            [['request'], 'exist', 'skipOnError' => true, 'targetClass' => Request::className(), 'targetAttribute' => ['request' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'request' => 'Request',
            'period' => 'Period',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest0()
    {
        return $this->hasOne(Request::className(), ['id' => 'request']);
    }
}
