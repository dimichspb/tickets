<?php

use yii\db\Schema;
use yii\db\Migration;

class m160303_080855_adding_mailing_processed_column_to_request_table extends Migration
{
    public function up()
    {
        $this->addColumn('request', 'mailing_processed', Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 1');
    }

    public function down()
    {
        $this->dropColumn('request', 'mailing_processed');
    }
}
