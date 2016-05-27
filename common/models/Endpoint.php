<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "endpoint".
 *
 * @property Service $service
 * @property ServiceType $service_type
 * @property string $endpoint
 */
class Endpoint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'endpoint';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service', 'service_type', 'endpoint'], 'required'],
            [['service', 'service_type', 'endpoint'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'service' => 'Service',
            'service_type' => 'Service Type',
            'endpoint' => 'Endpoint',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['code' => 'service'])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceType()
    {
        return $this->hasOne(ServiceType::className(), ['code' => 'service_type']);
    }
}
