<?php

namespace console\controllers;

use common\models\Country;
use common\models\CountryDesc;
use common\models\City;
use common\models\CityDesc;
use common\models\Airport;
use common\models\AirportDesc;
use common\models\Language;
use yii\console\Controller;
use common\models\ServiceType;
use linslin\yii2\curl\Curl;
use yii\helpers\Json;
use yii\helpers\Console;

class ServiceController extends Controller
{
    public function actionIndex()
    {
        $activeServiceTypes = ServiceType::find()
            ->where(['status' => ServiceType::STATUS_ACTIVE])
            ->orderBy('order')
            ->all();

        foreach ($activeServiceTypes as $activeServiceType) {
            $this->stdout(PHP_EOL . $activeServiceType->name);
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
                $this->uploadCities($service, $dataJson);
                break;
            case 'AP':
                $this->uploadAirports($service, $dataJson);
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

        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Total items: '. $totalItems . PHP_EOL);


        foreach ($dataArray as $item) {
            $this->addCountry([
                'code' => $item['code'],
                'name' => $item['name'],
                'currency' => $item['currency'],
                'description' => $item['name_translations'],
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }

    private function uploadCitiesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Total items: '. $totalItems . PHP_EOL);

        foreach ($dataArray as $item) {
            $this->addCity([
                'code' => $item['code'],
                'name' => $item['name'],
                'coordinates' => serialize($item['coordinates']),
                'description' => $item['name_translations'],
                'time_zone' => $item['time_zone'],
                'country' => $item['country_code'],
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }

    private function uploadAirportsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Total items: '. $totalItems . PHP_EOL);

        foreach ($dataArray as $item) {
            $this->addAirport([
                'code' => $item['code'],
                'name' => $item['name'],
                'coordinates' => serialize($item['coordinates']),
                'description' => $item['name_translations'],
                'time_zone' => $item['time_zone'],
                'country' => $item['country_code'],
                'city' => $item['city_code'],
            ]);
            $this->progressBar($totalItems, $currentItem);
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
            $country->code = $countryData['code'];
        }

        $country->name = $countryData['name'];
        $country->currency = !empty($countryData['currency'])? $countryData['currency']: NULL;

        $result = $country->save();

        if ($result && isset($countryData['description'])) {
            $this->addCountryDescriptions($country, $countryData['description']);
        }

        return $result;
    }

    private function addCountryDescriptions(Country $country, array $countryDataArray)
    {
        if (count($countryDataArray)>0)
        {
            foreach ($countryDataArray as $countryDataIndex => $countryDataValue) {
                $this->addCountryDescription($country, $countryDataIndex, $countryDataValue);
            }
        }
    }

    private function addCountryDescription(Country $country, $countryDataIndex, $countryDataValue)
    {
        $language = Language::getLanguageByCode($countryDataIndex);

        $countryDesc = CountryDesc::find()
            ->where([
                'country' => $country->code,
                'language' => $language->code,
            ])->one();

        if (!$countryDesc) {
            $countryDesc = new CountryDesc();
            $countryDesc->country = $country->code;
            $countryDesc->language = $language->code;
        }

        $countryDesc->name = $countryDataValue;

        return $countryDesc->save();
    }

    private function addCity($cityData)
    {
        $country = Country::getCountryByCode($cityData['country']);

        $city = City::find()
            ->where([
                'code' => $cityData['code'],
            ])->one();

        if (!$city) {
            $city = new City();
            $city->code = $cityData['code'];
        }

        $city->name = $cityData['name'];
        $city->coordinates = $cityData['coordinates'];
        $city->time_zone = $cityData['time_zone'];
        $city->country = $country->code;

        $result = $city->save();

        if ($result && isset($cityData['description'])) {
            $this->addCityDescriptions($city, $cityData['description']);
        }

        return $result;
    }

    private function addCityDescriptions(City $city, array $cityDataArray)
    {
        if (count($cityDataArray)>0)
        {
            foreach ($cityDataArray as $cityDataIndex => $cityDataValue) {
                $this->addCityDescription($city, $cityDataIndex, $cityDataValue);
            }
        }
    }

    private function addCityDescription(City $city, $cityDataIndex, $cityDataValue)
    {
        $language = Language::getLanguageByCode($cityDataIndex);

        $cityDesc = CityDesc::find()
            ->where([
                'city' => $city->code,
                'language' => $language->code,
            ])->one();

        if (!$cityDesc) {
            $cityDesc = new CityDesc();
            $cityDesc->city = $city->code;
            $cityDesc->language = $language->code;
        }

        $cityDesc->name = $cityDataValue;

        return $cityDesc->save();
    }

    private function addAirport($airportData)
    {
        $country = Country::getCountryByCode($airportData['country']);
        $city = City::getCityByCode($airportData['city']);

        $airport = Airport::find()
            ->where([
                'code' => $airportData['code'],
            ])->one();

        if (!$airport) {
            $airport = new Airport();
            $airport->code = $airportData['code'];
        }

        $airport->name = $airportData['name'];
        $airport->time_zone = $airportData['time_zone'];
        $airport->coordinates = $airportData['coordinates'];
        $airport->country = $country->code;
        $airport->city = $city->code;

        $result = $airport->save();

        if ($result && isset($airportData['description'])) {
            $this->addAirportDescriptions($airport, $airportData['description']);
        }
    }

    private function addAirportDescriptions(Airport $airport, array $airportDataArray)
    {
        if (count($airportDataArray)>0)
        {
            foreach ($airportDataArray as $airportDataIndex => $airportDataValue) {
                $this->addAirportDescription($airport, $airportDataIndex, $airportDataValue);
            }
        }
    }

    private function addAirportDescription(Airport $airport, $airportDataIndex, $airportDataValue)
    {
        $language = Language::getLanguageByCode($airportDataIndex);

        $airportDesc = AirportDesc::find()
            ->where([
                'airport' => $airport->code,
                'language' => $language->code,
            ])->one();

        if (!$airportDesc) {
            $airportDesc = new AirportDesc();
            $airportDesc->airport = $airport->code;
            $airportDesc->language = $language->code;
        }

        $airportDesc->name = $airportDataValue;

        return $airportDesc->save();
    }

    private function actionCurl($url, array $params = [])
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

    private function progressBar($totalItems, &$currentItem, $step = 10)
    {
        if ($currentItem++ > $totalItems / $step) {
            Console::stdout('.');
            $currentItem = 0;
        }
    }
}
