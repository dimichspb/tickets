<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "service_type".
 *
 * @property string $code
 * @property string $name
 *
 * @property Endpoint[] $endpoints
 */
class ServiceType extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_DELETED = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['code', 'name'], 'string', 'max' => 255]
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
    public function getEndpoints()
    {
        return $this->hasMany(Endpoint::className(), ['service_type' => 'code']);
    }
}
