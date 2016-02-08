<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_095813_adding_country_desc_table extends Migration
{
    public function up()
    {
        $this->createTable('language', [
            'code' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_code', 'language', 'code');

        $this->createTable('country_desc', [
            'country' => Schema::TYPE_STRING . ' NOT NULL',
            'language' => Schema::TYPE_STRING . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fkey_country_desc_country_code', 'country_desc', 'country', 'country', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fkey_country_desc_language_code', 'country_desc', 'language', 'language', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fkey_country_desc_language_code', 'country_desc');
        $this->dropForeignKey('fkey_country_desc_country_code', 'country_desc');
        $this->dropTable('country_desc');
        $this->dropTable('language');
    }

}
