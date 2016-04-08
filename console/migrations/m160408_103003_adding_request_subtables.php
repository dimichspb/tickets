<?php

use yii\db\Migration;

class m160408_103003_adding_request_subtables extends Migration
{
    public function up()
    {
        $this->createTable('request_origin_city', [
            'request' => $this->integer(11)->notNull(),
            'city' => $this->string(3)->notNull(),
            'status' => $this->integer(2)->notNull()->defaultValue(0)
        ], "DEFAULT CHARSET=utf8");


        $this->addPrimaryKey('pk_request_origin_city', 'request_origin_city', [
            'request',
            'city',
        ]);
        $this->addForeignKey('fk_request_origin_city_request_id', 'request_origin_city', 'request', 'request', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_request_origin_city_city_code', 'request_origin_city', 'city', 'city', 'code', 'RESTRICT', 'CASCADE');

        $this->createTable('request_destination_city', [
            'request' => $this->integer(11)->notNull(),
            'city' => $this->string(3)->notNull(),
            'status' => $this->integer(2)->notNull()->defaultValue(0)
        ], "DEFAULT CHARSET=utf8");

        $this->addPrimaryKey('pk_request_destination_city', 'request_destination_city', [
            'request',
            'city',
        ]);
        $this->addForeignKey('fk_request_destination_city_request_id', 'request_destination_city', 'request', 'request', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_request_destination_city_city_code', 'request_destination_city', 'city', 'city', 'code', 'RESTRICT', 'CASCADE');

        $this->createTable('request_there_date', [
            'request' => $this->integer(11)->notNull(),
            'date' => $this->date()->notNull(),
            'status' => $this->integer(2)->notNull()->defaultValue(0)
        ], "DEFAULT CHARSET=utf8");

        $this->addPrimaryKey('pk_request_there_date', 'request_there_date', [
            'request',
            'date',
        ]);
        $this->addForeignKey('fk_request_there_date_request_id', 'request_there_date', 'request', 'request', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('request_travel_period', [
            'request' => $this->integer(11)->notNull(),
            'period' => $this->integer(4)->notNull(),
            'status' => $this->integer(2)->notNull()->defaultValue(0)
        ], "DEFAULT CHARSET=utf8");

        $this->addPrimaryKey('pk_request_travel_period', 'request_travel_period', [
            'request',
            'period',
        ]);
        $this->addForeignKey('fk_request_travel_period_request_id', 'request_travel_period', 'request', 'request', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('request_origin_city');
        $this->dropTable('request_destination_city');
        $this->dropTable('request_there_date');
        $this->dropTable('request_travel_period');
    }
}
