<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "variable".
 *
 * @property string $code
 * @property string $name
 *
 * @property VariableScope[] $variableScopes
 * @property VariableValue[] $variableValues
 */
class Variable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'variable';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 32],
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
    public function getVariableScopes()
    {
        return $this->hasMany(VariableScope::className(), ['variable' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariableValues()
    {
        return $this->hasMany(VariableValue::className(), ['variable' => 'code']);
    }
}
