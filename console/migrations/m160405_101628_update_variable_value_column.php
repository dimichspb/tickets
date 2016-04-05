<?php

use yii\db\Schema;
use yii\db\Migration;

class m160405_101628_update_variable_value_column extends Migration
{
    public function up()
    {
        $this->alterColumn('variable_value', 'value', $this->text());
    }

    public function down()
    {

    }

}
