<?php

use yii\db\Schema;
use yii\db\Migration;

class m160218_074439_adding_currency_table extends Migration
{
    public function up()
    {
        $this->createTable('currency', [
            'code' => Schema::TYPE_STRING . '(3) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addPrimaryKey('pk_currency', 'currency', 'code');

        $this->insert('currency', [
            'code' => 'RUB',
            'name' => 'Russian ruble',
        ]);

        $this->addForeignKey('fk_request_currency_code', 'request', 'currency', 'currency', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_route_currency_code', 'route', 'currency', 'currency', 'code', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('fk_route_currency_code', 'route');
        $this->dropForeignKey('fk_request_currency_code', 'request');

        $this->dropPrimaryKey('pk_currency', 'currency');
        $this->dropTable('currency');
    }

}
