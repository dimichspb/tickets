<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "server_configuration".
 *
 * @property string $create_date
 * @property string $server
 * @property string $server_type
 * @property string $server_detail
 * @property string $value
 * @property integer $status
 *
 * @property Server $server0
 * @property ServerDetail $serverDetail
 * @property ServerType $serverType
 */
class ServerConfiguration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server_configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date'], 'safe'],
            [['server', 'server_type', 'server_detail'], 'required'],
            [['status'], 'integer'],
            [['server'], 'string', 'max' => 5],
            [['server_type', 'server_detail'], 'string', 'max' => 4],
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
            'server' => 'Server',
            'server_type' => 'Server Type',
            'server_detail' => 'Server Detail',
            'value' => 'Value',
            'status' => 'Status',
        ];
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
    public function getServerDetail()
    {
        return $this->hasOne(ServerDetail::className(), ['code' => 'server_detail']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerType()
    {
        return $this->hasOne(ServerType::className(), ['code' => 'server_type']);
    }
}
