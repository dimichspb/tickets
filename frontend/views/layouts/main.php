<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use common\models\Language;

AppAsset::register($this);



$this->registerJsFile('@web/js/jquery.interactive_bg.js', ['depends' => 'yii\web\JqueryAsset']);

$this->registerJs('$(".bg").interactive_bg({
    strength: 25,
    scale: 1.005,
    animationSpeed: "100ms",
    contain: true,
    wrapContent: false,
});');

$this->registerJs('
$(window).resize(function() {
      $(".bg > .ibg-bg").css({
        width: $(window).outerWidth() + 100,
        height: $(window).outerHeight() + 100,
        left: "-50px",
        top: "-50px"
      })
   })
');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap bg" data-ibg-bg="/images/bg.jpg">
    <?php
    NavBar::begin([
        'brandLabel' => '<div class="row"><div class="col-xs-4"><span class="bt-logo"></span></div><div class="col-xs-8"><span class="bt-brand">' . Yii::$app->name . '</span></div></div>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default navbar-fixed-top',
        ],
    ]);

    $menuItems = [
        ['label' => '<span class="glyphicon glyphicon-home"></span> '. Yii::t('app', 'Home'), 'url' => ['/site/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Signup'), 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-user"></span> ' . Yii::t('app', 'Login'), 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => '<span class=""></span> ' . Yii::t('app', 'My requests'), 'url' => ['request/index']];
        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-off"></span> ' . Yii::t('app', 'Logout'), 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']];
    }
    $menuItems[] = [
        'label' => '<span class="glyphicon glyphicon-globe"></span> ' . Language::defaultLabel(),
        'items' => Language::labelsList(),
    ];
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <?= Alert::widget() ?>
            </div>
        </div>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
