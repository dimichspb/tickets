<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "server".
 *
 * @property string $code
 * @property string $name
 * @property integer $status
 *
 * @property MailingQueue[] $mailingQueues
 * @property ServerConfiguration[] $serverConfigurations
 * @property ServerToServerType[] $serverToServerTypes
 */
class Server extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server';
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
    public function getMailingQueues()
    {
        return $this->hasMany(MailingQueue::className(), ['server' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerConfigurations()
    {
        return $this->hasMany(ServerConfiguration::className(), ['server' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerToServerTypes()
    {
        return $this->hasMany(ServerToServerType::className(), ['server' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerTypes()
    {
        return $this->hasMany(ServerType::className(), ['server' => 'server_type'])->via('serverToServerTypes');
    }

    /**
     * @return ServerType
     */
    public function getServerType()
    {
        return $this->getServerTypes()->one();
    }

    /**
     * @param MailingType $mailingType
     * return Server
     */
    public static function getServer(MailingType $mailingType)
    {
        return $mailingType->serverType->getServers()->one();
    }
}
