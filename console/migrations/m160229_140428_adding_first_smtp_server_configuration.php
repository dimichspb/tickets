<?php

use yii\db\Schema;
use yii\db\Migration;

class m160229_140428_adding_first_smtp_server_configuration extends Migration
{
    public function up()
    {
        $this->insert('server', [
            'code' => 'FSMTP',
            'name' => 'First SMTP server',
            'status' => 0,
        ]);

        $this->insert('server_to_server_type', [
            'server' => 'FSMTP',
            'server_type' => 'SMTP',
        ]);

        $this->batchInsert('server_configuration', [
            'server', 'server_type', 'server_detail', 'value', 'status'
        ], [
            ['FSMTP', 'SMTP', 'HOST', 'smtp.backend.dev', 0],
            ['FSMTP', 'SMTP', 'USER', 'username', 0],
            ['FSMTP', 'SMTP', 'PASS', 'password', 0],
            ['FSMTP', 'SMTP', 'AUTH', 'true', 0],
            ['FSMTP', 'SMTP', 'PORT', '25', 0],
        ]);
    }

    public function down()
    {
        $this->delete('server_configuration', [
            'server' => 'FSMTP',
        ]);

        $this->delete('server_to_server_type', [
            'server' => 'FSMTP',
        ]);

        $this->delete('server', [
            'code' => 'FSMTP',
        ]);
    }

}
