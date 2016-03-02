<?php

use yii\db\Schema;
use yii\db\Migration;

class m160302_064933_adding_mailing_queue_table extends Migration
{
    public function up()
    {
        $this->createTable('mailing_queue', [
            'id' => $this->primaryKey(11),
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'planned_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'processed_date' => Schema::TYPE_DATETIME . ' NULL',
            'mailing' => Schema::TYPE_STRING . '(5) NOT NULL',
            'user' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'server' => Schema::TYPE_STRING . '(5) NOT NULL',
            'status' => Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0',
        ], "DEFAULT CHARSET=utf8");
        $this->addForeignKey('fk_mailing_queue_mailing_code', 'mailing_queue', 'mailing', 'mailing', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_mailing_queue_user_id', 'mailing_queue', 'user', 'user', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_mailing_queue_server_code', 'mailing_queue', 'server', 'server', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_mailing_queue_server_code', 'mailing_queue');
        $this->dropForeignKey('fk_mailing_queue_user_id', 'mailing_queue');
        $this->dropForeignKey('fk_mailing_queue_mailing_code', 'mailing_queue');

        $this->dropTable('mailing_queue');
    }
}
