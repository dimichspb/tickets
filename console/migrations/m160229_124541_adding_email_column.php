<?php

use yii\db\Schema;
use yii\db\Migration;

class m160229_124541_adding_email_column extends Migration
{
    public function up()
    {
        $this->addColumn('request', 'email', Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 1');
    }

    public function down()
    {
        $this->dropColumn('request', 'email');
    }
}
