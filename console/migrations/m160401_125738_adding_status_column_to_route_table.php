<?php

use yii\db\Schema;
use yii\db\Migration;

class m160401_125738_adding_status_column_to_route_table extends Migration
{
    public function up()
    {
        $this->addColumn('route', 'status', Schema::TYPE_INTEGER . '(2) DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('route', 'status');
    }

}
