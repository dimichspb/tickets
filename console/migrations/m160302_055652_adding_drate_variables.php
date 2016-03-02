<?php

use yii\db\Schema;
use yii\db\Migration;

class m160302_055652_adding_drate_variables extends Migration
{
    public function up()
    {
        $this->insert('variable', [
            'code' => 'subject',
            'name' => 'E-mail Subject',
        ]);
        $this->insert('variable_scope', [
            'variable' => 'subject',
            'mailing' => 'DRATE',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
                ['variable',    'variable_scope',   'language',     'value'], [
                ['subject',     $lastInsertId,      'ru',           'Первая рассылка'],
                ['subject',     $lastInsertId,      'en',           'First mailing'],
            ]);

        $this->insert('variable', [
            'code' => 'header',
            'name' => 'E-mail Header',
        ]);
        $this->insert('variable_scope', [
            'variable' => 'header',
            'mailing' => 'DRATE',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
                ['variable',    'variable_scope',   'language',     'value'], [
                ['header',      $lastInsertId,      'ru',           'Первая рассылка заголовок'],
                ['header',      $lastInsertId,      'en',           'First mailing header'],
            ]);

        $this->insert('variable', [
            'code' => 'body',
            'name' => 'E-mail Body',
        ]);
        $this->insert('variable_scope', [
            'variable' => 'body',
            'mailing' => 'DRATE',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
                ['variable',    'variable_scope',   'language',     'value'], [
                ['body',        $lastInsertId,      'ru',           'Первая рассылка тело'],
                ['body',        $lastInsertId,      'en',           'First mailing body'],
            ]);

        $this->insert('variable', [
            'code' => 'footer',
            'name' => 'E-mail Footer',
        ]);
        $this->insert('variable_scope', [
            'variable' => 'footer',
            'mailing' => 'DRATE',
        ]);
        $lastInsertId = $this->getDb()->getLastInsertID();
        $this->batchInsert('variable_value',
                ['variable',    'variable_scope',   'language',     'value'], [
                ['footer',      $lastInsertId,      'ru',           'Первая рассылка подвал'],
                ['footer',      $lastInsertId,      'en',           'First mailing footer'],
            ]);
    }

    public function down()
    {
        $this->delete('variable_value', [
            'variable' => 'footer',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'footer',
        ]);
        $this->delete('variable', [
            'code' => 'footer',
        ]);

        $this->delete('variable_value', [
            'variable' => 'body',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'body',
        ]);
        $this->delete('variable', [
            'code' => 'body',
        ]);

        $this->delete('variable_value', [
            'variable' => 'header',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'header',
        ]);
        $this->delete('variable', [
            'code' => 'header',
        ]);

        $this->delete('variable_value', [
            'variable' => 'subject',
        ]);
        $this->delete('variable_scope', [
            'variable' => 'subject',
        ]);
        $this->delete('variable', [
            'code' => 'subject',
        ]);
    }
}
