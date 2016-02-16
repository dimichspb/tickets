<?php

use yii\db\Schema;
use yii\db\Migration;

class m160216_083653_adding_subregion_columns extends Migration
{
    public function up()
    {
        $this->addColumn('place', 'subregion', Schema::TYPE_STRING . '(3) NULL AFTER `region`');
        $this->addColumn('country', 'subregion', Schema::TYPE_STRING . '(3) NULL AFTER `region`');
        $this->addColumn('city', 'subregion', Schema::TYPE_STRING . '(3) NULL AFTER `region`');
        $this->addColumn('airport', 'subregion', Schema::TYPE_STRING . '(3) NULL AFTER `region`');
    }

    public function down()
    {   
        $this->dropColumn('airport', 'subregion');
        $this->dropColumn('city', 'subregion');
        $this->dropColumn('country', 'subregion');
        $this->dropColumn('place', 'subregion');
    }
}
