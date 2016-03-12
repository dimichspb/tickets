<?php

namespace common\models;

use common\models\Server;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * This is the model class for table "mailing_queue".
 *
 * @property integer $id
 * @property string $create_date
 * @property string $planned_date
 * @property string $processed_date
 * @property integer $status
 *
 * @property Mailing $mailing
 * @property Server $server
 * @property User $user
 */
class MailingQueue extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_DELETED = 2;
    const STATUS_SENT = 3;
    const STATUS_ERROR = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_queue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date', 'planned_date', 'processed_date'], 'safe'],
            [['mailing', 'user', 'server'], 'required'],
            [['user', 'status'], 'integer'],
            [['mailing','server'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_date' => 'Create Date',
            'planned_date' => 'Planned Date',
            'processed_date' => 'Processed Date',
            'mailing' => 'Mailing',
            'user' => 'User',
            'server' => 'Server',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailing()
    {
        $result = $this->hasOne(Mailing::className(), ['code' => 'mailing']);

        return $result;
    }

    public function getMailingToMailingTypes()
    {
        $result = $this->hasMany(MailingToMailingType::className(), ['mailing' => 'code'])->via('mailing');

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingTypes()
    {
        $result = $this->hasMany(MailingType::className(), ['code' => 'mailing_type'])->via('mailingToMailingTypes');

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingDetailToMailingTypes()
    {
        $result = $this->hasMany(MailingDetailToMailingType::className(), ['mailing_type' => 'code'])->via('mailingTypes');

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServer()
    {
        return $this->hasOne(Server::className(), ['code' => 'server']);
    }

    /**
     * @return Server
     */
    public function getServerOne()
    {
        return $this->getServer()->one();
    }

    public function getServerType()
    {
        return $this->getServerOne()->getServerType();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    public function getUserOne()
    {
        return $this->getUser()->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingQueueDetails()
    {
        return $this->hasMany(MailingQueueDetail::className(), ['mailing_queue' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingDetailsViaMailingQueueDetails()
    {
        return $this->hasMany(MailingDetail::className(), ['code' => 'mailing_detail'])->viaTable('mailing_queue_detail', ['mailing_queue' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingDetailsViaMailingTypes()
    {
        $result = $this->hasMany(MailingDetail::className(), ['code' => 'mailing_detail'])->via('mailingDetailToMailingTypes');

        return $result;
    }

    public function getMailingDetailsViaMailingTypesArray()
    {
        return $this->getMailingDetailsViaMailingTypes()->all();
    }

    /**
     * @param $mailingDetailCode
     * @return MailingDetail
     */
    public function getMailingDetailViaMailingTypes($mailingDetailCode)
    {
        $result = $this
            ->getMailingDetailsViaMailingTypes()
            ->where([
                'code' => $mailingDetailCode,
            ])->one();


        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingConfigurations()
    {
        return $this->hasMany(MailingConfiguration::className(), ['mailing' => 'code'])->via('mailing');
    }

    /**
     * @param MailingDetail $mailingDetail
     * @return MailingConfiguration
     */
    public function getMailingConfiguration(MailingDetail $mailingDetail)
    {
        return $this->getMailingConfigurations()->where(['mailing_detail' => $mailingDetail->code])->one();
    }

    /**
     * @return MailingQueue[]
     */
    public static function getActive()
    {
        return MailingQueue::find()
            ->where([
                'status' => self::STATUS_ACTIVE,
            ])
            ->all();
    }

    /**
     * @param MailingDetail $mailingDetail
     * @return MailingQueueDetail
     */
    public function getMailingQueueDetail(MailingDetail $mailingDetail)
    {
        return $this->getMailingQueueDetails()
            ->where([
               'mailing_detail' => $mailingDetail->code,
            ])
            ->one();
    }

    public function getFromName()
    {
        return $this->getMailingQueueDetail(MailingDetail::getMailingDetailByCode('FRNAME'))->value;
    }

    public function getFromAddress()
    {
        return $this->getMailingQueueDetail(MailingDetail::getMailingDetailByCode('FRMAIL'))->value;
    }

    public function getToName()
    {
        return $this->getMailingQueueDetail(MailingDetail::getMailingDetailByCode('TONAME'))->value;
    }

    public function getToAddress()
    {
        return $this->getMailingQueueDetail(MailingDetail::getMailingDetailByCode('TOMAIL'))->value;
    }

    public function getSubject()
    {
        return $this->getMailingQueueDetail(MailingDetail::getMailingDetailByCode('SUBJ'))->value;
    }

    public function getBody()
    {
        return $this->getMailingQueueDetail(MailingDetail::getMailingDetailByCode('BODY'))->value;
    }

    public function send()
    {
        switch ($this->getServerType()->code) {
            case 'SMTP':
                return $this->getServerOne()->sendSMTP($this);
                break;
            default:
        }
    }

}
