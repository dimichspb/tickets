<?php

use yii\db\Schema;
use yii\db\Migration;

class m160314_074421_fixing_local_services_paths extends Migration
{
    public function up()
    {
        $this->update('service_type', [
            'status' => 0,
        ]);

        $this->update('endpoint', [
            'endpoint' => '/data/regions.json',
        ], [
            'service' => 'AVS',
            'service_type' => 'RG',
        ]);
        $this->update('endpoint', [
            'endpoint' => '/data/subregions.json',
        ], [
            'service' => 'AVS',
            'service_type' => 'SR',
        ]);
        $this->update('endpoint', [
            'endpoint' => '/data/countries_regions_subregions.json',
        ], [
            'service' => 'AVS',
            'service_type' => 'CR',
        ]);
    }

    public function down()
    {

    }

}
