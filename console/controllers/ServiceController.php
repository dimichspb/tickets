<?php

namespace console\controllers;

use common\Models\Endpoint;
use common\models\Region;
use common\models\RegionDesc;
use common\models\Subregion;
use common\models\SubregionDesc;
use common\models\Country;
use common\models\CountryDesc;
use common\models\City;
use common\models\CityDesc;
use common\models\Airport;
use common\models\AirportDesc;
use common\models\Airline;
use common\models\Language;
use common\Models\Place;
use common\models\Route;
use common\models\Rate;
use yii\console\Controller;
use common\models\ServiceType;
use linslin\yii2\curl\Curl;
use yii\helpers\Json;
use yii\helpers\Console;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

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
        $this->addRegionsToPlaces();
        $this->addSubregionsToPlaces();
        $this->addCountriesToPlaces();
        $this->addCitiesToPlaces();
        $this->addAirportsToPlaces();

    }

    private function addRegionsToPlaces()
    {
        $regions = Region::find()->all();
        $totalItems = count($regions);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Regions, total items: ' . $totalItems . PHP_EOL);

        foreach($regions as $region) {
            $this->addNewPlace([
                'region' => $region->code,
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }
    
    private function addSubregionsToPlaces()
    {
        $subregions = Subregion::find()->all();
        $totalItems = count($subregions);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Subregions, total items: ' . $totalItems . PHP_EOL);

        foreach($subregions as $subregion) {
            $region = Region::getRegionByCode($subregion->region);
            $parent = Place::getPlaceByRegionCode($region->code);
            $this->addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'parent' => $parent->id,
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }

    private function addCountriesToPlaces()
    {
        $countries = Country::find()->all();
        $totalItems = count($countries);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Countries, total items: ' . $totalItems . PHP_EOL);

        foreach($countries as $country) {
            $subregion = Subregion::getSubregionByCode($country->subregion);
            $region = Region::getRegionByCode($subregion->region);
            $parent = Place::getPlaceBySubregionCode($subregion->code);
            $this->addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'country' => $country->code,
                'parent' => $parent->id,
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
            $subregion = Subregion::getSubregionByCode($country->subregion);
            $region = Region::getRegionByCode($subregion->region);
            $parent = Place::getPlaceByCountryCode($country->code);
            $this->addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'country' => $country->code,
                'city' => $city->code,
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
            $subregion = Subregion::getSubregionByCode($country->subregion);
            $region = Region::getRegionByCode($subregion->region);
            $parent = Place::getPlaceByCityCode($city->code);

            $this->addNewPlace([
                'region' => $region->code,
                'subregion' => $subregion->code,
                'country' => $country->code,
                'city' => $city->code,
                'airport' => $airport->code,
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
                $this->uploadSubregions($service, $dataJson);
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
                break;
            case 'AL':
                $this->uploadAirlines($service, $dataJson);
                break;
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

    private function uploadSubregions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                $this->uploadSubregionsFromAVS($dataJson);
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
    
    private function uploadCountriesToRegions($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                $this->uploadCountriesToRegionsFromAVS($dataJson);
                break;
            default:
        }
    }

    private function uploadAirlines($service, $dataJson)
    {
        switch ($service) {
            case 'AVS':
                $this->uploadAirlinesFromAVS($dataJson);
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
    
    private function uploadSubregionsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Subregions, total items: '. $totalItems . PHP_EOL);
        
        foreach ($dataArray as $item) {
            $this->addSubregion([
                'code' => $item['code'],
                'region' => $item['region'],
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

    private function uploadCountriesToRegionsFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding CountriesToRegions, total items: '. $totalItems . PHP_EOL);


        foreach ($dataArray as $item) {
            $this->updateCountryRegion([
                'code' => $item['country'],
                'region' => $item['region'],
                'subregion' => $item['subregion'],
            ]);
            
            $this->updateCitiesRegionsByCountry([
                'country' => $item['country'],
                'region' => $item['region'],
                'subregion' => $item['subregion'],
            ]);
            
            $this->updateAirportsRegionsByCountry([
                'country' => $item['country'],
                'region' => $item['region'],
                'subregion' => $item['subregion'],
            ]);
            $this->progressBar($totalItems, $currentItem);
        }
    }

    private function uploadAirlinesFromAVS($dataJson)
    {
        $dataArray = Json::decode($dataJson);
        $totalItems = count($dataArray);
        $currentItem = 0;
        $this->stdout(PHP_EOL . 'Adding Airlines, total items: '. $totalItems . PHP_EOL);

        foreach ($dataArray as $item) {
            $this->addAirline([
                'name' => $item['name'],
                'alias' => $item['alias'],
                'iata' => $item['iata'],
                'icao' => $item['icao'],
                'callsign' => $item['callsign'],
                'country' => $item['country'],
                'is_active' => $item['is_active'],
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
    
    
    private function addSubregion(array $subregionData)
    {
        $subregion = Subregion::getSubregionByCode($subregionData['code']);
        
        if (!$subregion) {
            $subregion = new Subregion();
            $subregion->code = $subregionData['code'];
        }
        
        $subregion->region = $subregionData['region'];
        $subregion->name = $subregionData['name'];
        
        $result = $subregion->save();

        if ($result && isset($subregionData['description'])) {
            $this->addSubregionDescriptions($subregion, $subregionData['description']);
        }
        
        return $result;
    }

    private function addSubregionDescriptions(Subregion $subregion, array $subregionDataArray)
    {
        if (count($subregionDataArray)>0)
        {
            foreach ($subregionDataArray as $subregionDataIndex => $subregionDataValue) {
                $this->addSubregionDescription($subregion, $subregionDataIndex, $subregionDataValue);
            }
        }
    }

    private function addSubregionDescription(Subregion $subregion, $subregionDataIndex, $subregionDataValue)
    {
        $language = Language::getLanguageByCode($subregionDataIndex);

        $subregionDesc = SubregionDesc::findOne([
                'subregion' => $subregion->code,
                'language' => $language->code,
            ]);

        if (!$subregionDesc) {
            $subregionDesc = new SubregionDesc();
            $subregionDesc->subregion = $subregion->code;
            $subregionDesc->language = $language->code;
        }

        $subregionDesc->name = $subregionDataValue;

        return $subregionDesc->save();
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

    private function addAirline($airlineData)
    {
        $airline = Airline::getAirlineByName($airlineData['name']);

        if (!$airline) {
            $airline = new Airline();
            $airline->name = $airlineData['name'];
        }

        $airline->alias = $airlineData['alias'];
        $airline->iata = $airlineData['iata'];
        $airline->icao = $airlineData['icao'];
        $airline->callsign = $airlineData['callsign'];
        $airline->country = Country::getCountryByName($airlineData['country'])? Country::getCountryByName($airlineData['country'])->code: NULL;
        $airline->is_active = $airlineData['is_active'];

        $result = $airline->save();

        return $result;
    }

    private function updateCountryRegion($countryData)
    {
        $country = Country::getCountryByCode($countryData['code']);

        if (!$country) {
            $country = new Country();
            $country->code = $countryData['code'];
        }

        $country->region = $countryData['region'];
        $country->subregion = $countryData['subregion'];

        $result = $country->save();

        return $result;
    }
    
    private function updateCitiesRegionsByCountry($countryData)
    {
        $cities = City::findAll([
            'country' => $countryData['country'],
        ]);

        foreach ($cities as $city) {
            $city->region = $countryData['region'];
            $city->subregion = $countryData['subregion'];
            $city->save();
        }
    }
    
    private function updateAirportsRegionsByCountry($countryData)
    {
        $airports = Airport::findAll([
            'country' => $countryData['country'],
        ]);

        foreach ($airports as $airport) {
            $airport->region = $countryData['region'];
            $airport->subregion = $countryData['subregion'];
            $airport->save();
        }
    }

    private function actionCurl($url, array $params = [])
    {
        $curl = new Curl();
        $urlQuery = $url . "?" . http_build_query($params);
        $curl->get($urlQuery);
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


    public function actionRates()
    {
        $routesToUpdate = Route::getRoutesWithOldRate();

        $activeRateService = ServiceType::findOne([
                'status' => ServiceType::STATUS_ACTIVE,
                'code' => 'DR',
            ]);

        if (!$activeRateService || !$routesToUpdate) {
            return;
        }

        $this->stdout(PHP_EOL . $activeRateService->name . PHP_EOL);
        foreach ($activeRateService->endpoints  as $endpoint) {
            $this->getRatesFromService($endpoint, $routesToUpdate);
        }
    }

    private function getRatesFromService(Endpoint $endpoint, array $routesToUpdate)
    {
        switch ($endpoint->service) {
            case 'AVS':
                $this->getRatesFromAVS($endpoint, $routesToUpdate);
                break;
            default:
        }
    }

    private function getRatesFromAVS(Endpoint $endpoint, array $routesToUpdate)
    {
        foreach ($routesToUpdate as $routeToUpdate) {
            $curlAction = $this->actionCurl($endpoint->endpoint, [
                'currency' => $routeToUpdate->currency,
                'origin' => $routeToUpdate->origin_city,
                'destination' => $routeToUpdate->destination_city,
                'depart_date' => \Yii::$app->formatter->asDate($routeToUpdate->there_date, 'php:Y-m-d'),
                'return_date' => \Yii::$app->formatter->asDate($routeToUpdate->back_date, 'php:Y-m-d'),
                'token' => $endpoint->getService()->token,
            ]);

            $responseJson = $curlAction['response'];
            $responseCode = $curlAction['responseCode'];

            if ($responseCode !== 200) {
                continue;
            }
            $this->addRatesAVS($endpoint, $routeToUpdate, $responseJson);
        }
    }

    private function addRatesAVS(Endpoint $endpoint, Route $route, $dataJson)
    {
        $data = Json::decode($dataJson);

        foreach ($data['data'] as $destinationItemIndex => $destinationItemData) {
            foreach ($destinationItemData as $destinationDataItem) {
                $rate = new Rate();
                $rate->route = $route->id;
                $rate->origin_city = $route->origin_city;
                $rate->destination_city = $destinationItemIndex;
                $rate->there_date = \Yii::$app->formatter->asDatetime($destinationDataItem['departure_at'],'php:Y-m-d H:i:s');
                $rate->back_date = \Yii::$app->formatter->asDatetime($destinationDataItem['return_at'],'php:Y-m-d H:i:s');
                $rate->service = $endpoint->service;
                $rate->airline = Airline::getAirlineByName($destinationDataItem['airline'])->id;
                $rate->flight_number = (string)$destinationDataItem['flight_number'];
                $rate->currency = $route->currency;
                $rate->price = (float)$destinationDataItem['price'];

                if ($rate->validate()) {
                    $rate->save();
                }
            }

        }

    }
        /*
        public function actionMerge()
        {
            $rootDir = dirname(dirname(dirname(__DIR__)));

            $firstJSONFile = $rootDir . '\APIFake\data\subregions.json';
            $secondJSONFile = $rootDir . '\APIFake\data\subregions_desc.json';
            $newJSONFile = $rootDir . '\APIFake\data\subregion.json';



            if (!file_exists($firstJSONFile)) {
                throw new \InvalidArgumentException("First JSON file is missing");
            }
            if (!file_exists($secondJSONFile)) {
                throw new \InvalidArgumentException("Second JSON file is missing");
            }

            $firstJSONArray = JSON::decode(file_get_contents($firstJSONFile));
            $secondJSONArray = JSON::decode(file_get_contents($secondJSONFile));

            $secondJSONArrayCodesList = array_column($secondJSONArray, 'code');
            //var_dump($secondJSONArrayCodesList);
            foreach ($firstJSONArray as &$firstJSONItem) {
                $key = array_search($firstJSONItem['code'], $secondJSONArrayCodesList);
                //var_dump($firstJSONItem['code']);
                //var_dump($key);
                //var_dump($secondJSONArray[$key]);
                array_shift($secondJSONArray[$key]); //remove "code" element
                $firstJSONItem['name_translations'] = $secondJSONArray[$key];
            }

            echo $newJSONFile;
            //var_dump($firstJSONArray);
            file_put_contents($newJSONFile, JSON::encode($firstJSONArray));
        }
        */
    
}
