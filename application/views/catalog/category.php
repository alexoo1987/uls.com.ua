<?php echo View::factory('common/car_select')->render();
$i = 0; $column = 1;
?>

<div class="gap"></div>
<h1 class="widget-title-lg"><?=$content_catalog->h1?></h1>
<ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
    <li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
    </li>
    <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
    </li>
    <?php if(isset($car['manuf_name']) AND $car['manuf_name']): ?>
        <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog/'.$car['manuf_url'])?>" rel="v:url" property="v:title"><?=$car['manuf_name'] ?></a>
        </li>
    <?php endif; ?>

    <?php if(isset($car['model_name']) AND $car['model_name']): ?>
        <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog/'.$car['manuf_url'].'/'.$car['model_url'])?>" rel="v:url" property="v:title"><?=$car['model_name'] ?></a>
        </li>
    <?php endif; ?>

    <?php if(isset( $car['type_name']) AND $car['type_name']): ?>
        <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog/'.$car['manuf_url'].'/'.$car['model_url'].'/'.$car['type_url'])?>" rel="v:url" property="v:title"><?=$car['type_name'] ?></a>
        </li>
    <?php endif; ?>

    <?php if((isset($car['manuf_name']) AND $car['manuf_name']) AND (isset($car['model_name']) AND $car['model_name']) AND (!isset( $car['type_name']))) : ?>
        <li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$car['manuf_url'].'/'.$car['model_url'].'/'.$category->slug ?>" rel="v:url" property="v:title"><?= $category->name ?></a></li>
    <?php elseif ((isset($car['manuf_name']) AND $car['manuf_name']) AND (isset($car['model_name']) AND $car['model_name']) AND (isset( $car['type_name']) AND $car['type_name'])) : ?>
        <li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$car['manuf_url'].'/'.$car['model_url'].'/'.$car['type_url'].'/'.$category->slug ?>" rel="v:url" property="v:title"><?= $category->name ?></a></li>
    <?php else: ?>
        <li class="active" typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$category->slug ?>" rel="v:url" property="v:title"><?= $category->name ?></a></li>
    <?php endif; ?>




</ol>
<div class="gap-small"></div>
<div class="col-md-2">
    <a class="banner-category disabled" href="#" style="margin-bottom:10px;">
        <img class="banner-category-img" alt="<?= $h1 ?>" title="<?= $h1 ?>" src="<?= URL::base(); ?>media/img/dist/icons/<?= $category->image ?>">
        <span class="block banner-category-title"><?=$content_catalog->section_titles?$content_catalog->section_titles:$category->name?></span>
    </a>
</div>
<div class="row" data-gutter="15">
    <div class="col-md-9">
        <div class="product">
            <div class="dropdown-menu-category-section-inner">
                <div class="dropdown-menu-category-section-content" style="min-height: 500px;">
                    <div class="row">
                        <div class="col-md-6">
                            <?php foreach ($category->get_children() as $ti2) { ?>
                            <?php if ($ti2->column AND $ti2->column != $column){
                            $i = 0;
                            $column = $ti2->column; ?>
                        </div>
                        <div class="col-md-6">
                            <?php } ?>
                            <span class="block dropdown-menu-category-title"><?= $ti2->name ?></span>
                            <?php $ti3_list = $ti2->get_children();
                            if (!empty($ti3_list)) { ?>
                            <ul class="dropdown-menu-category-list">
                                <?php foreach ($ti3_list as $ti3) {
                                $i++ ?>
                                <li>
                                    <?php if (!isset($active_categories) OR in_array($ti3->id, $active_categories)) {?>
                                        <a href="<?= Helper_Url::createUrl('/katalog/' . $modification_url . $ti3->slug)?>" <?=(Cookie::get('car_modification', NULL) ? 'rel="nofollow"' : '')?>><?= $ti3->name ?></a>
                                    <?php }  ?>                                </li>
                                <?php if ($i == $ti2->limit){
                                $i = 0; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="dropdown-menu-category-list">

                                <?php } ?>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                </div>
                <?php if ($category->image != 3) { ?>
                    <img class="dropdown-menu-category-section-theme-img"
                         src="<?= URL::base(); ?>media/img/dist/<?= $category->image ?>-1.png" alt="<?= $h1 ?>"
                         title="<?= $h1 ?>" style="right: -10px;"/>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php if(isset($models)):?>
    <?php $count = count($models)/3;?>
    <div class="col-md-2 col-md-offset-2">
        <ul class="no-styled-ul new_manuf_list catalog-list">
            <?php foreach (array_slice($models, 0, ceil($count)) as $model):?>
                <li>
                    <a href="<?=URL::site('katalog/'.$manufacture['url'].'/'.$model['url'].'/'.$category->slug);?>"><span class="dist"><?=$model['short_name']?></span></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-2">
        <ul class="no-styled-ul new_manuf_list catalog-list">
            <?php foreach (array_slice($models, ceil($count), $count) as $model):?>
                <li>
                    <a href="<?=URL::site('katalog/'.$manufacture['url'].'/'.$model['url'].'/'.$category->slug);?>"><span class="dist"><?=$model['short_name']?></span></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-2">
        <ul class="no-styled-ul new_manuf_list catalog-list">
            <?php foreach (array_slice($models, $count+ceil($count), $count) as $model):?>
                <li>
                    <a href="<?=URL::site('katalog/'.$manufacture['url'].'/'.$model['url'].'/'.$category->slug);?>"><span class="dist"><?=$model['short_name']?></span></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <?php echo View::factory('common/manufacturers')->render(); ?>
<?php endif; ?>
<div class="col-md-12" style="margin-top: 5px">
    <?php echo $content_catalog->content; ?>
</div>

<div class="clearfix"></div>

<div class="seo_text"><!-- seo_text --><!-- /seo_text --></div>
