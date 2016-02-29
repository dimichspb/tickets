<?php

use yii\db\Schema;
use yii\db\Migration;

class m160229_135031_adding_common_server_details extends Migration
{
    public function up()
    {
        $this->batchInsert('server_detail', [
            'code', 'name'
        ], [
            ['HOST', 'Host address'],
            ['PORT', 'Port'],
            ['AUTH', 'Requires Authentication'],
            ['USER', 'Username'],
            ['PASS', 'Password'],
        ]);
    }

    public function down()
    {
        $this->delete('server_detail', [
            'code' => 'HOST',
        ]);
        $this->delete('server_detail', [
            'code' => 'PORT',
        ]);
        $this->delete('server_detail', [
            'code' => 'AUTH',
        ]);
        $this->delete('server_detail', [
            'code' => 'USER',
        ]);
        $this->delete('server_detail', [
            'code' => 'PASS',
        ]);
    }
}
