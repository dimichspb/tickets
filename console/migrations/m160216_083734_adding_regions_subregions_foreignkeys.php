<?php

use yii\db\Schema;
use yii\db\Migration;

class m160216_083734_adding_regions_subregions_foreignkeys extends Migration
{
    public function up()
    {
        $this->addForeignKey('fk_place_region_code', 'place', 'region', 'region', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_country_region_code', 'country', 'region', 'region', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_city_region_code', 'city', 'region', 'region', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_airport_region_code', 'airport', 'region', 'region', 'code', 'RESTRICT', 'RESTRICT');
        
        $this->addForeignKey('fk_place_subregion_code', 'place', 'subregion', 'subregion', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_country_subregion_code', 'country', 'subregion', 'subregion', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_city_subregion_code', 'city', 'subregion', 'subregion', 'code', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk_airport_subregion_code', 'airport', 'subregion', 'subregion', 'code', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk_airport_subregion_code', 'airport');
        $this->dropForeignKey('fk_city_subregion_code', 'city');
        $this->dropForeignKey('fk_country_subregion_code', 'country');
        $this->dropForeignKey('fk_place_subregion_code', 'place');
        
        $this->dropForeignKey('fk_airport_region_code', 'airport');
        $this->dropForeignKey('fk_city_region_code', 'city');
        $this->dropForeignKey('fk_country_region_code', 'country');
        $this->dropForeignKey('fk_place_region_code', 'place');
    }
}
