<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "request_mailing_rate".
 *
 * @property integer $request
 * @property integer $mailing_queue
 * @property integer $rate
 *
 * @property MailingQueue $mailingQueue
 * @property Rate $rate0
 * @property Request $request0
 */
class RequestMailingRate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_mailing_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request', 'mailing_queue', 'rate'], 'required'],
            [['request', 'mailing_queue', 'rate'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'request' => 'Request',
            'mailing_queue' => 'Mailing Queue',
            'rate' => 'Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingQueue()
    {
        return $this->hasOne(MailingQueue::className(), ['id' => 'mailing_queue']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRate0()
    {
        return $this->hasOne(Rate::className(), ['id' => 'rate']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest0()
    {
        return $this->hasOne(Request::className(), ['id' => 'request']);
    }
}
