<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mailing_type".
 *
 * @property string $code
 * @property string $name
 *
 * @property MailingConfiguration[] $mailingConfigurations
 * @property MailingDetailToMailingType[] $mailingDetailToMailingTypes
 * @property MailingDetail[] $mailingDetails
 * @property MailingToMailingType[] $mailingToMailingTypes
 * @property ServerType $serverType
 */
class MailingType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingConfigurations()
    {
        return $this->hasMany(MailingConfiguration::className(), ['mailing_type' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingDetailToMailingTypes()
    {
        return $this->hasMany(MailingDetailToMailingType::className(), ['mailing_type' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingDetails()
    {
        return $this->hasMany(MailingDetail::className(), ['code' => 'mailing_detail'])->viaTable('mailing_detail_to_mailing_type', ['mailing_type' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingToMailingTypes()
    {
        return $this->hasMany(MailingToMailingType::className(), ['mailing_type' => 'code']);
    }

    public function getServerType()
    {
        return $this->hasOne(ServerType::className(), ['code' => 'server_type']);
    }
}
