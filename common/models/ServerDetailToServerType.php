<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "server_detail_to_server_type".
 *
 * @property string $server_type
 * @property string $server_detail
 *
 * @property ServerDetail $serverDetail
 * @property ServerType $serverType
 */
class ServerDetailToServerType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server_detail_to_server_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['server_type', 'server_detail'], 'required'],
            [['server_type', 'server_detail'], 'string', 'max' => 4]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'server_type' => 'Server Type',
            'server_detail' => 'Server Detail',
        ];
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
