<?php

use yii\db\Schema;
use yii\db\Migration;

class m160303_083521_adding_mailing_queue_detail_table extends Migration
{
    public function up()
    {
        $this->createTable('mailing_queue_detail', [
            'mailing_queue' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'mailing_detail' => Schema::TYPE_STRING . '(8) NOT NULL',
            'value' => Schema::TYPE_TEXT . ' NULL',
        ], "DEFAULT CHARSET=utf8");
        $this->addPrimaryKey('pk_mailing_queue_detail', 'mailing_queue_detail', [
            'mailing_queue',
            'mailing_detail',
        ]);
        $this->addForeignKey('fk_mailing_queue_detail_mailing_queue_id', 'mailing_queue_detail', 'mailing_queue', 'mailing_queue', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_mailing_queue_detail_mailing_detail_code', 'mailing_queue_detail', 'mailing_detail', 'mailing_detail', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_mailing_queue_detail_mailing_detail_code', 'mailing_queue_detail');
        $this->dropForeignKey('fk_mailing_queue_detail_mailing_queue_id', 'mailing_queue_detail');
        $this->dropPrimaryKey('pk_mailing_queue_detail', 'mailing_queue_detail');

        $this->dropTable('mailing_queue_detail');
    }
}
