<?php

namespace console\controllers;

use common\models\Region;
use common\models\RegionDesc;
use common\models\Country;
use common\models\CountryDesc;
use common\models\City;
use common\models\CityDesc;
use common\models\Airport;
use common\models\AirportDesc;
use common\models\Language;
use common\Models\Place;
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

    public function actionPlaces()
    {
        $this->addCountriesToPlaces();
        $this->addCitiesToPlaces();
        $this->addAirportsToPlaces();

    }

    private function addCountriesToPlaces()
    {
        $countries = Country::find()->all();
        $totalItems = count($countries);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Countries, total items: ' . $totalItems . PHP_EOL);

        foreach($countries as $country) {
            $this->addNewPlace([
                'country' => $country->code,
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }

    private function addCitiesToPlaces()
    {
        $cities = City::find()->all();
        $totalItems = count($cities);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Cities, total items: ' . $totalItems . PHP_EOL);

        foreach($cities as $city) {
            $country = Country::getCountryByCode($city->country);
            $parent = Place::getPlaceByCountryCode($country->code);
            $this->addNewPlace([
                'city' => $city->code,
                'country' => $country->code,
                'parent' => $parent->id,
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }

    private function addAirportsToPlaces()
    {
        $airports = Airport::find()->all();
        $totalItems = count($airports);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Airports, total items: ' . $totalItems . PHP_EOL);

        foreach ($airports as $airport) {
            $city = City::getCityByCode($airport->city);
            $country = Country::getCountryByCode($city->country);
            $parent = Place::getPlaceByCityCode($city->code);

            $this->addNewPlace([
                'airport' => $airport->code,
                'city' => $city->code,
                'country' => $country->code,
                'parent' => $parent->id,
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }

    private function addNewPlace(array $placeData)
    {
        $place = Place::findOne($placeData);
        if (!$place) {
            $place = new Place();
            $place->setAttributes($placeData);
            $place->save();
        }
        return $place;
    }

    private function uploadNewData($serviceType, $service, $dataJson)
    {
        switch ($serviceType) {
            case 'RG':
                $this->uploadRegions($service, $dataJson);
                break;
            case 'SR':
                $this->uploadSubRegions($service, $dataJson);
                break;
            case 'CN':
                $this->uploadCountries($service, $dataJson);
                break;
            case 'CT':
                $this->uploadCities($service, $dataJson);
                break;
            case 'AP':
                $this->uploadAirports($service, $dataJson);
                break;
            case 'CR': 
                $this->uploadCountriesToRegions($service, $dataJson);
            default:
        }
    }

    private function uploadRegions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                $this->uploadRegionsFromAVS($dataJson);
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

    private function uploadRegionsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Regions, total items: '. $totalItems . PHP_EOL);
        
        foreach ($dataArray as $item) {
            $this->addRegion([
                'code' => $item['code'],
                'name' => $item['name'],
                'description' => $item['name_translations'],
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }
    private function uploadCountriesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Countries, total items: '. $totalItems . PHP_EOL);


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
        $this->stdout(PHP_EOL . 'Adding Cities, total items: '. $totalItems . PHP_EOL);

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
        $this->stdout(PHP_EOL . 'Adding airports, total items: '. $totalItems . PHP_EOL);

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

    private function addRegion($regionData)
    {
        $region = Region::getRegionByCode($regionData['code']);
        
        if (!$region) {
            $region = new Region();
            $region->code = $regionData['code'];
        }
        
        $region->name = $regionData['name'];
        
        $result = $region->save();
        
        if ($result && isset($regionData['description'])) {
            $this->addRegionDescriptions($region, $regionData['description']);
        }
        
        return $result;
    }

    private function addRegionDescriptions(Region $region, array $regionDataArray)
    {
        if (count($regionDataArray)>0)
        {
            foreach ($regionDataArray as $regionDataIndex => $regionDataValue) {
                $this->addRegionDescription($region, $regionDataIndex, $regionDataValue);
            }
        }
    }

    private function addRegionDescription(Region $region, $regionDataIndex, $regionDataValue)
    {
        $language = Language::getLanguageByCode($regionDataIndex);

        $regionDesc = RegionDesc::findOne([
                'region' => $region->code,
                'language' => $language->code,
            ]);

        if (!$regionDesc) {
            $regionDesc = new RegionDesc();
            $regionDesc->region = $region->code;
            $regionDesc->language = $language->code;
        }

        $regionDesc->name = $regionDataValue;

        return $regionDesc->save();
    }    

    private function addCountry($countryData)
    {
        $country = Country::getCountryByCode($countryData['code']);

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

        $countryDesc = CountryDesc::findOne([
                'country' => $country->code,
                'language' => $language->code,
            ]);

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

        $city = City::getCityByCode($cityData['code']);

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

        $cityDesc = CityDesc::findOne([
                'city' => $city->code,
                'language' => $language->code,
            ]);

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

        $airport = Airport::getAirportByCode($airportData['code']);

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

        $airportDesc = AirportDesc::findOne([
                'airport' => $airport->code,
                'language' => $language->code,
            ]);

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

    private function progressBar($totalItems, &$currentItem = 0, $step = 10)
    {
        if ($currentItem++ > $totalItems / $step) {
            Console::stdout('.');
            $currentItem = 0;
        }
    }
    
    
    /*
    public function actionMerge()
    {
        $rootDir = dirname(dirname(dirname(__DIR__)));

        $firstJSONFile = $rootDir . "\APIFake\data\segions.json";
        $secondJSONFile = $rootDir . "\APIFake\data\segions_desc.json";
        $newJSONFile = $rootDir . "\APIFake\data\segions.json";
       
        
                
        if (!file_exists($firstJSONFile)) {
            throw new \InvalidArgumentException("First JSON file is missing");
        }
        if (!file_exists($secondJSONFile)) {
            throw new \InvalidArgumentException("Second JSON file is missing");
        }
        
        $firstJSONArray = JSON::decode(file_get_contents($firstJSONFile));
        $secondJSONArray = JSON::decode(file_get_contents($secondJSONFile));
        
        //var_dump($secondJSONArray);
        //var_dump(array_column($secondJSONArray, 'code'));
        foreach ($firstJSONArray as &$firstJSONItem) {
            $key = (array_search($firstJSONItem['code'], array_column($secondJSONArray, 'code')));
            //var_dump($firstJSONItem['code']);
            //var_dump($secondJSONArray[$key]);
            array_shift($secondJSONArray[$key]);
            $firstJSONItem['name_translations'] = $secondJSONArray[$key];
        }
        
        echo $newJSONFile;
        file_put_contents($newJSONFile, JSON::encode($firstJSONArray));
    }
    */
}
