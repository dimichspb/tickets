<?php

namespace console\controllers;

use common\Models\Country;
use yii\console\Controller;
use common\models\ServiceType;
use linslin\yii2\curl\Curl;
use yii\helpers\Json;

class ServiceController extends Controller
{
    public function actionIndex()
    {
        $serviceTypes = new ServiceType();
        $activeServiceTypes = $serviceTypes->findAll(['status' => ServiceType::STATUS_ACTIVE]);

        foreach ($activeServiceTypes as $activeServiceType) {
            var_dump($activeServiceType->name);
            foreach ($activeServiceType->endpoints as $endpoint) {
                $curlAction = $this->actionCurl($endpoint->endpoint);
                $responseJson = $curlAction['response'];
                $responseCode = $curlAction['responseCode'];

                if ($responseCode !== 200) {
                    continue;
                }
                $this->uploadNewData($endpoint->service_type, $endpoint->service, $responseJson);
            }
        }
    }

    private function uploadNewData($serviceType, $service, $dataJson)
    {
        switch ($serviceType) {
            case 'CN':
                $this->uploadCountries($service, $dataJson);
                break;
            case 'CT':
                //$this->uploadCities($service, $dataJson);
                break;
            case 'AP':
                //$this->uploadAirports($service, $dataJson);
                break;
            default:
        }
    }

    private function uploadCountries($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                $this->uploadCountriesFromAVS($dataJson);
                break;
            default:
        }
    }

    private function uploadCities($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                $this->uploadCitiesFromAVS($dataJson);
                break;
            default:
        }
    }

    private function uploadAirports($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                $this->uploadAirportsFromAVS($dataJson);
                break;
            default:
        }
    }

    private function uploadCountriesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        foreach ($dataArray as $item) {
            $this->addCountry([
                'code' => $item['code'],
                'name' => $item['name'],
                'currency' => $item['currency'],
            ]);
        }
    }

    private function uploadCitiesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        foreach ($dataArray as $item) {
            $this->addCity([
                'code' => $item['code'],
                'name' => $item['name'],
            ]);
        }
    }

    private function uploadAirportsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        foreach ($dataArray as $item) {
            $this->addAirport([
                'code' => $item['code'],
                'name' => $item['name'],
            ]);
        }
    }

    private function addCountry($countryData)
    {
        $country = Country::find()
            ->where([
                'code' => $countryData['code'],
            ])->one();

        if (!$country) {
            $country = new Country();
        }

        $country->code = $countryData['code'];
        $country->name = $countryData['name'];
        $country->currency = $countryData['currency'];

        return $country->save();
    }

    private function addCity($cityData)
    {
        $city = City::find()
            ->where([
                'code' => $cityData['code'],
            ])->one();

        if (!$city) {
            $city = new City();
        }

        $city->code = $cityData['code'];
        $city->name = $cityData['name'];

        return $city->save();
    }

    private function addAirport($airportData)
    {
        $airport = Airport::find()
            ->where([
                'code' => $airportData['code'],
            ])->one();

        if (!$airport) {
            $airport = new Airport();
        }

        $airport->code = $airportData['code'];
        $airport->name = $airportData['name'];

        return $airport->save();
    }

    public function actionCurl($url, array $params = [])
    {
        $curl = new Curl();
        $curl->reset()->setOption(
            CURLOPT_POSTFIELDS,
            http_build_query($params))->post($url);
        return [
            'response' => $curl->response,
            'responseCode' => $curl->responseCode,
        ];
    }
}
