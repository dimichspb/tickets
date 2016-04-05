<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

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
                ] ,'`vs`.`variable` = `variable_value`.`variable` AND `vs`.`id` = `variable_value`.`variable_scope`')
            ->andWhere([
                '`variable_value`.`language`' => $language->code,
            ]);

        return $result->one();
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
        $result = Variable::processLoops($value, $subTablesArray);

        $pattern = '/\{\$([A-Za-z0-9\.\_]+)\}/';

        $matches = [];

        if (preg_match_all($pattern, $value, $matches)) {
            foreach($matches[1] as $matchItem) {
                if (strpos($matchItem, '.')) {
                    $result = Variable::processSubtable($result, $matchItem, $subTablesArray);
                } else {
                    $result = Variable::processVariable($result, $matchItem, $subTablesArray, $mailing, $language );
                }
            }
            $result = Variable::processValue($result, $mailing, $language, $subTablesArray);
        }

        return $result;
    }

    private static function processVariable($text, $item, array $subTablesArray, Mailing $mailing, Language $language)
    {
        if (ArrayHelper::keyExists($item, $subTablesArray)) {
            $value = $subTablesArray[$item];
        } else {
            $value = Variable::getValue($item, [
                'mailing' => $mailing->code,
            ], $language);
        }

        return str_replace('{$'. $item . '}', isset($value)? $value: '', $text);
    }

    private static function processSubtable($text, $item, array $subTablesArray)
    {
        list($tableName, $fieldName) = explode('.', $item, 2);
        $value = isset($subTablesArray[$tableName]) && isset($subTablesArray[$tableName][$fieldName])?
            ($subTablesArray[$tableName][$fieldName]):
            '';
        return str_replace('{$' . $item . '}', $value, $text);
    }

    private static function processLoops($text, array &$subTablesArray)
    {
        $pattern = '/{foreach \$(\w+) as \$(\w+) => \$(\w+)}([^{]*+(?:{(?!\/?foreach)[^{]*)*+){\/foreach}/';

        $matches = [];

        $newText = '';

        while (preg_match_all($pattern, $text, $matches)) {

            foreach ($matches[1] as $index => $arrayName) {
                if (isset($subTablesArray[$arrayName]) && is_array($subTablesArray[$arrayName])) {
                    foreach ($subTablesArray[$arrayName] as $subTable) {
                        $i = isset($i)? $i + 1: 0;
                        $itemName = $matches[3][$index];
                        $newItemName = $itemName  . $i;
                        $subTablesArray[$newItemName] = $subTable;
                        $newText .= str_replace('$' . $itemName . '.', '$' . $newItemName . '.', $matches[4][$index]);
                    }
                    $text = str_replace($matches[0][$index], $newText, $text);
                }
            }
        }
        return $text;
    }
}
