<?php

use yii\db\Schema;
use yii\db\Migration;

class m160218_075533_adding_route_to_request_subtable extends Migration
{
    public function up()
    {
        $this->createTable('request_to_route', [
            'request' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'route' => Schema::TYPE_INTEGER . '(11) NOT NULL',
        ]);

        $this->addPrimaryKey('pk_request_to_route', 'request_to_route', [
            'request',
            'route',
        ]);

        $this->addForeignKey('fk_request_to_route_request_id', 'request_to_route', 'request', 'request', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_request_to_route_route_id', 'request_to_route', 'route', 'route', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_request_to_route_request_id', 'request_to_route');
        $this->dropForeignKey('fk_request_to_route_route_id', 'request_to_route');

        $this->dropPrimaryKey('pk_request_to_route', 'request_to_route');

        $this->dropTable('request_to_route');
    }
}
