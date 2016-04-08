<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "request_there_date".
 *
 * @property integer $request
 * @property string $date
 * @property integer $status
 *
 * @property Request $request0
 */
class RequestThereDate extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_PROCESSED = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_there_date';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request', 'date'], 'required'],
            [['request', 'status'], 'integer'],
            [['date'], 'safe'],
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
            'date' => 'Date',
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
