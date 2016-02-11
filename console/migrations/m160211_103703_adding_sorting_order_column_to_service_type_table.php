<?php

use yii\db\Schema;
use yii\db\Migration;

class m160211_103703_adding_sorting_order_column_to_service_type_table extends Migration
{
    public function up()
    {
        $this->addColumn('service_type', 'order', Schema::TYPE_INTEGER . '(2) NOT NULL DEFAULT 0');
        $this->update('service_type', [
            'order' => '1',
            ], [
            'code' => 'CN',
        ]);
        $this->update('service_type', [
            'order' => '2',
        ], [
            'code' => 'CT',
        ]);
        $this->update('service_type', [
            'order' => '3',
        ], [
            'code' => 'AP',
        ]);
    }

    public function down()
    {
        $this->dropColumn('service_type', 'order');
    }

}
