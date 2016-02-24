<?php

use yii\db\Schema;
use yii\db\Migration;

class m160224_120708_adding_service_column_to_rate_table extends Migration
{
    public function up()
    {
        $this->delete('rate');
        $this->addColumn('rate', 'service', Schema::TYPE_STRING . '(3) NOT NULL  AFTER `destination_city`');

        $this->addForeignKey('fk_rate_service_code', 'rate', 'service', 'service', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_rate_service_code', 'rate');
        $this->dropColumn('rate', 'service');

    }

}
