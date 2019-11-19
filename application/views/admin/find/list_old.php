<div class="container">
    <?= Form::open('', array('class' => 'form-horizontal price-level', 'method' => 'get')); ?>
    <? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
        <?= Form::hidden('art', Arr::get($_GET, 'art')); ?>
        <?= Form::hidden('brand', Arr::get($_GET, 'brand')); ?>
        Уровень цен <?= Form::select('discount_id', $discounts, $discount_id); ?><br />
        <?php if($discount_id) { ?>
            Показан уровень цен "<?=$discounts[$discount_id]?>" <!--a href="<?=URL::site('admin/find/index');?>?art=<?=Arr::get($_GET, 'art')?>&brand=<?=Arr::get($_GET, 'brand')?>">отмена</a-->
        <?php } ?>
    <?php } ?>

    <!--		--><?//= Form::submit('', 'Применить', array('class' => 'btn btn-primary')); ?>
    <?= Form::close(); ?>
    <?php
    if(count($parts) > 1) { ?>
        <table class="table table-striped table-bordered find-table">
            <tr>
                <th>Производитель</th>
                <th>Артикул</th>
                <th>Наименование</th>
                <th></th>
            </tr>
            <?php foreach($parts as $part) : ?>
                <?php
                //if($part->priceitems->count_all() == 0) continue;
                ?>
                <tr>
                    <td><?=$part->get_brand()?></td>
                    <td><?=$part->article_long?></td>
                    <td><?=Article::shorten_string($part->name, 3)?></td>
                    <td>
                        <a class="btn btn-mini" href="<?=URL::site('admin/find/index');?>?art=<?=$part->article?>&brand=<?=$part->brand?>&discount_id=<?=($_GET['discount_id'] ? $_GET['discount_id'] : $discount_id)?>">>>></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php } elseif(!$found) { ?>
        <p>По вашему запросу "<?=$_GET['art']?>" ничего не найдено</p>
    <?php } ?>

    <?php if(count($parts) <= 1): ?>
        <?php $hide_purchase = Cookie::get('hide_purchase', '0') == '1'; ?>
        <? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
            <p><input name="hide_purchase" id="hide_purchase" type="checkbox" data-url="<?=URL::site('admin/find/hide_purchase');?>" <?=$hide_purchase ? ' checked="checked"' : "" ?> />Скрыть входящую цену</p>
        <?php } ?>
        <?php foreach($groups as $group): ?>
            <?php if(count($group[1]) > 0): ?>
                <?=($group[0] ? "<span>".$group[0]."</span>" : "")?>
                <table class="table table-striped table-bordered find-table">
                    <tr>
                        <th>Производитель</th>
                        <th>Артикул</th>
                        <th>Наименование</th>
                        <? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
                            <th class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>">Цена закупки</th>
                        <?php } ?>
                        <? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
                            <th>Цена продажи</th>
                        <?php } ?>
                        <th>Кол-во</th>
                        <th>Ожидание (дней)</th>
                        <th>Примечания</th>
                        <? if(ORM::factory('Permission')->checkPermission('find_show_supplier')) { ?>
                            <th>Поставщик</th>
                        <?php } ?>
                        <th></th>
                        <th></th>
                    </tr>
                    <?php foreach($group[1] as $part) : ?>
                        <?php $flag = true; ?>
                        <?php $count = 1; ?>
                        <?php foreach($part['price_items'] as $price_item) : ?>
                            <?php if($price_item->amount < 0){continue;} ?>
                            <tr class="main-row <?=count($part['price_items']) == $count ? "last" : ""?><?=($price_item->delivery == 1 ? " green-row" : "")?><?=(!$flag ? " must-hide" : " non-hide")?>" <?=($flag ? 'data-main="1"' : '')?>>
                                <?php if($flag): ?>
                                    <td><?=$price_item->part->get_brand()?></td>
                                    <td><?=$price_item->part->article_long?></td>
                                    <td><?=Article::shorten_string($price_item->part->name, 3)?></td>
                                <?php else: ?>
                                    <td class="no-border"></td>
                                    <td class="no-border"></td>
                                    <td class="no-border"></td>
                                <?php endif; ?>
                                <? if(ORM::factory('Permission')->checkPermission('find_show_purchase')) { ?>
                                    <td class="purchase-column" style="<?=$hide_purchase ? "display: none;" : "" ?>"><?=$price_item->get_price()?> грн.
                                        <?=(isset($price_item->weight) ? "<br/> Вес: $price_item->weight кг": '')?>
                                    </td>
                                <?php } ?>
                                <? if(ORM::factory('Permission')->checkPermission('find_show_sale')) { ?>
                                    <td><?=$price_item->get_price_for_client(false, true, $discount_id)?> грн.
                                        <?=(isset($price_item->volume) AND $price_item->volume) ? "<span title='Возможна дополнительная плата за объем' style='cursor: pointer'><i class=\"icon-plane\"></i></span>": ''?>
                                    </td>
                                <?php } ?>
                                <td><?=$price_item->amount?> </td>
                                <td><?php
                                    if($price_item->delivery >= 5)
                                    {
                                        echo '<a href="#orderitem_log" role="button" class="popover_link" data-content="Приблизительное время ожидания" data-toggle="modal">~ '.$price_item->delivery.'</a>';
                                    }
                                    else{
                                        echo $price_item->delivery;
                                    }?>
                                </td>
                                <td>
                                    <?php if($price_item->supplier->id==38)
                                    {
                                        if($price_item->return_flag == 'Y')
                                        {
                                            echo "Существует возможность возврата";
                                        }
                                        else
                                        {
                                            echo "Нет возвратов";
                                        }
                                    }
                                    else
                                    {
                                        echo $price_item->supplier->notice;
                                    }?>
                                </td>
                                <? if(ORM::factory('Permission')->checkPermission('find_show_supplier')) { ?>
                                    <td>
                                    <? if(ORM::factory('Permission')->checkPermission('supplier_information')) { ?>
                                        <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content="<?=addcslashes($price_item->supplier->phone."<br>".$price_item->supplier->сomment_text, '"')?>"><?=$price_item->supplier->name?></a><br><?=$price_item->supplier->price->last_date ?></td>
                                    <?php }else{?>
                                        <a href="#" class="popover_link" rel="popover" data-placement="bottom" data-content=""><?=$price_item->supplier->name?></a><br><?=$price_item->supplier->price->last_date ?></td>
                                    <?php } }?>
                                <td>
                                    <?php if(!empty($price_item->part->tecdoc_id)) { ?>
                                        <a class="btn btn-mini" href="<?=URL::site('katalog/article/'.$price_item->part->id)?>" target="_blank"><i class="icon-info-sign"></i> Инфо</a>
                                    <?php } ?>
                                    <? if($price_item->id): ?>
                                        <a class="btn btn-mini" href="<?=URL::site('admin/orders/add_by_price_id?priceitem_id='.$price_item->id.$discount_str);?>"><i class="icon-shopping-cart"></i> Добавить в заказ</a>
                                    <? else: ?>
                                        ---
                                    <? endif; ?>
                                </td>
                                <td>
                                    <?php if ($flag and $price_item != end($part['price_items'])) { ?>
                                        <button class="btn btn-mini btn-primary show-more" type="button">Еще >>></button>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php $flag = false; ?>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </table>


                <!-- Modal -->
                <div id="orderitem_log" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3>Приблизительное время ожидания</h3>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>