<?php

use yii\db\Schema;
use yii\db\Migration;

class m160220_113029_adding_AVS_daily_rates_and_airlines_endpoint extends Migration
{
    public function up()
    {
        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'DR',
            'endpoint' => 'http://api.travelpayouts.com/v1/prices/direct',
        ]);

        $this->insert('endpoint', [
            'service' => 'AVS',
            'service_type' => 'AL',
            'endpoint' => 'http://api.travelpayouts.com/data/airlines.json',
        ]);
    }

    public function down()
    {
        $this->delete('endpoint', [
            'service' => 'AVS',
            'service_type' => 'AL',
        ]);
        $this->delete('endpoint', [
            'service' => 'AVS',
            'service_type' => 'DR',
        ]);
    }
}
