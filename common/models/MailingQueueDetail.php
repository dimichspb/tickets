<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mailing_queue_detail".
 *
 * @property integer $mailing_queue
 * @property string $mailing_detail
 * @property string $value
 *
 * @property MailingDetail $mailingDetail
 * @property MailingQueue $mailingQueue
 */
class MailingQueueDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_queue_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mailing_queue', 'mailing_detail'], 'required'],
            [['mailing_queue'], 'integer'],
            [['value'], 'string'],
            [['mailing_detail'], 'string', 'max' => 8]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mailing_queue' => 'Mailing Queue',
            'mailing_detail' => 'Mailing Detail',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingDetail()
    {
        return $this->hasOne(MailingDetail::className(), ['code' => 'mailing_detail']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingQueue()
    {
        return $this->hasOne(MailingQueue::className(), ['id' => 'mailing_queue']);
    }
}
