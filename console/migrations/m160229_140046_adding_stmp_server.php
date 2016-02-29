<?php

use yii\db\Schema;
use yii\db\Migration;

class m160229_140046_adding_stmp_server extends Migration
{
    public function up()
    {
        $this->insert('server_type', [
            'code' => 'SMTP',
            'name' => 'Simple Mail Transfer Protocol',
        ]);

        $this->batchInsert('server_detail_to_server_type',[
            'server_type', 'server_detail'
        ],[
            ['SMTP', 'HOST'],
            ['SMTP', 'PORT'],
            ['SMTP', 'AUTH'],
            ['SMTP', 'USER'],
            ['SMTP', 'PASS'],
        ]);
    }

    public function down()
    {
        $this->delete('server_detail_to_server_type', [
            'server_type' => 'SMTP',
        ]);

        $this->delete('server_type', [
            'code' => 'SMTP',
        ]);
    }
}
