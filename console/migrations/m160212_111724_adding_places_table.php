<?php

use yii\db\Schema;
use yii\db\Migration;

class m160212_111724_adding_places_table extends Migration
{
    public function up()
    {
        $this->createTable('place', [
            'id' => $this->primaryKey(11),
            'country' => Schema::TYPE_STRING . '(2) NULL',
            'city' => Schema::TYPE_STRING . '(3) NULL',
            'airport' => Schema::TYPE_STRING . '(3) NULL',
            'parent' => Schema::TYPE_INTEGER . ' NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fk_place_country_code', 'place', 'country', 'country', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_place_city_code', 'place', 'city', 'city', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_place_airport_code', 'place', 'airport', 'airport', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_place_parent_id', 'place', 'parent', 'place', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_place_parent_id', 'place');
        $this->dropForeignKey('fk_place_airport_code', 'place');
        $this->dropForeignKey('fk_place_city_code', 'place');
        $this->dropForeignKey('fk_place_country_code', 'place');
        $this->dropTable('place');
    }
}
