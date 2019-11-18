<?php $tree_list = ORM::factory('Category')->where('level', '=', 0)->order_by('id')->find_all()->as_array();
$data = array();
$column = 1;
$i = 0;

foreach ($tree_list as $ti) {
    ?>
    <li class="main-item">
        <?php
            $secondLevel = $ti->get_children();
            $flag2 = 0;
            $count2 = count($secondLevel);

            foreach ($secondLevel as $second)
            {
                $thirdLevel = $second->get_children();
                $flag3 = 0;
                $count3 = count($thirdLevel);
                foreach ($thirdLevel as $third)
                {
                    if(isset($active_categories) && !in_array($third->id, $active_categories))
                        $flag3++;
                }
                if($count3 == $flag3)
                {
                    $flag2++;
                    continue;
                }
            }

            if($count2 == $flag2)
                continue;
        ?>
        <a class="categories_left_manu" href="<?= Helper_Url::createUrl('katalog/' . $modification_url . $ti->slug) ?>">
            <img src="<?= URL::base(); ?>media/img/dist/icons/<?= $ti->image ?>" alt="picture"
                       class="dropdown-menu-category-icon"><p><?= $ti->name ?></p>
        </a>
        <div class="dropdown-menu-category-section">
            <div class="dropdown-menu-category-section-inner">
                <div class="dropdown-menu-category-section-content">
                    <div class="row">
                        <div class="col-md-4">
                            <?php foreach ($secondLevel as $ti2) { ?>
                                <?php if ($ti2->column AND $ti2->column != $column){
                                    $i = 0;
                                    $column = $ti2->column; ?>
                                    </div>
                                    <div class="col-md-4">
                                <?php } ?>
                                <p class="dropdown-menu-category-title"><?= $ti2->name ?></p>
                                <?php $ti3_list = $ti2->get_children();
                                if (!empty($ti3_list)) { ?>
                                    <ul class="dropdown-menu-category-list">
                                        <?php foreach ($ti3_list as $ti3) {
                                            $i++ ?>
                                            <li>
                                                <?php if(!isset($active_categories) OR in_array($ti3->id, $active_categories)): ?>
                                                    <a href="<?= Helper_Url::createUrl('katalog/' . $modification_url . $ti3->slug) ?>" <?=(Cookie::get('car_modification', NULL) ? 'rel="nofollow"' : '')?>><?= $ti3->name ?></a>
                                                <?php endif; ?>
                                            </li>
                                            <?php if ($i == $ti2->limit){
                                                $i = 0; ?>
                                                </ul>
                                                </div>
                                                <div class="col-md-4">
                                                <ul class="dropdown-menu-category-list">

                                            <?php } ?>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>

                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php if ($ti->image != 3) { ?>
                    <img class="dropdown-menu-category-section-theme-img" src="<?= URL::base(); ?>media/img/dist/<?= $ti->image ?>-1.png" alt="<?= $ti->name ?>" title="<?= $ti->name ?>" style="right: -10px;"/>
                <?php } ?>
            </div>
        </div>
    </li>

    <?php $column = 1;
    $i = 1;
} ?>
