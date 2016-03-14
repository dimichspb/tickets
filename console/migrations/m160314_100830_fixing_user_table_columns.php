<?php

use yii\db\Schema;
use yii\db\Migration;

class m160314_100830_fixing_user_table_columns extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'last_name', Schema::TYPE_STRING . ' NULL AFTER `first_name`');
        $this->dropColumn('user', 'username');
    }

    public function down()
    {
        $this->dropColumn('user', 'last_name');
        $this->addColumn('user', 'username', Schema::TYPE_STRING . ' NULL');
    }
}
