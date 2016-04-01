<?php

use yii\db\Schema;
use yii\db\Migration;

class m160401_135452_edit_variable_mailing_body extends Migration
{
    public function up()
    {
        $this->update('variable_value',[
            'value' => '
First mailing body

{foreach $rates as $index => $rate}
{$rate.origin_city} - {$rate.destination_city} - туда {$rate.there_date} - обратно {$rate.back_date} - {$rate.price} {$rate.currency}
{/foreach}
'
        ],[
            'variable' => 'body',
            'variable_scope' => '7',
            'language' => 'en',
        ]);

        $this->update('variable_value',[
            'value' => '
Первая рассылка тело

{foreach $rates as $index => $rate}
{$rate.origin_city} - {$rate.destination_city} - туда {$rate.there_date} - обратно {$rate.back_date} - {$rate.price} {$rate.currency}
{/foreach}
'
        ],[
            'variable' => 'body',
            'variable_scope' => '7',
            'language' => 'ru',
        ]);
    }

    public function down()
    {

    }

}
