<?php

use yii\db\Schema;
use yii\db\Migration;

class m160314_135830_adding_mailing_configuration_for_login_signup_mailings extends Migration
{
    public function up()
    {
        $this->batchInsert('mailing_configuration', [
            'mailing', 'mailing_type', 'mailing_detail', 'value', 'status'
        ], [
            ['LOGIN', 'EMAIL', 'FRMAIL', 'info@frontend.dev', 0],
            ['LOGIN', 'EMAIL', 'TOMAIL', '{user.email}', 0],
            ['LOGIN', 'EMAIL', 'FRNAME', '{from_name}', 0],
            ['LOGIN', 'EMAIL', 'TONAME', '{user.firstname}', 0],
            ['LOGIN', 'EMAIL', 'SUBJ', '{subject}', 0],
            ['LOGIN', 'EMAIL', 'BODY', '{header}{body}{footer}', 0],
        ]);
        $this->batchInsert('mailing_configuration', [
            'mailing', 'mailing_type', 'mailing_detail', 'value', 'status'
        ], [
            ['SGNUP', 'EMAIL', 'FRMAIL', 'info@frontend.dev', 0],
            ['SGNUP', 'EMAIL', 'TOMAIL', '{user.email}', 0],
            ['SGNUP', 'EMAIL', 'FRNAME', '{from_name}', 0],
            ['SGNUP', 'EMAIL', 'TONAME', '{user.firstname}', 0],
            ['SGNUP', 'EMAIL', 'SUBJ', '{subject}', 0],
            ['SGNUP', 'EMAIL', 'BODY', '{header}{body}{footer}', 0],
        ]);
    }

    public function down()
    {
        $this->delete('mailing_configuration', [
            'mailing' => 'LOGIN',
        ]);
        $this->delete('mailing_configuration', [
            'mailing' => 'SGNUP',
        ]);
    }

}
