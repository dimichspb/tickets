<?php

use yii\db\Schema;
use yii\db\Migration;

class m160405_073207_adding_request_mailing_rate_table extends Migration
{
    public function up()
    {
        $this->createTable('request_mailing_rate',[
            'request' => $this->integer(11)->notNull(),
            'mailing_queue' => $this->integer(11)-> notNull(),
            'rate' => $this->integer(11)->notNull(),
        ]);

        $this->addForeignKey('fk_request_mailing_rate_request_id', 'request_mailing_rate', 'request', 'request', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_request_mailing_rate_mailing_id', 'request_mailing_rate', 'mailing_queue', 'mailing_queue', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_request_mailing_rate_rate_id', 'request_mailing_rate', 'rate', 'rate', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('request_mailing_rate');
    }
}
