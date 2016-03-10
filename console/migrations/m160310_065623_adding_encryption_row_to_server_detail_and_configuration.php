<?php

use yii\db\Schema;
use yii\db\Migration;

class m160310_065623_adding_encryption_row_to_server_detail_and_configuration extends Migration
{
    public function up()
    {
        $this->insert('server_detail', [
            'code' => 'CRYP',
            'name' => 'Encryption',
        ]);

        $this->insert('server_detail_to_server_type', [
            'server_type' => 'SMTP',
            'server_detail' => 'CRYP',
        ]);

        $this->insert('server_configuration', [
            'server' => 'FSMTP',
            'server_type' => 'SMTP',
            'server_detail' => 'CRYP',
            'value' => 'ssl',
        ]);
    }

    public function down()
    {
        $this->delete('server_configuration', [
            'server' => 'FSMTP',
            'server_type' => 'SMTP',
            'server_detail' => 'CRYP',
        ]);

        $this->delete('server_detail_to_server_type', [
            'server_type' => 'SMTP',
            'server_detail' => 'CRYP',
        ]);

        $this->delete('server_detail', [
            'code' => 'CRYP',
        ]);
    }
}
