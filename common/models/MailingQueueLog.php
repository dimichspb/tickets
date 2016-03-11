<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "mailing_queue_log".
 *
 * @property string $create_date
 * @property integer $mailing_queue
 * @property string $message
 *
 * @property MailingQueue $mailingQueue
 */
class MailingQueueLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_queue_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date'], 'safe'],
            [['mailing_queue'], 'required'],
            [['mailing_queue'], 'integer'],
            [['message'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'create_date' => 'Create Date',
            'mailing_queue' => 'Mailing Queue',
            'message' => 'Message',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingQueue()
    {
        return $this->hasOne(MailingQueue::className(), ['id' => 'mailing_queue']);
    }

    public static function log(MailingQueue $mailingQueue, $message)
    {
        $mailingQueueLog = new MailingQueueLog();
        $mailingQueueLog->mailing_queue = $mailingQueue->id;
        $mailingQueueLog->message = $message;
        $mailingQueueLog->save();
    }
}
