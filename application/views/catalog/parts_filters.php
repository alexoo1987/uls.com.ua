<?php
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 05.06.17
 * Time: 18:42
 */
?>
<?php foreach ($filters as $filter):?>
<div class="checkbox">
    <label>
        <?php if(isset($brand_ids) AND in_array($filter['brand'],$brand_ids)): ?>
            <input type="checkbox" name="brand[]" id="checkbox1" value="<?= $filter['brand'] ?>" checked /><?= $filter['brand_long'] ?>
        <?php else: ?>
            <input type="checkbox" name="brand[]" id="checkbox1" value="<?= $filter['brand'] ?>" /><?= $filter['brand_long'] ?>
        <?php endif; ?>
    </label>
</div>
<?php endforeach; ?>
<a id="filter_add" class="btn btn-lg btn-primary" href="<?=$linkreal ?>" data-linkreal = "<?=$linkreal ?>" data-priceitem="">Подобрать</a>

<script>
    function updateTextArea() {
        preloader_more = $("#more_details_preloader");
        all_blocks = $(".parts_blocks");
        var allVals = [];
        $('#brands_filters :checked').each(function() {
            allVals.push($(this).val());
        });
        link = $('#filter_add');
        linkreal = link.data('linkreal') + '/filter-';

        if(allVals.length != 0)
        {
            link.attr("href", linkreal);
            for(var i = 0; i < allVals.length; i++)
            {
                if(i != allVals.length-1){
                    $('.'+allVals[i]+'').show();
                    var attr_now = link.attr('href');
                    var new_attr = attr_now + allVals[i]+'-';
                    link.attr("href", new_attr);
                }
                else{
                    $('.'+allVals[i]+'').show();
                    var attr_now = link.attr('href');
                    var new_attr = attr_now + allVals[i];
                    link.attr("href", new_attr);
                }


                console.log(allVals[i]);
            }
        }
        else{
            link.attr("href", link.data('linkreal'));
        }
    }
    $(function() {
        $('#brands_filters input').click(updateTextArea);
    });
</script>
