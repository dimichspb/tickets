<?php

use yii\db\Schema;
use yii\db\Migration;

class m160224_110720_changing_airport_to_city_columns_at_route_table extends Migration
{
    public function up()
    {
        $this->delete('rate');
        $this->delete('request_to_route');
        $this->delete('route');
        $this->dropForeignKey('fk_route_destination_airport_code', 'route');
        $this->dropForeignKey('fk_route_origin_airport_code', 'route');

        $this->renameColumn('route', 'origin_airport', 'origin_city');
        $this->renameColumn('route', 'destination_airport', 'destination_city');

        $this->addForeignKey('fk_route_origin_city_code', 'route', 'origin_city', 'city', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_route_destination_city_code', 'route', 'destination_city', 'city', 'code', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('fk_route_destination_city_code', 'route');
        $this->dropForeignKey('fk_route_origin_city_code', 'route');

        $this->renameColumn('route', 'origin_city', 'origin_airport');
        $this->renameColumn('route', 'destination_city', 'destination_airport');

        $this->addForeignKey('fk_route_origin_airport_code', 'route', 'origin_airport', 'airport', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_route_destination_airport_code', 'route', 'destination_airport', 'airport', 'code', 'RESTRICT', 'RESTRICT');
    }

}
