<?php

/* @var $this yii\web\View */

use kartik\widgets\Typeahead;
use yii\helpers\Url;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You are in one step to get best air fares!</p>

        <?php
        echo Typeahead::widget([
            'name' => 'country',
            'options' => ['placeholder' => 'Filter as you type ...'],
            'pluginOptions' => ['highlight'=>true],
            'dataset' => [
                [
                    'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                    'display' => 'id',
                    'remote' => [
                        'url' => '//api.frontend.dev/v1/places?access-token=Te5qwM9G9-HFsCf6vQpyOlTqrzVfjOMl&q=%QUERY',
                        'wildcard' => '%QUERY'
                    ]
                ]
            ]
        ]);
        ?>

        <p><a class="btn btn-lg btn-success" href="#">Request</a></p>
    </div>

    <div class="body-content">

    </div>
</div>
