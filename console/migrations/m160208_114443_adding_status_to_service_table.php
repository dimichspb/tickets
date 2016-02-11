<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_114443_adding_status_to_service_table extends Migration
{
    public function up()
    {
        $this->addColumn('service', 'status', Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('service', 'status');
    }

}
