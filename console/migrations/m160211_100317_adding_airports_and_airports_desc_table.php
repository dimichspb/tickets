<?php

use yii\db\Schema;
use yii\db\Migration;

class m160211_100317_adding_airports_and_airports_desc_table extends Migration
{
    public function up()
    {
        $this->createTable('airport', [
            'code' => Schema::TYPE_STRING . '(3) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
            'coordinates' => Schema::TYPE_STRING . ' NULL',
            'time_zone' => Schema::TYPE_STRING . ' NULL',
            'country' => Schema::TYPE_STRING . '(2) NOT NULL',
            'city' => Schema::TYPE_STRING . '(3) NOT NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addPrimaryKey('pk_airport_code', 'airport', 'code');
        $this->addForeignKey('fk_airport_country_code', 'airport', 'country', 'country', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_airport_city_code', 'airport', 'city', 'city', 'code', 'RESTRICT', 'RESTRICT');

        $this->createTable('airport_desc', [
            'airport' => Schema::TYPE_STRING . '(3) NOT NULL',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addPrimaryKey('pk_airport_desc', 'airport_desc', [
            'airport',
            'language',
        ]);
        $this->addForeignKey('fk_airport_desc_airport_code', 'airport_desc', 'airport', 'airport', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_airport_desc_language_code', 'airport_desc', 'language', 'language', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_airport_desc_language_code', 'airport_desc');
        $this->dropForeignKey('fk_airport_desc_airport_code', 'airport_desc');
        $this->dropTable('airport_desc');

        $this->dropForeignKey('fk_airport_city_code', 'airport');
        $this->dropForeignKey('fk_airport_country_code', 'airport');
        $this->dropTable('airport');
    }

}
