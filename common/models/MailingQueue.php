<?php

namespace common\models;

use Yii;
use yii\debug\models\search\Mail;

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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
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

}
