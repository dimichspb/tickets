<?php

use yii\db\Schema;
use yii\db\Migration;

class m160210_143156_country_currency_canbe_NULL extends Migration
{
    public function up()
    {
        $this->alterColumn('country', 'currency', Schema::TYPE_STRING . ' NULL');
    }

    public function down()
    {
        $this->alterColumn('country', 'currency', Schema::TYPE_STRING . ' NOT NULL');
    }

}
