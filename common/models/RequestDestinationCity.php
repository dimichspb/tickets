<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "request_destination_city".
 *
 * @property integer $request
 * @property string $city
 * @property integer $status
 *
 * @property City $city0
 * @property Request $request0
 */
class RequestDestinationCity extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_PROCESSED = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_destination_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request', 'city'], 'required'],
            [['request', 'status'], 'integer'],
            [['city'], 'string', 'max' => 3],
            [['city'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city' => 'code']],
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
            'city' => 'City',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity0()
    {
        return $this->hasOne(City::className(), ['code' => 'city']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest0()
    {
        return $this->hasOne(Request::className(), ['id' => 'request']);
    }
}
