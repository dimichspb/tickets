<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_093441_changing_countries_table extends Migration
{
    public function up()
    {
        $this->createIndex('country_code_idx', 'country', 'code', true);
        $this->addColumn('country', 'name', Schema::TYPE_STRING . ' NOT NULL');
        $this->addColumn('country', 'currency', Schema::TYPE_STRING . '(3) NOT NULL');
        $this->dropColumn('country', 'population');
        $this->dropColumn('country', 'country');
    }

    public function down()
    {
        $this->dropIndex('country_code_idx', 'country');
        $this->addColumn('country', 'country', Schema::TYPE_STRING . ' NOT NULL');
        $this->addColumn('country', 'population', Schema::TYPE_INTEGER . ' NOT NULL');
        $this->dropColumn('country', 'currency');
        $this->dropColumn('country', 'name');
    }

}
