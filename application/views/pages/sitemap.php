<ol class="breadcrumb page-breadcrumb">
    <li><a href="<?= URL::base()?>">Интернет магазин автозапчастей</a>
    </li>
    <li class="active"><?=$h1?></li>
</ol>
<?php $tehdoc = Model::factory('NewTecdoc'); ?>

<h1><?=$h1?></h1>


<a href="https://ulc.com.ua">Главная</a><br />
<?php foreach ($categories as $category): ?>
    <?php if(in_array($category->id, [813, 862, 792, 666, 801, 866, 831, 678, 627]))
        continue; ?>
    <a href="https://ulc.com.ua/katalog/<?=$category->slug?>"><?=$category->name?></a><br />
<?php endforeach; ?>

<?php foreach ($manufacture as $manufactur):?>
    <a href="https://ulc.com.ua/katalog/<?=$manufactur['url']?>"><?="Запчасти на ".$manufactur['name']?></a><br />
    <?php foreach ($tehdoc->get_all_models_for_manufactures_url($manufactur['url']) as $model): ?>
        <a href="https://ulc.com.ua/katalog/<?=$manufactur['url'].'/'.$model['url_model']?>"><?="Запчасти на ".$manufactur['name']." ".$model['model']?></a><br />
    <?php endforeach; ?>
<?php endforeach; ?>

<!--<a href="https://eparts.kiev.ua/catalog/alfa-romeo">Запчасти на Alfa romeo</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/alfa-romeo/33">Запчасти на ALFA ROMEO 33</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/alfa-romeo/75">Запчасти на ALFA ROMEO 75</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/alfa-romeo/156">Запчасти на ALFA ROMEO 156</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/audi">Запчасти на Audi</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/audi/80">Запчасти на AUDI 80</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/audi/90">Запчасти на AUDI 90</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/audi/a6">Запчасти на AUDI A6</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/audi/a8">Запчасти на AUDI A8</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/audi/q7">Запчасти на AUDI Q7</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/bmw">Запчасти на BMW</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/bmw/3">Запчасти на BMW 3</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/bmw/5">Запчасти на BMW 5</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/bmw/x5">Запчасти на BMW X5</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/bmw/x6">Запчасти на BMW X6</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/chrysler">Запчасти на Chrysler</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/citroen">Запчасти на Citroen</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/citroen/jumper">Запчасти на CITROEN JUMPER</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/citroen/berlingo">Запчасти на CITROEN BERLINGO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/citroen/c4">Запчасти на CITROEN C4</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/daihatsu">Запчасти на Daihatsu</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/daihatsu/terios">Запчасти на DAIHATSU TERIOS</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/dodge">Запчасти на Dodge</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/dodge/neon">Запчасти на DODGE NEON</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/dodge/ram">Запчасти на DODGE RAM</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat">Запчасти на Fiat</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/regata">Запчасти на FIAT REGATA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/ducato">Запчасти на FIAT DUCATO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/tempra">Запчасти на FIAT TEMPRA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/tipo">Запчасти на FIAT TIPO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/punto">Запчасти на FIAT PUNTO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/uno">Запчасти на FIAT UNO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/bravo-i">Запчасти на FIAT BRAVO I</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/scudo">Запчасти на FIAT SCUDO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/doblo">Запчасти на FIAT DOBLO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/fiat/linea">Запчасти на FIAT LINEA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford">Запчасти на Ford</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/sierra">Запчасти на FORD SIERRA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/transit">Запчасти на FORD TRANSIT</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/fiesta">Запчасти на FORD FIESTA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/focus">Запчасти на FORD FOCUS</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/escort-classic">Запчасти на FORD ESCORT CLASSIC</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/fusion">Запчасти на FORD FUSION</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/focus-c-max">Запчасти на FORD FOCUS C-MAX</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/transit-tourneo">Запчасти на FORD TRANSIT TOURNEO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/kuga-i">Запчасти на FORD KUGA I</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/ford/courier">Запчасти на FORD COURIER</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/honda">Запчасти на Honda</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/honda/legend-i">Запчасти на HONDA LEGEND I</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/honda/prelude-i">Запчасти на HONDA PRELUDE I</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/honda/city">Запчасти на HONDA CITY</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/honda/jazz">Запчасти на HONDA JAZZ</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/honda/accord">Запчасти на HONDA ACCORD</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/honda/civic">Запчасти на HONDA CIVIC</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/jaguar">Запчасти на Jaguar</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/jeep">Запчасти на Jeep</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia">Запчасти на Kia</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/sephia">Запчасти на KIA SEPHIA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/sportage">Запчасти на KIA SPORTAGE</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/clarus">Запчасти на KIA CLARUS</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/besta">Запчасти на KIA BESTA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/rio">Запчасти на KIA RIO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/carens-i">Запчасти на KIA CARENS I</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/cerato">Запчасти на KIA CERATO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/cee-d">Запчасти на KIA CEE-D</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/soul">Запчасти на KIA SOUL</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/venga">Запчасти на KIA VENGA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/kia/optima">Запчасти на KIA OPTIMA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mazda">Запчасти на Mazda</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mazda/xedos-9">Запчасти на MAZDA XEDOS 9</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mazda/2">Запчасти на MAZDA 2</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mazda/3">Запчасти на MAZDA 3</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mazda/5">Запчасти на MAZDA 5</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mazda/6">Запчасти на MAZDA 6</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mercedes-benz">Запчасти на Mercedes</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mercedes-benz/vito">Запчасти на MERCEDES-BENZ VITO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mercedes-benz/sprinter">Запчасти на MERCEDES-BENZ SPRINTER</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mitsubishi">Запчасти на Mitsubishi</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mitsubishi/pajero-sport">Запчасти на MITSUBISHI PAJERO SPORT</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mitsubishi/lancer">Запчасти на MITSUBISHI LANCER</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/mitsubishi/galant">Запчасти на MITSUBISHI GALANT</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan">Запчасти на Nissan</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/micra-i">Запчасти на NISSAN MICRA I</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/sunny">Запчасти на NISSAN SUNNY</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/primera">Запчасти на NISSAN PRIMERA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/bluebird">Запчасти на NISSAN BLUEBIRD</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/pathfinder">Запчасти на NISSAN PATHFINDER</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/x-trail">Запчасти на NISSAN X-TRAIL</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/murano">Запчасти на NISSAN MURANO</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/navara">Запчасти на NISSAN NAVARA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/note">Запчасти на NISSAN NOTE</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/tiida">Запчасти на NISSAN TIIDA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/maxima">Запчасти на NISSAN MAXIMA</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/patrol">Запчасти на NISSAN PATROL</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/juke">Запчасти на NISSAN JUKE</a><br />-->
<!--<a href="https://eparts.kiev.ua/catalog/nissan/almera">Запчасти на NISSAN ALMERA</a><br />-->
<a href=""></a><br />








