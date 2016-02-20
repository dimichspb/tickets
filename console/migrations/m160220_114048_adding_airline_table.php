<?php

use yii\db\Schema;
use yii\db\Migration;

class m160220_114048_adding_airline_table extends Migration
{
    public function up()
    {
        $this->createTable('airline', [
            'id' => $this->primaryKey(11),
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'alias' => Schema::TYPE_STRING . ' NULL',
            'iata' => Schema::TYPE_STRING . '(2) NULL',
            'icao' => Schema::TYPE_STRING . '(3) NULL',
            'callsign' => Schema::TYPE_STRING . ' NULL',
            'country' => Schema::TYPE_STRING  . '(2) NULL',
            'is_active' => Schema::TYPE_BOOLEAN,
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fk_airline_country_code', 'airline', 'country', 'country', 'code', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('fk_airline_country_code', 'airline');

        $this->dropTable('airline');
    }
}
