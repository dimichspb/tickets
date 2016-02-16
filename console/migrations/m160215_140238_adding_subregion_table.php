<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_140238_adding_subregion_table extends Migration
{
    public function up()
    {
        $this->createTable('subregion', [
            'code' => Schema::TYPE_STRING . '(3) NOT NULL',
            'region' => Schema::TYPE_STRING . '(3) NULL',
            'name' => Schema::TYPE_STRING . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        
        $this->addPrimaryKey('pk_subregion', 'subregion', 'code');
        $this->addForeignKey('fk_subregion_region_code', 'subregion', 'region', 'region', 'code', 'RESTRICT', 'RESTRICT');
        
        $this->createTable('subregion_desc', [
            'subregion' => Schema::TYPE_STRING . '(3) NOT NULL',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
        ], "DEFAULT CHARSET=utf8");
        
        $this->addPrimaryKey('pk_subregion_desc', 'subregion_desc', [
            'subregion',
            'language',
        ]);
        $this->addForeignKey('fk_subregion_desc_subregion_code', 'subregion_desc', 'subregion', 'subregion', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_subregion_desc_language_code', 'subregion_desc', 'language', 'language', 'code', 'RESTRICT', 'RESTRICT');
   
    }

    public function down()
    {
        $this->dropForeignKey('fk_subregion_desc_language_code', 'subregion_desc');
        $this->dropForeignKey('fk_subregion_desc_subregion_code', 'subregion_desc');
        $this->dropTable('subregion_desc');
        $this->dropForeignKey('fk_subregion_region_code', 'subregion');
        $this->dropTable('subregion');
    }
}
