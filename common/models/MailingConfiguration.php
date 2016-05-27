<?php

namespace common\models;

use Yii;
use yii\helpers\Console;

/**
 * This is the model class for table "mailing_configuration".
 *
 * @property string $create_date
 * @property string $mailing_type
 * @property string $mailing_detail
 * @property string $value
 * @property integer $status
 *
 * @property Mailing $mailing
 * @property MailingDetail $mailingDetail
 * @property MailingType $mailingType
 */
class MailingConfiguration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date'], 'safe'],
            [['mailing', 'mailing_type', 'mailing_detail'], 'required'],
            [['status'], 'integer'],
            [['mailing', 'mailing_type'], 'string', 'max' => 5],
            [['mailing_detail'], 'string', 'max' => 8],
            [['value'], 'string', 'max' => 255]
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
            'mailing_detail' => 'Mailing Detail',
            'value' => 'Value',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailing()
    {
        return $this->hasOne(Mailing::className(), ['code' => 'mailing']);
    }

    /**
     * @return Mailing
     */
    public function getMailingOne()
    {
        return $this->getMailing()->one();
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

    public function getValue(Language $language, array $subTablesArray = [])
    {
        $result = Variable::processValue($this->value, $this->getMailingOne(), $language, $subTablesArray);
        return $result;
    }
}
