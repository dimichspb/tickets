<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

class m160807_123902_adding_last_update_column_to_route_table extends Migration
{
    public function up()
    {
        $this->addColumn('route', 'last_update', Schema::TYPE_DATETIME);
    }

    public function down()
    {
       $this->dropColumn('route', 'last_update');
    }

}
