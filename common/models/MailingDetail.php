<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "mailing_detail".
 *
 * @property string $code
 * @property string $name
 *
 * @property MailingConfiguration[] $mailingConfigurations
 * @property MailingDetailToMailingType[] $mailingDetailToMailingTypes
 * @property MailingType[] $mailingTypes
 */
class MailingDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 8],
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
        return $this->hasMany(MailingConfiguration::className(), ['mailing_detail' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingDetailToMailingTypes()
    {
        return $this->hasMany(MailingDetailToMailingType::className(), ['mailing_detail' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingTypes()
    {
        return $this->hasMany(MailingType::className(), ['code' => 'mailing_type'])->viaTable('mailing_detail_to_mailing_type', ['mailing_detail' => 'code']);
    }

    /**
     * @param $code
     * @return MailingDetail
     */
    public static function getMailingDetailByCode($code)
    {
        return MailingDetail::find()
            ->where([
                'code' => $code,
            ])
            ->one();
    }
}
