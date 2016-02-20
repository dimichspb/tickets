<?php

use yii\db\Schema;
use yii\db\Migration;

class m160220_115437_adding_rates_table extends Migration
{
    public function up()
    {
        $this->createTable('rate', [
            'id' => $this->primaryKey(11),
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'route' => Schema::TYPE_INTEGER . ' NOT NULL',
            'origin_place' => Schema::TYPE_INTEGER . ' NOT NULL',
            'destination_place' => Schema::TYPE_INTEGER . ' NOT NULL',
            'there_date' => Schema::TYPE_DATETIME . ' NOT NULL',
            'back_date' => Schema::TYPE_DATETIME . ' NULL',
            'airline' => Schema::TYPE_INTEGER . ' NOT NULL',
            'flight_number' => Schema::TYPE_STRING . '(5) NOT NULL',
            'currency' => Schema::TYPE_STRING . '(3) NOT NULL',
            'price' => Schema::TYPE_DECIMAL . '(10,2) NOT NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fk_rate_route_id', 'rate', 'route', 'route', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_rate_origin_place_id', 'rate', 'origin_place', 'place', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_rate_destination_place_id', 'rate', 'destination_place', 'place', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_rate_airline_id', 'rate', 'airline', 'airline', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_rate_currency_code', 'rate', 'origin_place', 'place', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('fk_rate_currency_code', 'rate');
        $this->dropForeignKey('fk_rate_airline_id', 'rate');
        $this->dropForeignKey('fk_rate_destination_place_id', 'rate');
        $this->dropForeignKey('fk_rate_origin_place_id', 'rate');
        $this->dropForeignKey('fk_rate_route_id', 'rate');

        $this->dropTable('rate');
    }
}
