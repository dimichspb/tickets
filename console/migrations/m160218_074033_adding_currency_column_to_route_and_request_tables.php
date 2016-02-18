<?php

use yii\db\Schema;
use yii\db\Migration;

class m160218_074033_adding_currency_column_to_route_and_request_tables extends Migration
{
    public function up()
    {
        $this->addColumn('request', 'currency', Schema::TYPE_STRING . '(3) NOT NULL DEFAULT "RUB"');
        $this->addColumn('route', 'currency', Schema::TYPE_STRING . '(3) NOT NULL DEFAULT "RUB"');
    }

    public function down()
    {
        $this->dropColumn('route', 'currency');
        $this->dropColumn('request', 'currency');
    }
}
