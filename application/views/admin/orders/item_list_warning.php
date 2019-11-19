<script>
    var print_url = '<?=URL::site('admin/orders/print_items');?>';
    var sticker_print_url = '<?=URL::site('admin/orders/print_sticker_items');?>';
    var edit_state_url = '<?=URL::site('admin/orders/edit_item_state');?>';
    var save_state_url = '<?=URL::site('admin/orders/save_item_state');?>';
    var salary_ok_url = '<?=URL::site('admin/orders/salary_ok');?>';
    var move_items_url = '<?=URL::site('admin/orders/move_items');?>';
</script>
<div class="container">


    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>№</th>
            <th><input type="checkbox" id="select_all" /></th>
            <th><?="№ заказа"?></th>
            <th><?="Дата"?></th>
            <th>Клиент</th>
            <th style="width: 200px">Состояние</th>
            <th>Доп. данные</th>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_supplier')) { ?>
                <th>Поставщик</th>
            <?php } ?>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_purchase')) { ?>
                <th>Закупка</th>
            <?php } ?>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_sale')) { ?>
                <th>Продажа</th>
            <?php } ?>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_delivery')) { ?>
                <th>Доставка от поставщика</th>
            <?php } ?>
            <th>Ожидается</th>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
                <th><?="Менеджер"?></th>
            <?php } ?>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_salary')) { ?>
                <th>Зарплата</th>
            <?php } ?>
            <th>Баланс общ/заказ</th>
            <th>Архив</th>
            <th></th>
        </tr>
        </thead>

        <tbody>
        <?php $i = 0?>
        <?php foreach($order_items_warning as $orderitem) : ?>
            <?php if ($orderitem->state_id == 1 AND $is_buyer) continue;?>
            <?php
            $i++;
            $styles = "";
            if(!empty($orderitem->state->bg_color)) {
                $styles .= "background: ".$orderitem->state->bg_color."; ";
            }
            if(!empty($orderitem->state->bg_color)) {
                $styles .= "color: ".$orderitem->state->font_color."; ";
            }
            ?>
            <tr>
                <td style="<?=$styles?>"><?php ?></td>
                <td style="<?=$styles?>"><input type="checkbox" class="order_checkbox" data-id="<?=$orderitem->id?>" /></td>
                <td style="<?=$styles?>"><a href="<?=URL::site('admin/orders/items/'.$orderitem->order->id);?>"><?=$orderitem->order->get_order_number()?></a></td>
                <td style="<?=$styles?>"><?php $d = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
                <td style="<?=$styles?>"><?=$orderitem->order->client->name?> <?=$orderitem->order->client->surname?><br><?=$orderitem->order->client->phone?></td>
                <?php $var_id = Auth::instance()->get_user()->id; ?>
                <td style="min-width:140px; <?=$styles?>"<? if((ORM::factory('Permission')->checkPermission('orders_edit_state'))&&($orderitem->state_id !=14 AND $orderitem->state_id !=4 AND $orderitem->state_id !=15 and $orderitem->state_id !=17)) {
                    if($orderitem->salary == 1){
                        if($var_id==2 or $var_id==74){?>
                            class="editable_state" id="state_<?=$orderitem->id?>"
                        <?php }
                    } else{ ?>
                        class="editable_state" id="state_<?=$orderitem->id?>"
                    <?php }
                } ?>>
                    <?php if ($orderitem->logs->find_all()->as_array()) { $count = count(array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7)); $j=0;?>
                        <?php foreach (array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7) AS $one) {
                            $j++;
                            if ($j == 1)
                            {
                                echo '<p><img src="'.URL::base().'media/img/states/'.$one->state->img.'" title="'.$one->state->description.'" /> <span class="orderitem_state" title="'.$one->state->name.'">'. date('d.m.Y H:i', strtotime($one->date_time)).'</span></p>';?>

                            <?php }  } ?>
                        <a href="#orderitem_log<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>

                        <!-- Modal -->
                        <div id="orderitem_log<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3>История изменения статусов</h3>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <th>Состояние</th>
                                        <th>Время</th>
                                        <th>Пользователь</th>
                                        <th>Синхронизация с техномиром</th>
                                    </tr>
                                    <?php foreach ($array = $orderitem->logs->find_all()->as_array() AS $one) { ?>
                                        <tr <?=($one == end($array) ? 'class="success"' : '')?>>
                                            <td><img src="<?=URL::base()?>media/img/states/<?=$one->state->img?>" title="<?=$one->state->description?>" /> <?=$one->state->name?></td>
                                            <td><?=date('d.m.Y H:i:s', strtotime($one->date_time))?></td>
                                            <td><?=$one->user->surname?></td>
                                            <td><?= $one->tehnomir==1 ? "Да" : "Нет" ?></td>
                                        </tr>
                                    <?php } ?>

                                </table>
                            </div>
                        </div>
                        <!--END Modal -->
                    <?php } else { ?>
                        <img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
                    <?php } ?>


                    <?php if (($orderitem->confirmed == 0 AND in_array($orderitem->state->text_id, array('withdrawal', 'not_available')))
                        AND ((ORM::factory('Permission')->checkRole('manager') AND $orderitem->order->manager_id = Auth::instance()->get_user()->id)
                            OR (ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Руководитель отделения продаж') OR ORM::factory('Permission')->checkRole('Програмист') OR ORM::factory('Permission')->checkRole('Менеджер по Выдаче')))) { ?>
                        <div class="alert alert-block">
                            <h4>Внимание!</h4>
                            Статус позиции изменен на "<?=$orderitem->state->name?>"
                            <button class="btn btn-success confirm_orderitem" data-orderitem_id="<?=$orderitem->id?>" type="button">Ознакомлен</button>
                        </div>

                    <?php } ?>

                </td>
                <td style="<?=$styles?>">
                    <b>Артикул:</b> <?=$orderitem->article?> (<?php if($orderitem->amount > 1) :?><b style="font-weight: bold; background: white; color: red; "><?=$orderitem->amount?>шт</b><?php else :?><b><?=$orderitem->amount?>шт</b><?php endif;?>)<br />
                    <b>Производитель:</b> <?=$orderitem->brand?><br />
                    <b><?php $short_name = $orderitem->name; $words=explode(" ",$short_name); echo implode(" ",array_splice($words,0,2)) ?></b><br />
                    <b>Ожидание:</b> <?=$orderitem->delivery_days?>


                    <a href="#orderitem_more_info<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>

                    <!-- Modal -->
                    <div id="orderitem_more_info<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <!--							<h3>История изменения статусов</h3>-->
                        </div>
                        <div class="modal-body">
                            <b>Наименование:</b> <?=$orderitem->name?><br />
                            <b>Ожидание:</b> <?=$orderitem->delivery_days?><br />
                            <?php if($orderitem->amount > 1) :?>
                                <b style="font-weight: bold; background: white; color: red; ">Кол-во: <?=$orderitem->amount?><br /></b>
                            <?php else :?>
                                <b>Кол-во: <?=$orderitem->amount?><br /></b>
                            <?php endif;?>
                            <b>Онлайн заказ:</b><?=($orderitem->order->online == 1 ? "Да" : "Нет")?><br />
                            <?php if(!empty($orderitem->discount->id)) { ?><b>Уровень цен:</b> <?=$orderitem->discount->name?><br /><?php } ?>
                            <?=(!empty($orderitem->order->client->comment) ? "<b>Комментарий к клиенту:</b> ".$orderitem->order->client->comment."<br />" : "")?>
                            <b>Комментарий менеджера:</b> <?=$orderitem->manager_comment?><br />
                        </div>
                    </div>

                </td>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_supplier')) { ?>
                    <td style="<?=$styles?>">
                        <?=$orderitem->supplier->name?>
                        <br>----------<br>
                        <?=$orderitem->supplier->notice?>
                    </td>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_purchase')) { ?>
                    <td style="<?=$styles?>"><?=$orderitem->purchase_per_unit*$orderitem->amount?> грн.
                    <?php if($orderitem->purchase_per_unit_in_currency && $orderitem->currency->code != 'UAH'): ?>
                        <br><b>В валюте:</b><br>
                        <?=$orderitem->purchase_per_unit_in_currency*$orderitem->amount?> <?=$orderitem->currency->code?></td>
                    <?php endif; ?>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_sale')) { ?>
                    <td style="<?=$styles?>"><?=$orderitem->sale_per_unit*$orderitem->amount?> грн.</td>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_delivery')) { ?>
                    <td style="<?=$styles?>"><?=$orderitem->delivery_price?> грн.</td>
                <?php } ?>
                <td style="<?=$styles?>">
                    <?php
                    if ($orderitem->state_id == 2 OR $orderitem->state_id == 6 OR $orderitem->state_id == 8 OR $orderitem->state_id == 31) {
                        $order_date = $d;
                        $delivery_days = $orderitem->delivery_days;

                        if ($orderitem->supplier->order_to) {
                            $order_to = str_replace('.', ':', $orderitem->supplier->order_to);
                            if ($order_date->format('H:i') < date('H:i', strtotime($order_to))) {
                                $delivery_days--;
                            }
                        }

                        $order_date->modify('+' . $delivery_days . 'days');

                        echo '<b>Ожидается:</b> <span class="expected" data-date="' . $order_date->format('Y-m-d') . '">' . $order_date->format('d.m.Y') . '</span>';
                    }
                    ?>
                    <?php
                    if ($orderitem->state_id == 13)
                    {
                        $order_date_return_max = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time);
                        $order_date_return_normal = clone $order_date_return_max;
                        $order_date_return_normal->add(new DateInterval('P10D'));
                        $order_date_return_max->add(new DateInterval('P14D'));
                        echo '<b>Максимальный срок возврата:</b> <span class="expected_return" data-midle="' . $order_date_return_normal->format('Y-m-d') . '" data-max="' . $order_date_return_max->format('Y-m-d') . '">' . $order_date_return_max->format('d.m.Y') . '</span>';
//						echo "<br>".$order_date_return_normal->format('Y-m-d');
                    }

                    ?>

                </td>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
                    <?php if ($orderitem->order->manager->id != Auth::instance()->get_user()->id) $show_summary = false;  ?>
                    <td style="<?=$styles?>">
                        <?=$orderitem->order->manager->surname?>
                        <br><br>
                        <?php if (ORM::factory('Permission')->checkPermission('add_order_tm') AND isset($orderitem_tm_array)) : ?>
                            <?php if ($orderitem->supplier_id == 38) :?>
                                <?php
                                $d = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time);
                                $d->format('d.m.Y H:i:s');
                                $date1 = new DateTime("yesterday");
                                $date1->format('d.m.Y H:i:s');
                                ?>
                                <?php if ( !in_array ($orderitem->id, $orderitem_tm_array) AND ($orderitem->state_id == 16 OR $orderitem->state_id == 7)) : ?>
                                    <a role="button" data-idorder="<?=$orderitem->id?>" role="button" id="" class="btn btn-mini create_tm" data-toggle="modal">Оформить заказ позиции на техномире</a>
                                <?php endif; ?>
                            <?php endif;?>
                        <?php endif;?>
                    </td>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_salary')) { ?>
                    <td style="<?=$styles?>">
                        <?php
                        if($orderitem->salary == 1) echo "<strike>";
                        foreach($orderitem->salary_arr as $s) {
                            if($s['dont_show_salary'] == 1) continue;
                            echo "<b>".$s['name']."</b>: ".$s['value']." грн.<br />";
                        }
                        if($orderitem->salary == 1) echo "</strike>";
                        ?>
                    </td>
                <?php } ?>
                <td style="<?=$styles?>">
                </td>
                <td style="<?=$styles?>"><?=($orderitem->order->archive == 1 ? "Да" : "Нет")?></td>
                <td style="<?=$styles?>">
                    <?php if ((ORM::factory('Permission')->checkPermission('orders_show_manager')) AND (ORM::factory('Orderitem')->where('id', '=', $orderitem->id)->and_where('state_id', '=', 1)->count_all())) { ?>
                        <a title="Отправить в закупку" alt="Отправить в закупку" data-item_id="<?=$orderitem->id;?>" class="btn btn-mini to_purchase"><i class="icon-tasks"></i></a>
                    <?php } ?>
                    <a class="btn btn-mini" href="<?=URL::site('admin/orders/edit_item/'.$orderitem->id);?>"><i class="icon-edit"></i></a>
                    <? if(ORM::factory('Permission')->checkPermission('orders_delete_orderitem')) { ?>
                        <a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/orders/delete_item/'.$orderitem->id);?>"><i class="icon-remove"></i></a>
                    <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


</div>