<?php

namespace common\models;

use Yii;
use yii\helpers\CurlHelper;
use yii\helpers\FileHelper;

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

    /**
     * Method processes all (or filtered) services
     *
     * @param string $filter
     */
    public static function process($filter = '')
    {
        if (!empty($filter)) {
            $activeServiceTypes = ServiceType::find()
                ->where([
                    'status' => ServiceType::STATUS_ACTIVE,
                    'code' => $filter,
                ])
                ->orderBy('order')
                ->all();
        } else {
            $activeServiceTypes = ServiceType::find()
                ->where(['status' => ServiceType::STATUS_ACTIVE])
                ->orderBy('order')
                ->all();
        }

        foreach ($activeServiceTypes as $activeServiceType) {
            foreach ($activeServiceType->endpoints as $endpoint) {
                //$curlAction = CurlHelper::get($endpoint->endpoint);
                //$responseJson = $curlAction['response'];
                //$responseCode = $curlAction['responseCode'];
                if (!filter_var($endpoint->endpoint, FILTER_VALIDATE_URL)) {
                    $endpoint->endpoint = dirname(Yii::$app->basePath) . $endpoint->endpoint;
                }
                if (!$responseJson = file_get_contents($endpoint->endpoint)) {
                    continue;
                }

                //if ($responseCode !== 200) {
                //    continue;
                //}
                var_dump($responseJson);
                ServiceType::uploadNewData($endpoint->service_type, $endpoint->service, $responseJson);
            }
        }
    }

    /**
     * Method uploads JSON data depending on provided $serviceType code and $service code
     *
     * @param $serviceType
     * @param $service
     * @param $dataJson
     */
    private static function uploadNewData($serviceType, $service, $dataJson)
    {
        switch ($serviceType) {
            case 'RG':
                Region::uploadRegions($service, $dataJson);
                break;
            case 'SR':
                Subregion::uploadSubregions($service, $dataJson);
                break;
            case 'CN':
                Country::uploadCountries($service, $dataJson);
                break;
            case 'CT':
                City::uploadCities($service, $dataJson);
                break;
            case 'AP':
                Airport::uploadAirports($service, $dataJson);
                break;
            case 'CR':
                Country::uploadCountriesToRegions($service, $dataJson);
                break;
            case 'AL':
                Airline::uploadAirlines($service, $dataJson);
                break;
            default:
        }
    }

    /**
     * Method returns particular "Direct flights" serviceType
     *
     * @return null|ServiceType
     */
    public static function directFlights()
    {
        return ServiceType::findOne([
            'status' => ServiceType::STATUS_ACTIVE,
            'code' => 'DR',
        ]);
    }
}
