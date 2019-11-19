<?php
$tecdoc = Model::factory('Tecdoc');
$manufacturers = $tecdoc->get_manufacturers(false, false, false, true);
$parts = Model::factory('Category')->where('level', '=', 2)->and_where('parent_id', '<>', 596)->order_by('id')->find_all()->as_array();

$manufacturers_count = ceil(count($manufacturers) / 4);
$parts_count = ceil(count($parts) / 4);
?>
<script src="/media/js/jquery-2.2.1.min.js"></script>
<p class="header_choose_car_categories pro-h">Выберите автомобиль:</p>
<div class="row">
    <?php for ($column = 0; $column < 4; $column++) { ?>
        <div class="col-md-3">
            <ul class="list-unstyled">
                <?php foreach (array_slice($manufacturers, $column * $manufacturers_count, $manufacturers_count) as $manufacturer) { ?>
                    <li>
                        <a  class="cars" data-slug="<?= URL::site($manufacturer['slug']); ?>">
                            Автозапчасти для&nbsp <span class="dist"><?= $manufacturer['name'] ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div>

<div id="parts" class="row" style="display: none">
    <?php for ($column = 0; $column < 4; $column++) { ?>
        <div class="col-md-3">
            <ul class="list-unstyled">
                <?php foreach (array_slice($parts, $column * $parts_count, $parts_count) as $item) { ?>
                    <li>
                        <a style="color: #0a6aa1; text-transform: uppercase; font-weight: 600;" class="parts" href="#" data-slug="<?= $item->slug ?>"><?= $item->name ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.cars').on('click', function() {
            //remove styles
            $('.cars').each(function(){
                $(this).removeAttr('style');
            });
            $('#parts').hide();
            $(this).css('color','#FF0000');
            var car_href = $(this).attr('data-slug');
            $('.parts').each(function(){
                var part_href = $(this).attr('data-slug');
                $(this).attr('href', car_href + "/" + part_href);
            });
            $('#parts').toggle('display');

        });
    });
</script>
