<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "variable_value".
 *
 * @property integer $variable_scope
 * @property string $create_date
 * @property string $language
 * @property string $value
 *
 * @property Variable $variable
 * @property VariableScope $variableScope
 */
class VariableValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'variable_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variable', 'variable_scope', 'create_date', 'language'], 'required'],
            [['variable_scope'], 'integer'],
            [['create_date'], 'safe'],
            [['variable'], 'string', 'max' => 32],
            [['language'], 'string', 'max' => 5],
            [['value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'variable' => 'Variable',
            'variable_scope' => 'Variable Scope',
            'create_date' => 'Create Date',
            'language' => 'Language',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariable()
    {
        return $this->hasOne(Variable::className(), ['code' => 'variable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariableScope()
    {
        return $this->hasOne(VariableScope::className(), ['id' => 'variable_scope']);
    }
}
