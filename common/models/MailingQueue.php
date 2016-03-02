<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mailing_queue".
 *
 * @property integer $id
 * @property string $create_date
 * @property string $planned_date
 * @property string $processed_date
 * @property string $mailing
 * @property integer $user
 * @property string $server
 * @property integer $status
 *
 * @property Mailing $mailing0
 * @property Server $server0
 * @property User $user0
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
            [['mailing', 'server'], 'string', 'max' => 5]
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
    public function getMailing0()
    {
        return $this->hasOne(Mailing::className(), ['code' => 'mailing']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServer0()
    {
        return $this->hasOne(Server::className(), ['code' => 'server']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }
}
