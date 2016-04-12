<?php

use yii\db\Migration;

class m160412_101430_adding_complex_index_to_route_table extends Migration
{
    public function up()
    {
        $this->createIndex('idx_route_origin_destination_there_back_date', 'route', [
            'origin_city',
            'destination_city',
            'there_date',
            'back_date',
        ], true);
    }

    public function down()
    {
        //$this->dropIndex('idx_route_origin_destination_there_back_date', 'route');
    }

}
