<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mailing_to_mailing_type".
 *
 * @property string $create_date
 * @property string $mailing
 * @property string $mailing_type
 * @property integer $status
 *
 * @property Mailing $mailing0
 * @property MailingType $mailingType
 */
class MailingToMailingType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_to_mailing_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date', 'mailing', 'mailing_type'], 'required'],
            [['create_date'], 'safe'],
            [['status'], 'integer'],
            [['mailing', 'mailing_type'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'create_date' => 'Create Date',
            'mailing' => 'Mailing',
            'mailing_type' => 'Mailing Type',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailing0()
    {
        return $this->hasOne(Mailing::className(), ['code' => 'mailing']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingType()
    {
        return $this->hasOne(MailingType::className(), ['code' => 'mailing_type']);
    }
}
