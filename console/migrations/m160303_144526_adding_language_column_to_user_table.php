<?php

use yii\db\Schema;
use yii\db\Migration;

class m160303_144526_adding_language_column_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'language', Schema::TYPE_STRING . "(5) NOT NULL DEFAULT 'en'");
        $this->addForeignKey('fk_user_language_code', 'user', 'language', 'language', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_language_code', 'user');
        $this->dropColumn('user', 'language');
    }

}
