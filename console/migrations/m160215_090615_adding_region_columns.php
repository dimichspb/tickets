<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_090615_adding_region_columns extends Migration
{
    public function up()
    {
        $this->addColumn('place', 'region', Schema::TYPE_STRING . '(3) NULL AFTER `id`');
        $this->addColumn('country', 'region', Schema::TYPE_STRING . '(3) NULL AFTER `name`');
        $this->addColumn('city', 'region', Schema::TYPE_STRING . '(3) NULL AFTER `time_zone`');
        $this->addColumn('airport', 'region', Schema::TYPE_STRING . '(3) NULL AFTER `time_zone`');
    }

    public function down()
    {   
        $this->dropColumn('airport', 'region');
        $this->dropColumn('city', 'region');
        $this->dropColumn('country', 'region');
        $this->dropColumn('place', 'region');
    }
}
