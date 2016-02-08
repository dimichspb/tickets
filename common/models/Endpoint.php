<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "endpoint".
 *
 * @property string $service
 * @property string $service_type
 * @property string $endpoint
 *
 * @property Service $service0
 * @property ServiceType $serviceType
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
    public function getService0()
    {
        return $this->hasOne(Service::className(), ['code' => 'service']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceType()
    {
        return $this->hasOne(ServiceType::className(), ['code' => 'service_type']);
    }
}
