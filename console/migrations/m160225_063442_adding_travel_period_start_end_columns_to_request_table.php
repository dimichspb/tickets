<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\db\Expression;

class m160225_063442_adding_travel_period_start_end_columns_to_request_table extends Migration
{
    public function up()
    {
        $this->addColumn('request', 'travel_period_start', Schema::TYPE_INTEGER . '(3) NULL AFTER `there_end_date`');
        $this->addColumn('request', 'travel_period_end', Schema::TYPE_INTEGER . '(3) NULL AFTER `travel_period_start`');

        $this->update('request', [
            'travel_period_start' => new Expression('TIMESTAMPDIFF(DAY, `there_end_date`, `back_start_date`)'),
            'travel_period_end' => new Expression('TIMESTAMPDIFF(DAY, `there_start_date`, `back_end_date`)'),
        ]);

        $this->dropColumn('request', 'back_start_date');
        $this->dropColumn('request', 'back_end_date');
    }

    public function down()
    {
        $this->addColumn('request', 'back_start_date', Schema::TYPE_DATETIME . ' NULL AFTER `there_end_date');
        $this->addColumn('request', 'back_end_date', Schema::TYPE_DATETIME . ' NULL AFTER `back_start_date');

        $this->update('request', [
            'back_start_date' => new Expression('date_add(`there_end_date`, INTERVAL `travel_period_start` DAY)'),
            'back_end_date' => new Expression('date_add(`there_start_date`, INTERVAL `travel_period_end` DAY)'),
        ]);

        $this->dropColumn('request', 'travel_period_start');
        $this->dropColumn('request', 'travel_period_end');
    }
}
