<?php echo View::factory('common/car_select')->render(); ?>
<div class="container">
    <?php if(!$models) :?>
        <p>Модели отсутствуют.</p>
    <?php else:?>
        <h1 class="widget-title-lg"><?= $category_name; ?> на <?= $manufacture['short_name']?></h1>
        <ol class="breadcrumb page-breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
            <li typeof="v:Breadcrumb"><a href="<?= URL::base()?>" rel="v:url" property="v:title">Интернет магазин автозапчастей</a>
            </li>
            <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog')?>" rel="v:url" property="v:title">Каталог</a>
            </li>
            <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$manufacture['url'] ?>" rel="v:url" property="v:title"><?= $manufacture['short_name']?></a>
            </li>
            <?php if ($category->get_parent()) {?>
                <li typeof="v:Breadcrumb"><a href="<?= URL::site('katalog').'/'.$manufacture['url'].'/'.$category->get_parent()->slug?>" rel="v:url" property="v:title"><?= $category->get_parent()->name ?></a></li>
            <?php } ?>
            <li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title"><?= $category->name ?></span></li>
        </ol>

        <?php $count = count($models)/3;?>
        <div class="col-md-2 col-md-offset-2">
            <ul class="no-styled-ul new_manuf_list catalog-list">
                <?php foreach (array_slice($models, 0, ceil($count)) as $model):?>
                    <li>
                        <a href="<?=URL::site('katalog/'.$manufacture['url'].'/'.$model['url'].'/'.$slug);?>"><span class="dist"><?=$model['short_name']?></span></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-2">
            <ul class="no-styled-ul new_manuf_list catalog-list">
                <?php foreach (array_slice($models, ceil($count), $count) as $model):?>
                    <li>
                        <a href="<?=URL::site('katalog/'.$manufacture['url'].'/'.$model['url'].'/'.$slug);?>"><span class="dist"><?=$model['short_name']?></span></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-2">
            <ul class="no-styled-ul new_manuf_list catalog-list">
                <?php foreach (array_slice($models, $count+ceil($count), $count) as $model):?>
                    <li>
                        <a href="<?=URL::site('katalog/'.$manufacture['url'].'/'.$model['url'].'/'.$slug);?>"><span class="dist"><?=$model['short_name']?></span></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
<?php echo View::factory('common/categories_block')->render(); ?>

<div class="seo_text">
    <?= $seo_data->content ?>
    <div class="category-video" style="margin: 20px auto">
        <?= $category->video ?>
    </div>
</div>






