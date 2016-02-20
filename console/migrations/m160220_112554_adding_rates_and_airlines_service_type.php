<?php

use yii\db\Schema;
use yii\db\Migration;

class m160220_112554_adding_rates_and_airlines_service_type extends Migration
{
    public function up()
    {
        $this->insert('service_type', [
            'code' => 'DR',
            'name' => 'Daily cheapest rate',
            'status' => '0',
            'order' => '8',
        ]);

        $this->insert('service_type', [
            'code' => 'AL',
            'name' => 'Airlines list',
            'status' => '0',
            'order' => '7',
        ]);
    }

    public function down()
    {
        $this->delete('service_type', [
            'code' => 'AL',
        ]);
        $this->delete('service_type', [
            'code' => 'DR',
        ]);
    }

}
