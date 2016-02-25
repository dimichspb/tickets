<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\Region;
use common\models\Subregion;
use common\models\Country;
use common\models\City;
use common\models\Airport;
use common\models\Rate;
use common\models\ServiceType;

class ServiceController extends Controller
{
    public function actionIndex()
    {
        ServiceType::process();
    }

    public function actionPlaces()
    {
        Region::addRegionsToPlaces();
        Subregion::addSubregionsToPlaces();
        Country::addCountriesToPlaces();
        City::addCitiesToPlaces();
        Airport::addAirportsToPlaces();

    }

    public function actionRates()
    {
        Rate::getRates();
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
