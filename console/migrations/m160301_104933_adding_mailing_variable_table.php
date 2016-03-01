<?php

use yii\db\Schema;
use yii\db\Migration;

class m160301_104933_adding_mailing_variable_table extends Migration
{
    public function up()
    {
        $this->createTable('variable', [
            'code' => Schema::TYPE_STRING . '(32) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_variable', 'variable', 'code');

        $this->createTable('variable_value', [
            'variable' => Schema::TYPE_STRING . '(32) NOT NULL',
            'language' => Schema::TYPE_STRING . '(5) NULL',
            'value' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_variable_value', 'variable_value', [
            'variable',
            'language',
        ]);
        $this->addForeignKey('fk_variable_value_variable_code', 'variable_value', 'variable', 'variable', 'code', 'RESTRICT', 'RESTRICT');

        $this->createTable('variable_scope', [
            'variable' => Schema::TYPE_STRING . '(32) NOT NULL',
            'mailing' => Schema::TYPE_STRING . '(5) NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_variable_scope', 'variable_scope', [
            'variable',
            'mailing',
        ]);
        $this->addForeignKey('fk_variable_scope_variable_code', 'variable_scope', 'variable', 'variable', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_variable_scope_mailing_code', 'variable_scope', 'mailing', 'mailing', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_variable_scope_mailing_code', 'variable_scope');
        $this->dropForeignKey('fk_variable_scope_variable_code', 'variable_scope');
        $this->dropPrimaryKey('pk_variable_scope', 'variable_scope');
        $this->dropTable('variable_scope');

        $this->dropForeignKey('fk_variable_value_variable_code', 'variable_value');
        $this->dropPrimaryKey('pk_variable_value', 'variable_value');
        $this->dropTable('variable_value');

        $this->dropPrimaryKey('pk_variable', 'variable');
        $this->dropTable('variable');
    }

}
