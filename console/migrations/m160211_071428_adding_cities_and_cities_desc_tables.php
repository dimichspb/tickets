<?php

use yii\db\Schema;
use yii\db\Migration;

class m160211_071428_adding_cities_and_cities_desc_tables extends Migration
{
    public function up()
    {
        $this->createTable('city', [
            'code' => Schema::TYPE_STRING . '(3) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'coordinates' => Schema::TYPE_STRING . ' NULL',
            'time_zone' => Schema::TYPE_STRING . ' NULL',
            'country' => Schema::TYPE_STRING . '(2) NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_city', 'city', 'code');
        $this->addForeignKey('fk_city_country_code', 'city', 'country', 'country', 'code', 'RESTRICT', 'RESTRICT');

        $this->createTable('city_desc', [
            'city' => Schema::TYPE_STRING . '(3) NOT NULL',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addPrimaryKey('pk_city_desc', 'city_desc', [
            'city',
            'language',
        ]);
        $this->addForeignKey('fk_city_desc_city_code', 'city_desc', 'city', 'city', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_city_desc_language_code', 'city_desc', 'language', 'language', 'code', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('fk_city_desc_language_code', 'city_desc');
        $this->dropForeignKey('fk_city_desc_city_code', 'city_desc');
        $this->dropTable('city_desc');

        $this->dropForeignKey('fk_city_country_code', 'city');
        $this->dropTable('city');
    }
}
