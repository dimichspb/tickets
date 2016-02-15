<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_092932_adding_regions_endpoints extends Migration
{
    public function up()
    {
        $this->insert('service_type', [
            'code' => 'RG',
            'name' => 'List of regions',
            'order' => '4',
        ]);
        $this->insert('service_type', [
            'code' => 'SR',
            'name' => 'List of subregions',
            'order' => '5',
        ]);
        $this->insert('service_type', [
            'code' => 'CR',
            'name' => 'Countries to regions relation',
            'order' => '6',
        ]);
    
        
        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'RG',
            'endpoint' => 'http://api.travelpayouts.com/data/regions.json',
        ]);
        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'SR',
            'endpoint' => 'http://api.travelpayouts.com/data/subregions.json',
        ]);
        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'CR',
            'endpoint' => 'http://api.travelpayouts.com/data/countries_regions_subregions.json',
        ]);
    }

    public function down()
    {
        $this->delete('endpoint', [
            'service' => 'AVS',
            'service_type' => 'RG',
        ]);
        $this->delete('endpoint', [
            'service' => 'AVS',
            'service_type' => 'SR',
        ]);
        $this->delete('endpoint', [
            'service' => 'AVS',
            'service_type' => 'CR',
        ]);
        
        
        $this->delete('service_type', [
            'code' => 'RG',
        ]);
        $this->delete('service_type', [
            'code' => 'SR',
        ]);
        $this->delete('service_type', [
            'code' => 'CR',
        ]);
    }
}
