<?php

use yii\db\Schema;
use yii\db\Migration;

class m160314_133150_adding_login_singup_mailings extends Migration
{
    public function up()
    {
        $this->insert('mailing', [
            'code' => 'LOGIN',
            'name' => 'Login mailing',
        ]);
        $this->insert('mailing_to_mailing_type', [
            'mailing' => 'LOGIN',
            'mailing_type' => 'EMAIL',
        ]);
        $this->insert('mailing', [
            'code' => 'SGNUP',
            'name' => 'Singup mailing',
        ]);
        $this->insert('mailing_to_mailing_type', [
            'mailing' => 'SGNUP',
            'mailing_type' => 'EMAIL',
        ]);
    }

    public function down()
    {
        $this->delete('mailing_to_mailing_type', [
            'mailing' => 'LOGIN',
        ]);
        $this->delete('mailing', [
            'code' => 'LOGIN',
        ]);
        $this->delete('mailing_to_mailing_type', [
            'mailing' => 'SGNUP',
        ]);
        $this->delete('mailing', [
            'code' => 'SGNUP',
        ]);
    }
}
