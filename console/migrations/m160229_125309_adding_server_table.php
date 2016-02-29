<?php

use yii\db\Schema;
use yii\db\Migration;

class m160229_125309_adding_server_table extends Migration
{
    public function up()
    {
        $this->createTable('server', [
            'code' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_server', 'server', 'code');

        $this->createTable('server_type', [
            'code' => Schema::TYPE_STRING . '(4) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_server_type', 'server_type', 'code');

        $this->createTable('server_to_server_type', [
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'server' => Schema::TYPE_STRING . '(5) NOT NULL',
            'server_type' => Schema::TYPE_STRING . '(4) NOT NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_server_to_server_type', 'server_to_server_type',[
            'create_date',
            'server',
            'server_type',
        ]);
        $this->addForeignKey('fk_server_to_server_type_server_code', 'server_to_server_type', 'server', 'server', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_server_to_server_type_server_type_code', 'server_to_server_type', 'server_type', 'server_type', 'code', 'RESTRICT', 'RESTRICT');

        $this->createTable('server_detail', [
            'code' => Schema::TYPE_STRING . '(4) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_server_detail', 'server_detail', 'code');

        $this->createTable('server_detail_to_server_type', [
            'server_type' => Schema::TYPE_STRING . '(4) NOT NULL',
            'server_detail' => Schema::TYPE_STRING . '(4) NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_server_detail_to_server_type', 'server_detail_to_server_type', [
            'server_type',
            'server_detail',
        ]);
        $this->addForeignKey('fk_server_detail_to_server_type_server_type_code', 'server_detail_to_server_type', 'server_type', 'server_type', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_server_detail_to_server_type_server_detail_code', 'server_detail_to_server_type', 'server_detail', 'server_detail', 'code', 'RESTRICT', 'RESTRICT');

        $this->createTable('server_configuration', [
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'server' => Schema::TYPE_STRING . '(5) NOT NULL',
            'server_type' => Schema::TYPE_STRING . '(4) NOT NULL',
            'server_detail' => Schema::TYPE_STRING . '(4) NOT NULL',
            'value' => Schema::TYPE_STRING . ' NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_server_configuration', 'server_configuration', [
            'server',
            'server_type',
            'server_detail',
        ]);

        $this->addForeignKey('fk_server_configuration_server_code', 'server_configuration', 'server', 'server', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_server_configuration_server_type_code', 'server_configuration', 'server_type', 'server_type', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_server_configuration_server_detail_code', 'server_configuration', 'server_detail', 'server_detail', 'code', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('fk_server_configuration_server_detail_code', 'server_configuration');
        $this->dropForeignKey('fk_server_configuration_server_type_code', 'server_configuration');
        $this->dropForeignKey('fk_server_configuration_server_code', 'server_configuration');
        $this->dropPrimaryKey('pk_server_configuration', 'server_configuration');
        $this->dropTable('server_configuration');

        $this->dropForeignKey('fk_server_detail_to_server_type_server_detail_code', 'server_detail_to_server_type');
        $this->dropForeignKey('fk_server_detail_to_server_type_server_type_code', 'server_detail_to_server_type');
        $this->dropPrimaryKey('pk_server_detail_to_server_type', 'server_detail_to_server_type');
        $this->dropTable('server_detail_to_server_type');

        $this->dropPrimaryKey('pk_server_detail', 'server_detail');
        $this->dropTable('server_detail');

        $this->dropForeignKey('fk_server_to_server_type_server_type_code', 'server_to_server_type');
        $this->dropForeignKey('fk_server_to_server_type_server_code', 'server_to_server_type');
        $this->dropPrimaryKey('pk_server_to_server_type', 'server_to_server_type');
        $this->dropTable('server_to_server_type');

        $this->dropPrimaryKey('pk_server_type', 'server_type');
        $this->dropTable('server_type');

        $this->dropTable('server');
    }

}
