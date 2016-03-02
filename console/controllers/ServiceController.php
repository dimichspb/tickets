<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\Region;
use common\models\Subregion;
use common\models\Country;
use common\models\City;
use common\models\Airport;
use common\models\Route;
use common\models\Rate;
use common\models\ServiceType;

/**
 * This is console Controller for main Service actions.
 * To be used in console or Cron
 *
 */

class ServiceController extends Controller
{
    /**
     * Process all services
     */
    public function actionIndex()
    {
        ServiceType::process();
    }

    /**
     * Process Airlines service
     */
    public function actionAirlines()
    {
        ServiceType::process('AL');
    }

    /**
     * Process Regions service
     */
    public function actionRegions()
    {
        ServiceType::process('RG');
    }

    /**
     * Process Subregions service
     */
    public function actionSubregions()
    {
        ServiceType::process('SR');
    }

    /**
     * Process Countries service
     */
    public function actionCounties()
    {
        ServiceType::process('CN');
    }

    /**
     * Process Cities service
     */
    public function actionCities()
    {
        ServiceType::process('CT');
    }

    /**
     * Process Airports service
     */
    public function actionAirports()
    {
        ServiceType::process('AP');
    }

    /**
     * Process Countries-to-Regions service
     */
    public function actionCountriesRegions()
    {
        ServiceType::process('CR');
    }

    /**
     * Add Regions, Subregions, Countries, Cities, Airports to Places
     */
    public function actionPlaces()
    {
        Region::addRegionsToPlaces();
        Subregion::addSubregionsToPlaces();
        Country::addCountriesToPlaces();
        City::addCitiesToPlaces();
        Airport::addAirportsToPlaces();

    }

    /**
     * Create limited number of Routes to all (or specified) Requests
     *
     * @param null $requestId
     * @param int $limit
     */
    public function actionRoutes($requestId = NULL, $limit = 1000)
    {
        Route::createRoutes($requestId, $limit);
    }

    /**
     * Get limited number of rates to all (or specified) Requests
     *
     * @param null $requestId
     * @param int $limit
     */
    public function actionRates($requestId = NULL, $limit = 100)
    {
        Rate::getRates($requestId, $limit);
    }

    public function actionMailing()
    {

    }

    /** some stuff */

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
