<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_103615_adding_service_types extends Migration
{
    public function up()
    {
        $this->insert('service_type', [
            'code' => 'CN',
            'name' => 'List of countries',
        ]);
        $this->insert('service_type', [
            'code' => 'CT',
            'name' => 'List of cities',
        ]);
        $this->insert('service_type', [
            'code' => 'AP',
            'name' => 'List of airports',
        ]);
    }

    public function down()
    {
        $this->delete('service_type', [
            'code' => 'CN',
        ]);
        $this->delete('service_type', [
            'code' => 'CT',
        ]);
        $this->delete('service_type', [
            'code' => 'AP',
        ]);
    }
}
