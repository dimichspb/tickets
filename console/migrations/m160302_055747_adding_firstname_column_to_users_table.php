<?php

use yii\db\Schema;
use yii\db\Migration;

class m160302_055747_adding_firstname_column_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'first_name', Schema::TYPE_STRING . ' NULL AFTER `username`');
    }

    public function down()
    {
        $this->dropColumn('user', 'first_name');
    }

}
