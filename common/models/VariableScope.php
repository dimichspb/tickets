<?php

namespace common\Models;

use Yii;

/**
 * This is the model class for table "variable_scope".
 *
 * @property integer $id
 *
 * @property Mailing $mailing
 * @property Variable $variable
 * @property VariableValue[] $variableValues
 */
class VariableScope extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'variable_scope';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variable'], 'required'],
            [['variable'], 'string', 'max' => 32],
            [['mailing'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'variable' => 'Variable',
            'mailing' => 'Mailing',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailing()
    {
        return $this->hasOne(Mailing::className(), ['mailing' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariable()
    {
        return $this->hasOne(Variable::className(), ['variable' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariableValues()
    {
        return $this->hasMany(VariableValue::className(), ['variable_scope' => 'id']);
    }
}
