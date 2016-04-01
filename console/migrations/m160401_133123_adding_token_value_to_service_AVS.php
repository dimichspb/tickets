<?php

use yii\db\Schema;
use yii\db\Migration;

class m160401_133123_adding_token_value_to_service_AVS extends Migration
{
    public function up()
    {
        $this->update('service', [
            'token' => 'e7bb63bfda30e87207d881abd3044812'
        ], [
            'code' => 'AVS',
        ]);
    }

    public function down()
    {

    }
}
