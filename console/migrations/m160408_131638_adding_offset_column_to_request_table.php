<?php

use yii\db\Migration;

class m160408_131638_adding_offset_column_to_request_table extends Migration
{
    public function up()
    {
        $this->addColumn('request', 'route_offset', $this->integer(11)->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('request', 'route_offset');
    }
}
