<?php

use yii\db\Schema;
use yii\db\Migration;

class m160301_091716_adding_email_mailing extends Migration
{
    public function up()
    {
        $this->insert('mailing_type', [
            'code' => 'EMAIL',
            'name' => 'E-mail mailing',
        ]);

        $this->batchInsert('mailing_detail_to_mailing_type',[
            'mailing_type', 'mailing_detail'
        ],[
            ['EMAIL', 'FRMAIL'],
            ['EMAIL', 'TOMAIL'],
            ['EMAIL', 'FRNAME'],
            ['EMAIL', 'TONAME'],
            ['EMAIL', 'SUBJ'],
        ]);
    }

    public function down()
    {
        $this->delete('mailing_detail_to_mailing_type', [
            'mailing_type' => 'EMAIL',
        ]);

        $this->delete('mailing_type', [
            'code' => 'EMAIL',
        ]);
    }
}
