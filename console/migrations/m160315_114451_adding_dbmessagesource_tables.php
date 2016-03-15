<?php

use yii\db\Schema;
use yii\db\Migration;

class m160315_114451_adding_dbmessagesource_tables extends Migration
{
    public function up()
    {
        $this->createTable('source_message', [
            'id' => $this->primaryKey(11),
            'category' => Schema::TYPE_STRING,
            'message' => Schema::TYPE_TEXT,
        ], "DEFAULT CHARSET=utf8");

        $this->createTable('message', [
            'id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'language' => Schema::TYPE_STRING . '(5) NOT NULL',
            'translation' => Schema::TYPE_TEXT,
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_message_id_language', 'message', [
            'id',
            'language',
        ]);
        $this->addForeignKey('fk_message_id_source_message_id', 'message', 'id', 'source_message', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_message_language_language_code', 'message', 'language', 'language', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_message_language_language_code', 'message');
        $this->dropForeignKey('fk_message_id_source_message_id', 'message');
        $this->dropTable('message');
        $this->dropTable('source_message');
    }

}
