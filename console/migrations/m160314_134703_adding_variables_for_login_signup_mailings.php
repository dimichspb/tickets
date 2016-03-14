<?php

use yii\db\Schema;
use yii\db\Migration;

class m160314_134703_adding_variables_for_login_signup_mailings extends Migration
{
    public function up()
    {
        $this->insert('variable_scope', [
            'variable' => 'subject',
            'mailing' => 'LOGIN',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['subject',     $lastInsertId,      'ru',           'Авторизация на сайте TicketTracker.com'],
                ['subject',     $lastInsertId,      'en',           'TicketTracker.com authorization'],
            ]);
        $this->insert('variable_scope', [
            'variable' => 'subject',
            'mailing' => 'SGNUP',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['subject',     $lastInsertId,      'ru',           'Регистрация на сайте TicketTracker.com'],
                ['subject',     $lastInsertId,      'en',           'TicketTracker.com registration'],
            ]);


        $this->insert('variable_scope', [
            'variable' => 'header',
            'mailing' => 'LOGIN',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['header',      $lastInsertId,      'ru',           'Добро пожаловать'],
                ['header',      $lastInsertId,      'en',           'Welcome!'],
            ]);
        $this->insert('variable_scope', [
            'variable' => 'header',
            'mailing' => 'SGNUP',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['header',      $lastInsertId,      'ru',           'Добро пожаловать!'],
                ['header',      $lastInsertId,      'en',           'Welcome!'],
            ]);

        $this->insert('variable_scope', [
            'variable' => 'body',
            'mailing' => 'LOGIN',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['body',        $lastInsertId,      'ru',           'Пожалуйста, пройдите по следующей ссылке на том устройстве, которое хотите авторизовать {$link}'],
                ['body',        $lastInsertId,      'en',           'Please click the following link on the device you want to authorize {$link}'],
            ]);
        $this->insert('variable_scope', [
            'variable' => 'body',
            'mailing' => 'SGNUP',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['body',        $lastInsertId,      'ru',           'Сохраните это сообщение, чтобы всегда оставаться на связи {$link}'],
                ['body',        $lastInsertId,      'en',           'Please save this email to be always in touch {$link}'],
            ]);

        $this->insert('variable_scope', [
            'variable' => 'footer',
            'mailing' => 'LOGIN',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['footer',      $lastInsertId,      'ru',           'Спасибо!'],
                ['footer',      $lastInsertId,      'en',           'Thanks!'],
            ]);
        $this->insert('variable_scope', [
            'variable' => 'footer',
            'mailing' => 'SGNUP',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',    'variable_scope',   'language',     'value'], [
                ['footer',      $lastInsertId,      'ru',           'Спасибо!'],
                ['footer',      $lastInsertId,      'en',           'Thanks!'],
            ]);

        $this->insert('variable_scope', [
            'variable' => 'from_name',
            'mailing' => 'LOGIN',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',      'variable_scope',   'language',     'value'], [
                ['from_name',     $lastInsertId,      'ru',           'Тикет трэкер'],
                ['from_name',     $lastInsertId,      'en',           'Ticket tracker'],
            ]);
        $this->insert('variable_scope', [
            'variable' => 'from_name',
            'mailing' => 'SGNUP',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
            ['variable',      'variable_scope',   'language',     'value'], [
                ['from_name',     $lastInsertId,      'ru',           'Тикет трэкер'],
                ['from_name',     $lastInsertId,      'en',           'Ticket tracker'],
            ]);

    }

    public function down()
    {
        $this->delete('variable_value', [
            'variable' => 'from_name',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'from_name',
        ]);

        $this->delete('variable_value', [
            'variable' => 'footer',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'footer',
        ]);

        $this->delete('variable_value', [
            'variable' => 'body',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'body',
        ]);

        $this->delete('variable_value', [
            'variable' => 'header',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'header',
        ]);

        $this->delete('variable_value', [
            'variable' => 'subject',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'subject',
        ]);
    }
}
