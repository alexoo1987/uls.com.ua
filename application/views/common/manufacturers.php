<?php
$tecdoc = Model::factory('NewTecdoc');
$manufacturers = $tecdoc->get_all_manufacture(); ?>
<!--<div class="clearfix"></div>-->
<?php if (!$manufacturers) : ?>
    <p>Производители отсутствуют.</p>
<?php else : ?>
    <?php
    $in_column = ceil(count($manufacturers) / 3);
    $count = 0;
    for ($column = 0; $column < 3; $column++) : ?>

<!--        <div class="col-md-4">-->
            <ul class="no-styled-ul new_manuf_list catalog-list col-md-4">
                <?php foreach (array_slice($manufacturers, $column * $in_column, $in_column) as $manufacturer) : ?>
                    <li>
                        <a class="href_manufacture" href="<?= URL::site('katalog/' . $manufacturer['url']);  ?>" >
                            <span class="dist"><?= $manufacturer['name'] ?></span>
                        </a>
<!--                        <div class="models_by_cat">-->
<!--                            --><?php //foreach ($tecdoc->get_all_models_for_manufactures_url($manufacturer['url']) as $model ): ?>
<!--                            <a href="--><?//= URL::site('catalog/' . $manufacturer['url'].'/'.$model['url_model']); ?><!--"><span class="dist">--><?//= $model['model'] ?><!--</span></a>-->
<!--                            --><?php //endforeach; ?>
<!--                        </div>-->
                    </li>
                <?php endforeach; ?>
            </ul>
<!--        </div>-->
    <?php endfor; ?>
<?php endif;?>