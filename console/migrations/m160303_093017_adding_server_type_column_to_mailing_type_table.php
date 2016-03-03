<?php

use yii\db\Schema;
use yii\db\Migration;

class m160303_093017_adding_server_type_column_to_mailing_type_table extends Migration
{
    public function up()
    {
        $this->addColumn('mailing_type', 'server_type', Schema::TYPE_STRING . "(5) NOT NULL DEFAULT 'SMTP'");
        $this->addForeignKey('fk_mailing_type_server_type_code', 'mailing_type', 'server_type', 'server_type', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_mailing_type_server_type_code', 'mailing_type');
        $this->dropColumn('mailing_type', 'server_type');
    }

}
