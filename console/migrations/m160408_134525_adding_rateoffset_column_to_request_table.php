<?php

use yii\db\Migration;

class m160408_134525_adding_rateoffset_column_to_request_table extends Migration
{
    public function up()
    {
        $this->addColumn('request', 'rate_offset', $this->integer(11)->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('request', 'rate_offset');
    }

}
