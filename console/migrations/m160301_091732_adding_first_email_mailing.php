<?php

use yii\db\Schema;
use yii\db\Migration;

class m160301_091732_adding_first_email_mailing extends Migration
{
    public function up()
    {
        $this->insert('mailing', [
            'code' => 'DRATE',
            'name' => 'Daily rates mailing',
            'status' => 0,
        ]);

        $this->insert('mailing_to_mailing_type', [
            'mailing' => 'DRATE',
            'mailing_type' => 'EMAIL',
        ]);

        $this->batchInsert('mailing_configuration', [
            'mailing', 'mailing_type', 'mailing_detail', 'value', 'status'
        ], [
            ['DRATE', 'EMAIL', 'FRMAIL', 'info@frontend.dev', 0],
            ['DRATE', 'EMAIL', 'TOMAIL', '{user.email}', 0],
            ['DRATE', 'EMAIL', 'FRNAME', '{from_name}', 0],
            ['DRATE', 'EMAIL', 'TONAME', '{user.firstname}', 0],
            ['DRATE', 'EMAIL', 'SUBJ', '{subject}', 0],
        ]);
    }

    public function down()
    {
        $this->delete('mailing_configuration', [
            'mailing' => 'DRATE',
        ]);

        $this->delete('mailing_to_mailing_type', [
            'mailing' => 'DRATE',
        ]);

        $this->delete('mailing', [
            'code' => 'DRATE',
        ]);
    }
}
