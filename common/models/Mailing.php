<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mailing".
 *
 * @property string $code
 * @property string $name
 * @property integer $status
 *
 * @property MailingConfiguration[] $mailingConfigurations
 * @property MailingQueue[] $mailingQueues
 * @property MailingToMailingType[] $mailingToMailingTypes
 * @property VariableScope[] $variableScopes
 */
class Mailing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['status'], 'integer'],
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
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingConfigurations()
    {
        return $this->hasMany(MailingConfiguration::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingQueues()
    {
        return $this->hasMany(MailingQueue::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailingToMailingTypes()
    {
        return $this->hasMany(MailingToMailingType::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariableScopes()
    {
        return $this->hasMany(VariableScope::className(), ['mailing' => 'code']);
    }
}
