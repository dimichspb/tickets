<?php

use yii\db\Schema;
use yii\db\Migration;

class m160310_071431_adding_mailing_queue_log_table extends Migration
{
    public function up()
    {
        $this->createTable('mailing_queue_log', [
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL DEFAULT NOW()',
            'mailing_queue' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'message' => Schema::TYPE_TEXT . ' NULL',
        ], "DEFAULT CHARSET=utf8");

        $this->addForeignKey('fk_mailing_queue_log_mailing_queue_id', 'mailing_queue_log', 'mailing_queue', 'mailing_queue', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropForeignKey('fk_mailing_queue_log_mailing_queue_id', 'mailing_queue_log');
        $this->dropTable('mailing_queue_log');
    }

}
