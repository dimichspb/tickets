<?php

use yii\db\Schema;
use yii\db\Migration;

class m160301_091656_adding_common_mailing_details extends Migration
{
    public function up()
    {
        $this->batchInsert('mailing_detail', [
            'code', 'name'
        ], [
            ['FRMAIL', 'Send from email address'],
            ['TOMAIL', 'Send to email address'],
            ['FRNAME', 'Send from name'],
            ['TONAME', 'Send to name'],
            ['SUBJ', 'Subject'],
        ]);
    }

    public function down()
    {
        $this->delete('mailing_detail', [
            'code' => 'FRMAIL',
        ]);
        $this->delete('mailing_detail', [
            'code' => 'TOMAIL',
        ]);
        $this->delete('mailing_detail', [
            'code' => 'FRNAME',
        ]);
        $this->delete('mailing_detail', [
            'code' => 'TONAME',
        ]);
        $this->delete('mailing_detail', [
            'code' => 'SUBJ',
        ]);
    }
}
