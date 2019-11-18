 <div class="edit_tecdoc_cat">
     <?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
        <?php for($i = 0; $i < count($data) - 1; ++$i): ?>
            <?php $level = $data[$i]['STR_LEVEL'];?>
                <span class="level-<?=$level?>">
<!--                    --><?//= Form::checkbox('category_tecdoc_id[]', $data[$i]['STR_ID' . $level], false, array('style' => 'margin-left:5px')); ?>
                    <?=$data[$i]['STR_TEXT' . $level]?>
                </span><br>
                <?php if($data[$i]['STR_ID'.$level] != next($data)['STR_ID'.$level]):?>
                    <?php
                    $results_sub_cat = Model::factory('NewTecdoc')->get_categories_group_by_cat($data[$i]['STR_ID' . $level]);
                    if($results_sub_cat)
                    {
                        foreach ($results_sub_cat AS $result_sub_cat):?>
                            <span class="level-<?=$level+1?>">
<!--                                --><?//= "Hello ".$result_sub_cat['group_id'] ?>
                                <?php if(in_array($result_sub_cat['group_id'], $array_td_id)): ?>
                                    <?= Form::checkbox('category_tecdoc_id[]', $result_sub_cat['group_id'], true, array('style' => 'margin-left:5px')); ?><?=$result_sub_cat['name']."(".$result_sub_cat['group_id'].")";?>
                                <?php else: ?>
                                    <?= Form::checkbox('category_tecdoc_id[]', $result_sub_cat['group_id'], false, array('style' => 'margin-left:5px')); ?><?=$result_sub_cat['name']?>
                                <?php endif;?>
                            </span><br>
                        <?php endforeach;
                    } ?>
                <?php endif; ?>
            <?php endfor; ?>
            <div class="control-group">
                <div class="controls">
                    <?= Form::submit('submit', 'Сохранить', array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
        <?= Form::close(); ?>
    </div>
</div>