<?php

use yii\db\Schema;
use yii\db\Migration;

class m160302_080222_adding_period_column_to_mailing_table extends Migration
{
    public function up()
    {
        $this->addColumn('mailing', 'processed_date', Schema::TYPE_DATETIME . ' NULL');
        $this->addColumn('mailing', 'process_period', Schema::TYPE_INTEGER . '(11) NOT NULL DEFAULT 60');
    }

    public function down()
    {
        $this->dropColumn('mailing', 'processed_date');
        $this->dropColumn('mailing', 'process_period');
    }

}
