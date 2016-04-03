<?php

use yii\db\Schema;
use yii\db\Migration;

class m160218_072624_adding_token_column_to_service_table extends Migration
{
    public function up()
    {
        $this->addColumn('service', 'token', Schema::TYPE_STRING);
        $this->update('service', [
            'token' => 'e7bb63bfda30e87207d881abd3044812',
        ], [
            'code' => 'AVS',
        ]);
    }

    public function down()
    {
        $this->dropColumn('service', 'token');
    }
}
