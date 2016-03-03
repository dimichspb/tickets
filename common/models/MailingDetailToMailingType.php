<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "mailing_detail_to_mailing_type".
 *
 * @property string $mailing_type
 * @property string $mailing_detail
 *
 * @property MailingDetail $mailingDetail
 * @property MailingType $mailingType
 */
class MailingDetailToMailingType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_detail_to_mailing_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mailing_type', 'mailing_detail'], 'required'],
            [['mailing_type'], 'string', 'max' => 5],
            [['mailing_detail'], 'string', 'max' => 8]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mailing_type' => 'Mailing Type',
            'mailing_detail' => 'Mailing Detail',
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
    public function getMailingType()
    {
        return $this->hasOne(MailingType::className(), ['code' => 'mailing_type']);
    }
}
