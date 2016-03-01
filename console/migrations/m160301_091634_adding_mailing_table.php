<?php

use yii\db\Schema;
use yii\db\Migration;

class m160301_091634_adding_mailing_table extends Migration
{
    public function up()
    {
        $this->createTable('mailing', [
            'code' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_mailing', 'mailing', 'code');

        $this->createTable('mailing_type', [
            'code' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_mailing_type', 'mailing_type', 'code');

        $this->createTable('mailing_to_mailing_type', [
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'mailing' => Schema::TYPE_STRING . '(5) NOT NULL',
            'mailing_type' => Schema::TYPE_STRING . '(5) NOT NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_mailing_to_mailing_type', 'mailing_to_mailing_type',[
            'create_date',
            'mailing',
            'mailing_type',
        ]);
        $this->addForeignKey('fk_mailing_to_mailing_type_mailing_code', 'mailing_to_mailing_type', 'mailing', 'mailing', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_mailing_to_mailing_type_mailing_type_code', 'mailing_to_mailing_type', 'mailing_type', 'mailing_type', 'code', 'RESTRICT', 'RESTRICT');

        $this->createTable('mailing_detail', [
            'code' => Schema::TYPE_STRING . '(8) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_mailing_detail', 'mailing_detail', 'code');

        $this->createTable('mailing_detail_to_mailing_type', [
            'mailing_type' => Schema::TYPE_STRING . '(5) NOT NULL',
            'mailing_detail' => Schema::TYPE_STRING . '(8) NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_mailing_detail_to_mailing_type', 'mailing_detail_to_mailing_type', [
            'mailing_type',
            'mailing_detail',
        ]);
        $this->addForeignKey('fk_mailing_detail_to_mailing_type_mailing_type_code', 'mailing_detail_to_mailing_type', 'mailing_type', 'mailing_type', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_mailing_detail_to_mailing_type_mailing_detail_code', 'mailing_detail_to_mailing_type', 'mailing_detail', 'mailing_detail', 'code', 'RESTRICT', 'RESTRICT');

        $this->createTable('mailing_configuration', [
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'mailing' => Schema::TYPE_STRING . '(5) NOT NULL',
            'mailing_type' => Schema::TYPE_STRING . '(5) NOT NULL',
            'mailing_detail' => Schema::TYPE_STRING . '(8) NOT NULL',
            'value' => Schema::TYPE_STRING . ' NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_mailing_configuration', 'mailing_configuration', [
            'mailing',
            'mailing_type',
            'mailing_detail',
        ]);

        $this->addForeignKey('fk_mailing_configuration_mailing_code', 'mailing_configuration', 'mailing', 'mailing', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_mailing_configuration_mailing_type_code', 'mailing_configuration', 'mailing_type', 'mailing_type', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_mailing_configuration_mailing_detail_code', 'mailing_configuration', 'mailing_detail', 'mailing_detail', 'code', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropForeignKey('fk_mailing_configuration_mailing_detail_code', 'mailing_configuration');
        $this->dropForeignKey('fk_mailing_configuration_mailing_type_code', 'mailing_configuration');
        $this->dropForeignKey('fk_mailing_configuration_mailing_code', 'mailing_configuration');
        $this->dropPrimaryKey('pk_mailing_configuration', 'mailing_configuration');
        $this->dropTable('mailing_configuration');

        $this->dropForeignKey('fk_mailing_detail_to_mailing_type_mailing_detail_code', 'mailing_detail_to_mailing_type');
        $this->dropForeignKey('fk_mailing_detail_to_mailing_type_mailing_type_code', 'mailing_detail_to_mailing_type');
        $this->dropPrimaryKey('pk_mailing_detail_to_mailing_type', 'mailing_detail_to_mailing_type');
        $this->dropTable('mailing_detail_to_mailing_type');

        $this->dropPrimaryKey('pk_mailing_detail', 'mailing_detail');
        $this->dropTable('mailing_detail');

        $this->dropForeignKey('fk_mailing_to_mailing_type_mailing_type_code', 'mailing_to_mailing_type');
        $this->dropForeignKey('fk_mailing_to_mailing_type_mailing_code', 'mailing_to_mailing_type');
        $this->dropPrimaryKey('pk_mailing_to_mailing_type', 'mailing_to_mailing_type');
        $this->dropTable('mailing_to_mailing_type');

        $this->dropPrimaryKey('pk_mailing_type', 'mailing_type');
        $this->dropTable('mailing_type');

        $this->dropTable('mailing');
    }
}
