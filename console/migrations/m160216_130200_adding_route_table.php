<?php

use yii\db\Schema;
use yii\db\Migration;

class m160216_130200_adding_route_table extends Migration
{
    public function up()
    {
        $this->createTable('route', [
            'id' => $this->primaryKey(11),
            'origin_airport' => Schema::TYPE_STRING . '(3) NOT NULL',
            'destination_airport' => Schema::TYPE_STRING . '(3) NOT NULL',
            'there_date' => Schema::TYPE_DATETIME . ' NOT NULL',
            'back_date' => Schema::TYPE_DATETIME . ' NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fk_route_origin_airport_code', 'route', 'origin_airport', 'airport', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_route_destination_airport_code', 'route', 'destination_airport', 'airport', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_route_destination_airport_code', 'route');
        $this->dropForeignKey('fk_route_origin_airport_code', 'route');
        $this->dropTable('route');
    }
}
