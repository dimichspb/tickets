<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "server_to_server_type".
 *
 * @property string $create_date
 * @property string $server
 * @property string $server_type
 * @property integer $status
 *
 * @property Server $server0
 * @property ServerType $serverType
 */
class ServerToServerType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server_to_server_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_date', 'server', 'server_type'], 'required'],
            [['create_date'], 'safe'],
            [['status'], 'integer'],
            [['server'], 'string', 'max' => 5],
            [['server_type'], 'string', 'max' => 4]
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
    public function getServerType()
    {
        return $this->hasOne(ServerType::className(), ['code' => 'server_type']);
    }
}
