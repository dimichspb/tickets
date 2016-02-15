<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_084025_adding_region_table extends Migration
{
    public function up()
    {
        $this->createTable('region', [
            'code' => Schema::TYPE_STRING . '(3) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        
        $this->addPrimaryKey('pk_region', 'region', 'code');
        
        $this->createTable('region_desc', [
            'region' => Schema::TYPE_STRING . '(3) NOT NULL',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        
        $this->addPrimaryKey('pk_region_desc', 'region_desc', [
            'region',
            'language',
        ]);
        $this->addForeignKey('fk_region_desc_region_code', 'region_desc', 'region', 'region', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_region_desc_language_code', 'region_desc', 'language', 'language', 'code', 'RESTRICT', 'RESTRICT');
   
    }

    public function down()
    {
        $this->dropForeignKey('fk_region_desc_language_code', 'region_desc');
        $this->dropForeignKey('fk_region_desc_region_code', 'region_desc');
        $this->dropTable('region_desc');
        $this->dropTable('region');
    }
}
