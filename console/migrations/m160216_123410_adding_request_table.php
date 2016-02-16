<?php

use yii\db\Schema;
use yii\db\Migration;

class m160216_123410_adding_request_table extends Migration
{
    public function up()
    {
        $this->createTable('request', [
            'id' => $this->primaryKey(11),
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'user' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'origin' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'destination' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'there_start_date' => Schema::TYPE_DATETIME . ' NOT NULL',
            'there_end_date' => Schema::TYPE_DATETIME . ' NOT NULL',
            'back_start_date' => Schema::TYPE_DATETIME . ' NULL',
            'back_end_date' => Schema::TYPE_DATETIME . ' NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fk_request_user_id', 'request', 'user', 'user', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_request_origin_place_code', 'request', 'origin', 'place', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_request_destination_place_code', 'request', 'destination', 'place', 'id', 'RESTRICT', 'RESTRICT');


    }

    public function down()
    {
        $this->dropForeignKey('fk_request_user_id', 'request');
        $this->dropForeignKey('fk_request_destination_place_code', 'request');
        $this->dropForeignKey('fk_request_origin_place_code', 'request');
        $this->dropTable('request');
    }

}
