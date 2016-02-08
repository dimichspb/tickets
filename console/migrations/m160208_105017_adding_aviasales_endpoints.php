<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_105017_adding_aviasales_endpoints extends Migration
{
    public function up()
    {
        $this->insert('service', [
            'code' => 'AVS',
            'name' => 'aviasales',
        ]);

        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'CN',
            'endpoint' => 'http://api.travelpayouts.com/data/countries.json',
        ]);
        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'CT',
            'endpoint' => 'http://api.travelpayouts.com/data/cities.json',
        ]);
        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'AP',
            'endpoint' => 'http://api.travelpayouts.com/data/airports.json',
        ]);
    }

    public function down()
    {
        $this->delete('endpoint', [
            'service' => 'AVS',
        ]);
        $this->delete('service', [
            'code' => 'AVS',
        ]);
    }
}
