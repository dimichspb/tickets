<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_102100_adding_services_tables extends Migration
{
    public function up()
    {
        $this->createTable('service', [
            'code' => Schema::TYPE_STRING . '(3) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_code', 'service', 'code');

        $this->createTable('service_type', [
            'code' => Schema::TYPE_STRING . '(2) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_code', 'service_type', 'code');

        $this->createTable('endpoint', [
            'service' => Schema::TYPE_STRING . '(3) NOT NULL',
            'service_type' => Schema::TYPE_STRING . '(2) NOT NULL',
            'endpoint' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fk_endpoint_service_code', 'endpoint', 'service', 'service', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_endpoint_service_type_code', 'endpoint', 'service_type', 'service_type', 'code', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropForeignKey('fk_endpoint_service_type_code', 'endpoint');
        $this->dropForeignKey('fk_endpoint_service_code', 'endpoint');
        $this->dropTable('endpoint');
        $this->dropTable('service_type');
        $this->dropTable('service');
    }
}
