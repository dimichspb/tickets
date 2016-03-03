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
        $result = $this->hasMany(VariableScope::className(), ['variable' => 'code']);

        //var_dump($result->createCommand()->rawSql);

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariableValues()
    {
        $result = $this->hasMany(VariableValue::className(), ['variable' => 'code']);

        //var_dump($result->createCommand()->rawSql);

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariableValuesViaVariableScope()
    {
        $result = $this->hasMany(VariableValue::className(), ['variable' => 'variable'])->via('variableScopes');

        //var_dump($result->createCommand()->rawSql);

        return $result;
    }

    /**
     * @param array $scope
     * @param Language $language
     * @return VariableValue
     */
    public function getValueByScopeAndLanguage(array $scope, Language $language)
    {
        $variableScopes = $this->getVariableScopes()->where($scope);

        $result = $this
            ->getVariableValues()
            ->innerJoin([
                'vs' => $variableScopes,
                ] ,'`vs`.`variable` = `variable_value`.`variable`')
            ->andWhere([
                '`variable_value`.`language`' => $language->code,
            ])->one();

        return $result;
    }

    public static function getValue($variableCode, array $scope, Language $language)
    {
        $variable = Variable::getVariableByCode($variableCode);
        if (!$variable) {
            var_dump($variableCode . " not found");
            return;
        }

        $value = $variable->getValueByScopeAndLanguage($scope, $language);

        return isset($value->value)? $value->value: null;
    }

    /**
     * @param $variableCode
     * @return Variable
     */
    public static function getVariableByCode($variableCode)
    {
        return Variable::find()
            ->where([
                'code' => $variableCode
            ])->one();
    }

    public static function processValue($value, Mailing $mailing, Language $language, array $subTablesArray)
    {
        $result = $value;

        $pattern = "/\{([A-Za-z0-9\.\_]+)\}/";

        $matches = [];

        if (preg_match_all($pattern, $value, $matches)) {
            foreach($matches[1] as $matchItem) {
                if (strpos($matchItem, '.')) {
                    if (count($subTablesArray)) {
                        list($tableName, $fieldName) = explode('.', $matchItem, 2);
                        $result = str_replace('{' . $matchItem . '}', $subTablesArray[$tableName]->$fieldName, $result);
                    }
                } else {
                    $value = Variable::getValue($matchItem, [
                        'mailing' => $mailing->code,
                    ], $language);
                    $result = str_replace('{'. $matchItem . '}', $value, $result);
                }
            }
            $result = Variable::processValue($result, $mailing, $language, $subTablesArray);
        }

        return $result;
    }

}
