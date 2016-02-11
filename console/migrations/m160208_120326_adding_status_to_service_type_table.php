<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_120326_adding_status_to_service_type_table extends Migration
{
    public function up()
    {
        $this->addColumn('service_type', 'status', Schema::TYPE_INTEGER . '(1) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('service_type', 'status');
    }

}
