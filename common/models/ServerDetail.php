<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "server_detail".
 *
 * @property string $code
 * @property string $name
 *
 * @property ServerConfiguration[] $serverConfigurations
 * @property ServerDetailToServerType[] $serverDetailToServerTypes
 * @property ServerType[] $serverTypes
 */
class ServerDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server_detail';
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
        return $this->hasMany(ServerConfiguration::className(), ['server_detail' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerDetailToServerTypes()
    {
        return $this->hasMany(ServerDetailToServerType::className(), ['server_detail' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServerTypes()
    {
        return $this->hasMany(ServerType::className(), ['code' => 'server_type'])->viaTable('server_detail_to_server_type', ['server_detail' => 'code']);
    }
}
