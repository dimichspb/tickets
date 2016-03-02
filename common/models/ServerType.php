<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "server_type".
 *
 * @property string $code
 * @property string $name
 *
 * @property ServerConfiguration[] $serverConfigurations
 * @property ServerDetailToServerType[] $serverDetailToServerTypes
 * @property ServerDetail[] $serverDetails
 * @property ServerToServerType[] $serverToServerTypes
 */
class ServerType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 4],
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
    public function getServerConfigurations()
    {
        return $this->hasMany(ServerConfiguration::className(), ['server_type' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerDetailToServerTypes()
    {
        return $this->hasMany(ServerDetailToServerType::className(), ['server_type' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerDetails()
    {
        return $this->hasMany(ServerDetail::className(), ['code' => 'server_detail'])->viaTable('server_detail_to_server_type', ['server_type' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerToServerTypes()
    {
        return $this->hasMany(ServerToServerType::className(), ['server_type' => 'code']);
    }
}
