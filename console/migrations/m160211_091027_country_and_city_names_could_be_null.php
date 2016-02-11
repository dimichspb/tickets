<?php

use yii\db\Schema;
use yii\db\Migration;

class m160211_091027_country_and_city_names_could_be_null extends Migration
{
    public function up()
    {
        $this->alterColumn('country', 'name', Schema::TYPE_STRING . ' NULL');
        $this->alterColumn('city', 'name', Schema::TYPE_STRING . ' NULL');


    }

    public function down()
    {
        $this->alterColumn('city', 'name', Schema::TYPE_STRING . ' NOT NULL');
        $this->alterColumn('country', 'name', Schema::TYPE_STRING . ' NOT NULL');
    }

}
